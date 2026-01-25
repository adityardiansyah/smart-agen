<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Agency, Area, BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import axios from 'axios';

const props = defineProps<{
    page_setting: {
        title: string;
        subtitle: string;
        breadcrumbs: BreadcrumbItem[];
    };
    agency: Agency;
    areas: Area[];
}>();

const regions = ref<{ id: number; city: string; region_sbm: string }[]>([]);

const form = useForm({
    area_id: String(props.agency.area_id),
    region_id: String(props.agency.region_id),
    name: props.agency.name,
    address: props.agency.address,
    cylinder_count: props.agency.cylinder_count || 0,
    daily_allocation: props.agency.daily_allocation || 0,
    is_active: props.agency.is_active,
});

const fetchRegions = async (areaId: string) => {
    if (!areaId) {
        regions.value = [];
        return;
    }
    try {
        const response = await axios.get(route('admin.agencies.get-regions'), {
            params: { area_id: areaId },
        });
        regions.value = response.data;
    } catch (error) {
        console.error('Failed to fetch regions:', error);
    }
};

watch(
    () => props.agency?.data,
    (newAgency) => {
        if (newAgency) {
            form.area_id = String(newAgency.area_id);
            // Fetch regions before setting region_id to ensure option exists (optional but good for UI)
            fetchRegions(form.area_id);
            form.region_id = newAgency.region_id ? String(newAgency.region_id) : '';
            
            form.name = newAgency.name;
            form.address = newAgency.address;
            form.cylinder_count = newAgency.cylinder_count || 0;
            form.daily_allocation = newAgency.daily_allocation || 0;
            form.is_active = newAgency.is_active;
        }
    },
    { immediate: true }
);

watch(
    () => form.area_id,
    (newValue, oldValue) => {
        if (newValue !== oldValue && oldValue !== '') {
            form.region_id = '';
            fetchRegions(String(newValue));
        }
    }
);

const submit = () => {
    form.put(route('admin.agencies.update', { agency: props.agency?.data?.id }), {
        onSuccess: () => {
            router.visit(route('admin.agencies.index'));
        },
    });
};
</script>

<template>
    <Head :title="page_setting.title" />

    <AppLayout :page-setting="page_setting">
        <Card class="mx-auto max-w-3xl">
            <CardHeader>
                <div class="flex items-center gap-4">
                    <Button variant="outline" size="sm" as-child>
                        <Link :href="route('admin.agencies.index')">
                            <ArrowLeft class="mr-2 h-4 w-4" />
                            Kembali
                        </Link>
                    </Button>
                    <div>
                        <CardTitle>Edit Agen</CardTitle>
                        <CardDescription>Perbarui informasi agen {{ agency.name }}</CardDescription>
                    </div>
                </div>
            </CardHeader>

            <CardContent>
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="area_id">Area *</Label>
                            <Select
                                v-model="form.area_id"
                                :options="areas.map((area) => ({ value: area.id, label: area.name }))"
                                placeholder="Pilih area"
                                :class="{ 'border-red-500': form.errors.area_id }"
                            />
                            <p v-if="form.errors.area_id" class="text-sm text-red-600">
                                {{ form.errors.area_id }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="region_id">Region (Kabupaten/Kota) *</Label>
                            <Select
                                v-model="form.region_id"
                                :options="regions.map((region) => ({ value: region.id, label: `${region.city} (${region.region_sbm})` }))"
                                placeholder="Pilih region"
                                :class="{ 'border-red-500': form.errors.region_id }"
                                :disabled="!form.area_id || regions.length === 0"
                            />
                            <p v-if="form.errors.region_id" class="text-sm text-red-600">
                                {{ form.errors.region_id }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="name">Nama Agen *</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                placeholder="Contoh: PT. ABC LPG"
                                :class="{ 'border-red-500': form.errors.name }"
                                required
                            />
                            <p v-if="form.errors.name" class="text-sm text-red-600">
                                {{ form.errors.name }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="address">Alamat *</Label>
                        <Input
                            id="address"
                            v-model="form.address"
                            placeholder="Jalan, nomor, komplek"
                            :class="{ 'border-red-500': form.errors.address }"
                            required
                        />
                        <p v-if="form.errors.address" class="text-sm text-red-600">
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="cylinder_count">Jumlah Tabung</Label>
                            <Input
                                id="cylinder_count"
                                v-model="form.cylinder_count"
                                type="number"
                                min="0"
                                placeholder="0"
                                :class="{ 'border-red-500': form.errors.cylinder_count }"
                            />
                            <p v-if="form.errors.cylinder_count" class="text-sm text-red-600">
                                {{ form.errors.cylinder_count }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="daily_allocation">Alokasi Harian</Label>
                            <Input
                                id="daily_allocation"
                                v-model="form.daily_allocation"
                                type="number"
                                min="0"
                                placeholder="0"
                                :class="{ 'border-red-500': form.errors.daily_allocation }"
                            />
                            <p v-if="form.errors.daily_allocation" class="text-sm text-red-600">
                                {{ form.errors.daily_allocation }}
                            </p>
                        </div>
                    </div>





                    <div class="flex items-center space-x-2">
                        <Switch id="is_active" v-model:checked="form.is_active" />
                        <Label for="is_active">Aktif</Label>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <Button type="button" variant="outline" as-child>
                            <Link :href="route('admin.agencies.index')">Batal</Link>
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                            <Save class="mr-2 h-4 w-4" />
                            Perbarui
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </AppLayout>
</template>
