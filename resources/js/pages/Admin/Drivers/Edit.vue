<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Driver } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save } from 'lucide-vue-next';
import { watch } from 'vue';

const props = defineProps<{
    page_setting: {
        title: string;
        subtitle: string;
        breadcrumbs: BreadcrumbItem[];
    };
    driver: Driver;
    fleets: any[];
}>();

const form = useForm({
    fleet_id: '',
    name: '',
    age: '',
    sim_expiry: '',
    sim_document: null as File | null,
    is_active: true,
});

watch(
    () => props.driver.data,
    (newDriver) => {
        if (newDriver) {
            form.fleet_id = String(newDriver.fleet_id);
            form.name = newDriver.name;
            form.age = String(newDriver.age);
            form.sim_expiry = newDriver.sim_expiry || '';
            form.is_active = newDriver.is_active;
        }
    },
    { immediate: true }
);

const submit = () => {
    form.transform((data) => ({
        ...data,
        _method: 'PUT',
    })).post(route('admin.drivers.update', { driver: props.driver.data.id }), {
        forceFormData: true,
        onSuccess: () => {
            router.visit(route('admin.drivers.index'));
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
                        <Link :href="route('admin.drivers.index')">
                            <ArrowLeft class="mr-2 h-4 w-4" />
                            Kembali
                        </Link>
                    </Button>
                    <div>
                        <CardTitle>Edit Supir</CardTitle>
                        <CardDescription>Perbarui informasi supir {{ driver.name }}</CardDescription>
                    </div>
                </div>
            </CardHeader>

            <CardContent>
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="fleet_id">Armada *</Label>
                            <Select
                                v-model="form.fleet_id"
                                :options="fleets.map((fleet) => ({ value: fleet.id, label: fleet.license_plate }))"
                                placeholder="Pilih armada"
                                :class="{ 'border-red-500': form.errors.fleet_id }"
                            />
                            <p v-if="form.errors.fleet_id" class="text-sm text-red-600">
                                {{ form.errors.fleet_id }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="name">Nama Supir *</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                placeholder="Contoh: Budi Santoso"
                                :class="{ 'border-red-500': form.errors.name }"
                                required
                            />
                            <p v-if="form.errors.name" class="text-sm text-red-600">
                                {{ form.errors.name }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-1">
                        <div class="space-y-2">
                            <Label for="age">Umur *</Label>
                            <Input
                                id="age"
                                v-model="form.age"
                                type="number"
                                placeholder="Contoh: 35"
                                :class="{ 'border-red-500': form.errors.age }"
                                required
                            />
                            <p v-if="form.errors.age" class="text-sm text-red-600">
                                {{ form.errors.age }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="sim_expiry">Tanggal Kadaluarsa SIM</Label>
                        <Input
                            id="sim_expiry"
                            v-model="form.sim_expiry"
                            type="date"
                            :class="{ 'border-red-500': form.errors.sim_expiry }"
                        />
                        <p v-if="form.errors.sim_expiry" class="text-sm text-red-600">
                            {{ form.errors.sim_expiry }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label for="sim_document">Dokumen SIM</Label>
                        <Input
                            id="sim_document"
                            type="file"
                            @input="form.sim_document = $event.target.files[0]"
                            accept=".pdf,.jpg,.jpeg,.png"
                            :class="{ 'border-red-500': form.errors.sim_document }"
                        />
                        <div v-if="driver.sim_document_url" class="mt-1">
                            <a :href="driver.sim_document_url" target="_blank" class="text-sm text-blue-600 hover:underline">
                                Lihat dokumen saat ini
                            </a>
                        </div>
                        <p v-if="form.errors.sim_document" class="text-sm text-red-600">
                            {{ form.errors.sim_document }}
                        </p>
                    </div>

                    <div class="flex items-center space-x-2">
                        <Switch id="is_active" v-model:checked="form.is_active" />
                        <Label for="is_active">Aktif</Label>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <Button type="button" variant="outline" as-child>
                            <Link :href="route('admin.drivers.index')">Batal</Link>
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
