<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Select } from '@/components/ui/select';
import { can } from '@/composables/usePermission';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Area, BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Building2, Package, TrendingUp, Truck, Users } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { Eye } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/vue3';

const props = defineProps<{
    page_setting: {
        title: string;
        subtitle: string;
        breadcrumbs: BreadcrumbItem[];
    };
    areas: any[];
    selectedAreaId: number | null;
    metrics: {
        area?: Area | null;
        metrics?: {
            agency_count: number;
            fleet_count: number;
            driver_count: number;
            cylinder_count: number;
            daily_allocation: number;
        } | null;
        agencies: any[];
        latest_fleets: any[];
    };
}>();

const selectedArea = ref<number | null>(props.selectedAreaId);

// Watch for area changes and update URL
watch(selectedArea, (newAreaId) => {
    if (newAreaId) {
        router.get(
            route('admin.dashboard.index'),
            { area_id: newAreaId },
            {
                preserveState: true,
                preserveScroll: true,
            },
        );
    }
});

const getAreaColor = (areaCode: string) => {
    switch (areaCode) {
        case 'SS':
            return 'bg-region-sumsel';
        case 'LG':
            return 'bg-region-lampung';
        case 'JB':
            return 'bg-region-jambi';
        case 'BG':
            return 'bg-region-bengkulu';
        case 'BB':
            return 'bg-region-bangka';
        default:
            return 'bg-gray-500';
    }
};

const getAreaBgColor = (areaCode: string) => {
    switch (areaCode) {
        case 'SS':
            return 'bg-green-50 border-green-200 text-green-800';
        case 'LG':
            return 'bg-orange-50 border-orange-200 text-orange-800';
        case 'JB':
            return 'bg-blue-50 border-blue-200 text-blue-800';
        case 'BG':
            return 'bg-red-50 border-red-200 text-red-800';
        case 'BB':
            return 'bg-purple-50 border-purple-200 text-purple-800';
        default:
            return 'bg-gray-50 border-gray-200 text-gray-800';
    }
};

const metricCards = computed(() => [
    {
        key: 'agency_count',
        title: 'Jumlah Agen',
        value: props.metrics.metrics?.agency_count || 0,
        icon: Building2,
        color: 'bg-brand-primary text-white',
    },
    {
        key: 'fleet_count',
        title: 'Jumlah Armada',
        value: props.metrics.metrics?.fleet_count || 0,
        icon: Truck,
        color: 'bg-brand-secondary text-white',
    },
    {
        key: 'driver_count',
        title: 'Jumlah Supir',
        value: props.metrics.metrics?.driver_count || 0,
        icon: Users,
        color: 'bg-brand-primary text-white',
    },
    {
        key: 'cylinder_count',
        title: 'Jumlah Tabung',
        value: props.metrics.metrics?.cylinder_count || 0,
        icon: Package,
        color: 'bg-brand-secondary text-white',
    },
    {
        key: 'daily_allocation',
        title: 'Alokasi Harian',
        value: props.metrics.metrics?.daily_allocation || 0,
        icon: TrendingUp,
        color: 'bg-brand-primary text-white',
    },
]);
</script>

<template>
    <Head :title="page_setting.title" />

    <AppLayout :page-setting="page_setting">
        <div class="space-y-6 p-container">
            <!-- Area Selector -->
            <Card class="shadow-smart-card">
                <CardHeader>
                    <CardTitle class="text-h1 font-bold">Dashboard</CardTitle>
                    <CardDescription class="text-text-secondary">Pilih wilayah untuk melihat ringkasan data operasional</CardDescription>
                </CardHeader>
                <CardContent>
                    <Select
                        v-model="selectedArea"
                        :options="[
                            { value: null, label: 'Semua Wilayah' },
                            ...areas.map((area) => ({ value: area.id, label: area.name }))
                        ]"
                        placeholder="Pilih wilayah..."
                        class="w-full rounded-smart-md border-input"
                    />
                </CardContent>
            </Card>

            <!-- Area Info & Metrics -->
            <div v-if="metrics.area" class="grid grid-cols-1 gap-card-gap lg:grid-cols-1">
                <!-- Selected Area Card -->
                <Card class="shadow-smart-card">
                    <CardHeader>
                        <CardTitle class="text-h1 font-bold">{{ metrics.area.name }}</CardTitle>
                        <CardDescription class="text-text-secondary">Wilayah {{ metrics.area.formatted_code }}</CardDescription>
                    </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-2 gap-4 md:grid-cols-5 lg:col-span-2">
                        <Card v-for="metric in metricCards" :key="metric.key" class="shadow-smart-card transition-shadow hover:shadow-lg">
                            <CardContent class="p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-text-secondary">{{ metric.title }}</p>
                                        <p class="text-3xl font-bold text-text-primary">{{ metric.value }}</p>
                                    </div>
                                    <div :class="['rounded-smart-md p-3', metric.color]">
                                        <component :is="metric.icon" class="h-6 w-6 text-white" />
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>

                </CardContent>
                </Card>
                <!-- Metrics Cards -->
            </div>

            <!-- Summary Tables -->
            <div v-if="metrics.area" class="grid grid-cols-1 gap-card-gap lg:grid-cols-2">
                <!-- Latest Agencies -->
                <Card class="shadow-smart-card">
                    <CardHeader>
                        <CardTitle class="text-h2 font-semibold">Daftar Agen</CardTitle>
                        <CardDescription class="text-text-secondary">Agen LPG di wilayah ini</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="relative overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="border-b bg-gray-50 text-xs uppercase text-gray-700">
                                    <tr>
                                        <th class="px-4 py-3">Nama Agen</th>
                                        <th class="px-4 py-3">Alamat</th>
                                        <th class="px-4 py-3">Armada</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="agency in metrics.agencies" :key="agency.id" class="border-b bg-white hover:bg-gray-50">
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ agency.name }}</td>
                                        <td class="px-4 py-3">{{ agency.address }}</td>
                                        <td class="px-4 py-3 text-center">{{ agency.fleets_count }}</td>
                                    </tr>
                                    <tr v-if="!metrics.agencies?.length">
                                        <td colspan="3" class="px-4 py-8 text-center text-gray-500">Tidak ada data agen</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>

                <!-- Latest Fleets -->
                <Card class="shadow-smart-card">
                    <CardHeader>
                        <CardTitle class="text-h2 font-semibold">Detail Armada</CardTitle>
                        <CardDescription class="text-text-secondary">Armada terbaru di wilayah ini</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="relative overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="border-b bg-gray-50 text-xs uppercase text-gray-700">
                                    <tr>
                                        <th class="px-4 py-3">Nopol</th>
                                        <th class="px-4 py-3">Agen</th>
                                        <th class="px-4 py-3">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="fleet in metrics.latest_fleets" :key="fleet.id" class="border-b bg-white hover:bg-gray-50">
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ fleet.license_plate }}</td>
                                        <td class="px-4 py-3">{{ fleet.agency?.name }}</td>
                                        <td class="px-4 py-3">
                                            <Button variant="outline" size="sm" as-child title="Lihat Detail">
                                                <Link :href="route('admin.fleets.show', fleet)">
                                                    <Eye class="h-4 w-4" /> Lihat Detail
                                                </Link>
                                            </Button>
                                        </td>
                                    </tr>
                                    <tr v-if="!metrics.latest_fleets?.length">
                                        <td colspan="3" class="px-4 py-8 text-center text-gray-500">Tidak ada data armada</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>

        <!-- Empty State -->
        <Card v-if="!metrics.area" class="shadow-smart-card">
            <CardContent class="py-12 text-center">
                <div class="mb-4 text-text-secondary">
                    <Building2 class="mx-auto mb-4 h-12 w-12 text-gray-300" />
                    <p class="text-lg font-medium text-text-primary">Pilih wilayah untuk melihat data</p>
                    <p class="mt-2 text-sm text-text-secondary">Silakan pilih salah satu wilayah dari selector di atas</p>
                </div>
            </CardContent>
        </Card>
    </AppLayout>
</template>
