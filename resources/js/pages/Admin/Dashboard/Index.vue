<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Select } from '@/components/ui/select';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Area, BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Building2, ChevronRight, Edit, Eye, Package, TrendingUp, Truck, User, Users } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

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
    };
}>();

const expandedAgencies = ref<number[]>([]);
const expandedFleets = ref<number[]>([]);

const toggleAgency = (id: number) => {
    const index = expandedAgencies.value.indexOf(id);
    if (index > -1) {
        expandedAgencies.value.splice(index, 1);
    } else {
        expandedAgencies.value.push(id);
    }
};

const toggleFleet = (id: number) => {
    const index = expandedFleets.value.indexOf(id);
    if (index > -1) {
        expandedFleets.value.splice(index, 1);
    } else {
        expandedFleets.value.push(id);
    }
};

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
            <div v-if="metrics.area" class="grid grid-cols-1">
                <Card class="shadow-smart-card">
                    <CardHeader>
                        <CardTitle class="text-h2 font-semibold">Detail Agen</CardTitle>
                        <CardDescription class="text-text-secondary">Lihat data agen, armada, dan supir secara hierarkis</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="rounded-md border bg-white">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead class="w-10"></TableHead>
                                        <TableHead>Nama Agen</TableHead>
                                        <TableHead>Alamat</TableHead>
                                        <TableHead>Jumlah Armada</TableHead>
                                        <TableHead>Jumlah Tabung</TableHead>
                                        <TableHead>Alokasi Harian</TableHead>
                                        <TableHead class="text-right">Aksi</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <template v-for="agency in metrics.agencies" :key="agency.id">
                                        <TableRow class="hover:bg-muted/30">
                                            <TableCell>
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    class="h-8 w-8 p-0"
                                                    @click="toggleAgency(agency.id)"
                                                >
                                                    <ChevronRight
                                                        class="h-4 w-4 transition-transform duration-200"
                                                        :class="{ 'rotate-90': expandedAgencies.includes(agency.id) }"
                                                    />
                                                </Button>
                                            </TableCell>
                                            <TableCell class="font-medium text-text-primary">{{ agency.name }}</TableCell>
                                            <TableCell class="text-text-secondary">{{ agency.address }}</TableCell>
                                            <TableCell>
                                                <Badge variant="secondary">{{ agency.fleets_count }} Armada</Badge>
                                            </TableCell>
                                            <TableCell>{{ agency.cylinder_count || 0 }}</TableCell>
                                            <TableCell>{{ agency.daily_delivery || 0 }}</TableCell>
                                            <TableCell class="text-right">
                                                <Button variant="ghost" size="sm" as-child>
                                                    <Link :href="route('admin.agencies.edit', { agency: agency.id })">
                                                        <Edit class="mr-2 h-4 w-4" /> Edit
                                                    </Link>
                                                </Button>
                                            </TableCell>
                                        </TableRow>

                                        <!-- Fleets Nested Table -->
                                        <TableRow v-if="expandedAgencies.includes(agency.id)">
                                            <TableCell colspan="7" class="bg-muted/20 p-0">
                                                <div class="p-6 pl-12">
                                                    <h4 class="mb-4 flex items-center gap-2 text-sm font-semibold text-text-secondary">
                                                        <Truck class="h-4 w-4 text-brand-secondary" />
                                                        Daftar Armada - {{ agency.name }}
                                                    </h4>
                                                    <div class="rounded-lg border bg-white shadow-sm overflow-hidden">
                                                        <Table>
                                                            <TableHeader class="bg-gray-50/50">
                                                                <TableRow>
                                                                    <TableHead class="w-10"></TableHead>
                                                                    <TableHead>No. Plat</TableHead>
                                                                    <TableHead>Tahun</TableHead>
                                                                    <TableHead>Status KEUR</TableHead>
                                                                    <TableHead>Status STNK</TableHead>
                                                                    <TableHead class="text-right">Aksi</TableHead>
                                                                </TableRow>
                                                            </TableHeader>
                                                            <TableBody>
                                                                <template v-for="fleet in agency.fleets" :key="fleet.id">
                                                                    <TableRow class="hover:bg-muted/30">
                                                                        <TableCell>
                                                                            <Button
                                                                                variant="ghost"
                                                                                size="sm"
                                                                                class="h-6 w-6 p-0"
                                                                                @click="toggleFleet(fleet.id)"
                                                                            >
                                                                                <ChevronRight
                                                                                    class="h-4 w-4 transition-transform duration-200"
                                                                                    :class="{ 'rotate-90': expandedFleets.includes(fleet.id) }"
                                                                                />
                                                                            </Button>
                                                                        </TableCell>
                                                                        <TableCell class="font-medium">{{ fleet.license_plate }}</TableCell>
                                                                        <TableCell>{{ fleet.year_manufacture }}</TableCell>
                                                                        <TableCell>
                                                                            <Badge :variant="fleet.keur_status === 'NOT EXPIRED' ? 'default' : 'destructive'">
                                                                                {{ fleet.keur_status || 'N/A' }}
                                                                            </Badge>
                                                                        </TableCell>
                                                                        <TableCell>
                                                                             <Badge :variant="fleet.stnk_status === 'NOT EXPIRED' ? 'default' : 'destructive'">
                                                                                {{ fleet.stnk_status || 'N/A' }}
                                                                            </Badge>
                                                                        </TableCell>
                                                                        <TableCell class="text-right">
                                                                            <Button variant="ghost" size="sm" as-child>
                                                                                <Link :href="route('admin.fleets.edit', fleet.id)">
                                                                                    <Edit class="h-4 w-4" />
                                                                                </Link>
                                                                            </Button>
                                                                        </TableCell>
                                                                    </TableRow>

                                                                    <!-- Drivers Nested Table -->
                                                                    <TableRow v-if="expandedFleets.includes(fleet.id)">
                                                                        <TableCell colspan="6" class="bg-muted/10 p-4 pl-12">
                                                                            <div class="flex items-center gap-2 mb-3 text-xs font-semibold uppercase tracking-wider text-text-secondary">
                                                                                <Users class="h-3 w-3" />
                                                                                Tim Supir
                                                                            </div>
                                                                            <div class="rounded-md border bg-white overflow-hidden">
                                                                                <Table>
                                                                                    <TableHeader class="bg-gray-50/50">
                                                                                        <TableRow>
                                                                                            <TableHead>Nama Supir</TableHead>
                                                                                            <TableHead>Umur</TableHead>
                                                                                            <TableHead>Expired SIM</TableHead>
                                                                                            <TableHead>Status SIM</TableHead>
                                                                                            <TableHead>Status</TableHead>
                                                                                            <TableHead class="text-right">Aksi</TableHead>
                                                                                        </TableRow>
                                                                                    </TableHeader>
                                                                                    <TableBody>
                                                                                        <TableRow v-for="driver in fleet.drivers" :key="driver.id" class="hover:bg-muted/30">
                                                                                            <TableCell class="font-medium">{{ driver.name }}</TableCell>
                                                                                            <TableCell>{{ driver.age }} Tahun</TableCell>
                                                                                            <TableCell>{{ driver.sim_expiry_date || '-' }}</TableCell>
                                                                                            <TableCell>
                                                                                                <Badge :variant="driver.sim_status === 'NOT EXPIRED' ? 'default' : 'destructive'">
                                                                                                    {{ driver.sim_status || 'N/A' }}
                                                                                                </Badge>
                                                                                            </TableCell>
                                                                                            <TableCell>
                                                                                                <Badge :variant="driver.is_active ? 'default' : 'secondary'">
                                                                                                    {{ driver.is_active ? 'Aktif' : 'Non-aktif' }}
                                                                                                </Badge>
                                                                                            </TableCell>
                                                                                            <TableCell class="text-right">
                                                                                                <Button variant="ghost" size="sm" as-child>
                                                                                                    <Link :href="route('admin.drivers.edit', driver.id)">
                                                                                                        <Edit class="h-4 w-4" />
                                                                                                    </Link>
                                                                                                </Button>
                                                                                            </TableCell>
                                                                                        </TableRow>
                                                                                        <TableRow v-if="!fleet.drivers?.length">
                                                                                            <TableCell colspan="6" class="py-4 text-center text-muted-foreground italic">
                                                                                                Belum ada supir terdaftar
                                                                                            </TableCell>
                                                                                        </TableRow>
                                                                                    </TableBody>
                                                                                </Table>
                                                                            </div>
                                                                        </TableCell>
                                                                    </TableRow>
                                                                </template>
                                                                <TableRow v-if="!agency.fleets?.length">
                                                                    <TableCell colspan="6" class="py-10 text-center text-muted-foreground italic">
                                                                        Tidak ada armada terdaftar di agen ini
                                                                    </TableCell>
                                                                </TableRow>
                                                            </TableBody>
                                                        </Table>
                                                    </div>
                                                </div>
                                            </TableCell>
                                        </TableRow>
                                    </template>
                                    <TableRow v-if="!metrics.agencies?.length">
                                        <td colspan="7" class="py-20 text-center text-text-secondary">
                                            <Building2 class="mx-auto mb-4 h-12 w-12 text-gray-200" />
                                            <p class="text-lg font-medium">Data agen tidak ditemukan</p>
                                        </td>
                                    </TableRow>
                                </TableBody>
                            </Table>
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
