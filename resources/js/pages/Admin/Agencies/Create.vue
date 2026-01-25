<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Area, BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { ArrowLeft, Save } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps<{
    page_setting: {
        title: string;
        subtitle: string;
        breadcrumbs: BreadcrumbItem[];
    };
    areas: Area[];
}>();

const regions = ref<{ id: number; city: string; region_sbm: string }[]>([]);

const form = useForm({
    area_id: '',
    region_id: '',
    name: '',
    address: '',
    cylinder_count: 0,
    daily_allocation: 0,
    is_active: true,
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
    () => form.area_id,
    (newValue) => {
        form.region_id = '';
        fetchRegions(String(newValue));
    }
);

const submit = () => {
    form.post(route('admin.agencies.store'), {
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
                <div class="flex items-center gap-4 justify-between">
                    <div>
                        <CardTitle>Tambah Agen Baru</CardTitle>
                        <CardDescription>Tambahkan agen baru untuk sistem SMART AGEN LPG</CardDescription>
                    </div>
                    <Button variant="outline" size="sm" as-child>
                        <Link :href="route('admin.agencies.index')">
                            <ArrowLeft class="mr-2 h-4 w-4" />
                            Kembali
                        </Link>
                    </Button>
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
                            <Label for="region_id">Kabupaten/Kota (Wilayah SBM) *</Label>
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

                        <div class="space-y-2 col-span-2">
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

                    <!-- <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="regency">Kabupaten/Kota *</Label>
                            <Input
                                id="regency"
                                v-model="form.regency"
                                placeholder="Contoh: Palembang"
                                :class="{ 'border-red-500': form.errors.regency }"
                                required
                            />
                            <p v-if="form.errors.regency" class="text-sm text-red-600">
                                {{ form.errors.regency }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="province">Provinsi *</Label>
                            <Input
                                id="province"
                                v-model="form.province"
                                placeholder="Contoh: Sumatera Selatan"
                                :class="{ 'border-red-500': form.errors.province }"
                                required
                            />
                            <p v-if="form.errors.province" class="text-sm text-red-600">
                                {{ form.errors.province }}
                            </p>
                        </div>
                    </div> -->

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
                            Simpan
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </AppLayout>
</template>
