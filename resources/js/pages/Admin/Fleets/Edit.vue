<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Area, BreadcrumbItem, Fleet } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save } from 'lucide-vue-next';
import { watch } from 'vue';

const props = defineProps<{
    page_setting: {
        title: string;
        subtitle: string;
        breadcrumbs: BreadcrumbItem[];
    };
    fleet: Fleet;
    agencies: any[];
}>();

const form = useForm({
    agency_id: '',
    license_plate: '',
    year_manufacture: '',
    keur_number: '',
    keur_expiry: '',
    keur_document: null as File | null,
    stnk_expiry: '',
    stnk_document: null as File | null,
    vehicle_expiry: '',
    is_active: true,
});

watch(
    () => props.fleet.data,
    (newFleet) => {
        if (newFleet) {
            form.agency_id = String(newFleet.agency_id);
            form.license_plate = newFleet.license_plate;
            form.year_manufacture = String(newFleet.year_manufacture);
            form.keur_number = newFleet.keur_number || '';
            form.keur_expiry = newFleet.keur_expiry || '';
            form.stnk_expiry = newFleet.stnk_expiry || '';
            form.vehicle_expiry = newFleet.vehicle_expiry || '';
            form.is_active = newFleet.is_active;
        }
    },
    { immediate: true }
);

const submit = () => {
    form.transform((data) => ({
        ...data,
        _method: 'PUT',
    })).post(route('admin.fleets.update', { fleet: props.fleet.data.id }), {
        forceFormData: true,
        onSuccess: () => {
            router.visit(route('admin.fleets.index'));
        },
    });
};
</script>

<template>
    <Head :title="page_setting.title" />

    <AppLayout :page-setting="page_setting">
        <Card class="mx-auto max-w-3xl my-6">
            <CardHeader>
                <div class="flex items-center gap-4 justify-between">
                    <div>
                        <CardTitle>Edit Armada</CardTitle>
                        <CardDescription>Perbarui informasi armada {{ fleet.data.license_plate }}</CardDescription>
                    </div>
                    <Button variant="outline" size="sm" as-child>
                        <Link :href="route('admin.fleets.index')">
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
                            <Label for="agency_id">Agen *</Label>
                            <Select
                                v-model="form.agency_id"
                                :options="agencies.map((agency) => ({ value: agency.id, label: agency.name }))"
                                placeholder="Pilih agen"
                                :class="{ 'border-red-500': form.errors.agency_id }"
                            />
                            <p v-if="form.errors.agency_id" class="text-sm text-red-600">
                                {{ form.errors.agency_id }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="license_plate">Nomor Polisi *</Label>
                            <Input
                                id="license_plate"
                                v-model="form.license_plate"
                                placeholder="Contoh: AB 1234 CD"
                                :class="{ 'border-red-500': form.errors.license_plate }"
                                required
                            />
                            <p v-if="form.errors.license_plate" class="text-sm text-red-600">
                                {{ form.errors.license_plate }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="year_manufacture">Tahun Pembuatan *</Label>
                            <Input
                                id="year_manufacture"
                                v-model="form.year_manufacture"
                                type="number"
                                placeholder="Contoh: 2020"
                                :class="{ 'border-red-500': form.errors.year_manufacture }"
                                required
                            />
                            <p v-if="form.errors.year_manufacture" class="text-sm text-red-600">
                                {{ form.errors.year_manufacture }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="keur_number">Nomor KEUR</Label>
                            <Input
                                id="keur_number"
                                v-model="form.keur_number"
                                placeholder="Nomor KEUR"
                                :class="{ 'border-red-500': form.errors.keur_number }"
                            />
                            <p v-if="form.errors.keur_number" class="text-sm text-red-600">
                                {{ form.errors.keur_number }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="keur_expiry">Tanggal Kadaluarsa KEUR</Label>
                            <Input
                                id="keur_expiry"
                                v-model="form.keur_expiry"
                                type="date"
                                :class="{ 'border-red-500': form.errors.keur_expiry }"
                            />
                            <p v-if="form.errors.keur_expiry" class="text-sm text-red-600">
                                {{ form.errors.keur_expiry }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="keur_document">Dokumen KEUR</Label>
                            <Input
                                id="keur_document"
                                type="file"
                                @input="form.keur_document = $event.target.files[0]"
                                accept=".pdf,.jpg,.jpeg,.png"
                                :class="{ 'border-red-500': form.errors.keur_document }"
                            />
                            <div v-if="fleet.data.keur_document_url" class="mt-1">
                                <a :href="fleet.data.keur_document_url" target="_blank" class="text-sm text-blue-600 hover:underline">
                                    Lihat dokumen saat ini
                                </a>
                            </div>
                            <p v-if="form.errors.keur_document" class="text-sm text-red-600">
                                {{ form.errors.keur_document }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="stnk_expiry">Tanggal Kadaluarsa STNK</Label>
                            <Input
                                id="stnk_expiry"
                                v-model="form.stnk_expiry"
                                type="date"
                                :class="{ 'border-red-500': form.errors.stnk_expiry }"
                            />
                            <p v-if="form.errors.stnk_expiry" class="text-sm text-red-600">
                                {{ form.errors.stnk_expiry }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="stnk_document">Dokumen STNK</Label>
                            <Input
                                id="stnk_document"
                                type="file"
                                @input="form.stnk_document = $event.target.files[0]"
                                accept=".pdf,.jpg,.jpeg,.png"
                                :class="{ 'border-red-500': form.errors.stnk_document }"
                            />
                            <div v-if="fleet.data.stnk_document_url" class="mt-1">
                                <a :href="fleet.data.stnk_document_url" target="_blank" class="text-sm text-blue-600 hover:underline">
                                    Lihat dokumen saat ini
                                </a>
                            </div>
                            <p v-if="form.errors.stnk_document" class="text-sm text-red-600">
                                {{ form.errors.stnk_document }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-1">
                        <div class="space-y-2">
                            <Label for="vehicle_expiry">Tanggal Kadaluarsa Kendaraan</Label>
                            <Input
                                id="vehicle_expiry"
                                v-model="form.vehicle_expiry"
                                type="date"
                                :class="{ 'border-red-500': form.errors.vehicle_expiry }"
                            />
                            <p v-if="form.errors.vehicle_expiry" class="text-sm text-red-600">
                                {{ form.errors.vehicle_expiry }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2">
                        <Switch id="is_active" v-model:checked="form.is_active" />
                        <Label for="is_active">Aktif</Label>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <Button type="button" variant="outline" as-child>
                            <Link :href="route('admin.fleets.index')">Batal</Link>
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
