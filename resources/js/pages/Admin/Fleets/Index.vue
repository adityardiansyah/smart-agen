<script setup lang="ts">
import { AlertDialog } from '@/components/ui/alert-dialog';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Pagination } from '@/components/ui/pagination';
import { Select } from '@/components/ui/select';
import { SortableTableHead, Table, TableBody, TableCell, TableEmpty, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { useDataTable, type SortDirection } from '@/composables/useDataTable';
import { usePermission } from '@/composables/usePermission';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Fleet, PaginatedData } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Download, Edit, Eye, Plus, Search, ToggleLeft, ToggleRight, Trash2, X, Truck, FileText, Calendar, AlertCircle, CheckCircle, Clock } from 'lucide-vue-next';
import SummaryCard from '@/components/SummaryCard.vue';
import SummarySection from '@/components/SummarySection.vue';
import { ref } from 'vue';

const props = defineProps<{
    page_setting: {
        title: string;
        subtitle: string;
        breadcrumbs: BreadcrumbItem[];
    };
    page_data: {
        fleets: PaginatedData<Fleet>;
        filters: {
            search?: string;
            area_id?: string;
            keur_status?: string;
            vehicle_age_status?: string;
            status?: string;
        };
        areas: any[];
        stats: {
            keur: { not_expired: number; near_expiry: number; expired: number };
            stnk: { not_expired: number; near_expiry: number; expired: number };
            vehicle_age: { not_expired: number; near_expiry: number; expired: number };
            status: { active: number; inactive: number; total: number };
        };
    };
}>();

const { can } = usePermission();

const { filters, sort, toggleSort, hasActiveFilters, clearFilters } = useDataTable({
    routeName: 'admin.fleets.index',
    filters: {
        search: props.page_data.filters.search,
        area_id: props.page_data.filters.area_id,
        keur_status: props.page_data.filters.keur_status,
        vehicle_age_status: props.page_data.filters.vehicle_age_status,
        status: props.page_data.filters.status,
    },
    sortColumn: 'license_plate',
    sortDirection: 'asc' as SortDirection,
});

const deleteFleet = ref<Fleet | null>(null);
const showDeleteDialog = ref(false);

const confirmDelete = () => {
    if (deleteFleet.value) {
        router.delete(route('admin.fleets.destroy', { fleet: deleteFleet.value.id }), {
            onSuccess: () => {
                showDeleteDialog.value = false;
                deleteFleet.value = null;
            },
            onError: (errors) => {
                console.error('Delete error:', errors);
            },
        });
    }
};

const toggleFleetStatus = (fleet: Fleet) => {
    router.post(route('admin.fleets.toggle-status', { fleet: fleet.id }), {
        onSuccess: () => {
            // Status updated successfully
        },
        onError: (errors) => {
            console.error('Toggle status error:', errors);
        },
    });
};

const getKeurStatusColor = (status: string) => {
    switch (status) {
        case 'NEAR EXPIRY':
            return 'bg-orange-100 text-orange-800';
        case 'NOT EXPIRED':
            return 'bg-green-100 text-green-800';
        default:
            return 'bg-red-100 text-red-800';
    }
};

const getVehicleAgeStatusColor = (status: string) => {
    switch (status) {
        case 'NEAR EXPIRY':
            return 'bg-orange-100 text-orange-800';
        case 'NOT EXPIRED':
            return 'bg-green-100 text-green-800';
        default:
            return 'bg-red-100 text-red-800';
    }
};
</script>

<template>
    <Head :title="page_setting.title" />

    <AppLayout :page-setting="page_setting">
        <Card>
            <CardHeader>
                <div class="flex items-center justify-between">
                    <div>
                        <CardTitle>Database Armada - Fleets</CardTitle>
                        <CardDescription> Kelola data armada truk LPG beserta status dokumen </CardDescription>
                    </div>
                    <div class="flex gap-2">
                        <Button variant="outline" as-child>
                            <a :href="route('admin.fleets.export', { area_id: filters.area_id })">
                                <Download class="mr-2 h-4 w-4" />
                                Export Excel
                            </a>
                        </Button>
                        <Button v-if="can('fleets.create')" as-child>
                            <Link :href="route('admin.fleets.create')">
                                <Plus class="mr-2 h-4 w-4" />
                                Tambah Armada
                            </Link>
                        </Button>
                    </div>
                </div>
            </CardHeader>

            <CardContent>
                <!-- Summary Sections -->
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">
                    <SummarySection title="Status KEUR" :icon="FileText">
                        <SummaryCard title="Not Expired" :count="page_data.stats.keur.not_expired" :icon="CheckCircle" variant="success" />
                        <SummaryCard title="Near Expiry" :count="page_data.stats.keur.near_expiry" :icon="Clock" variant="warning" />
                        <SummaryCard title="Expired" :count="page_data.stats.keur.expired" :icon="AlertCircle" variant="destructive" />
                    </SummarySection>

                    <SummarySection title="Status STNK" :icon="FileText">
                        <SummaryCard title="Not Expired" :count="page_data.stats.stnk.not_expired" :icon="CheckCircle" variant="success" />
                        <SummaryCard title="Near Expiry" :count="page_data.stats.stnk.near_expiry" :icon="Clock" variant="warning" />
                        <SummaryCard title="Expired" :count="page_data.stats.stnk.expired" :icon="AlertCircle" variant="destructive" />
                    </SummarySection>
                    
                    <SummarySection title="Status Umur Kendaraan" :icon="Calendar">
                        <SummaryCard title="Not Expired" :count="page_data.stats.vehicle_age.not_expired" :icon="CheckCircle" variant="success" />
                        <SummaryCard title="Near Expiry" :count="page_data.stats.vehicle_age.near_expiry" :icon="Clock" variant="warning" />
                        <SummaryCard title="Expired" :count="page_data.stats.vehicle_age.expired" :icon="AlertCircle" variant="destructive" />
                    </SummarySection>
                </div>

                <!-- Filters -->
                <div class="mb-6 flex flex-col gap-4 sm:flex-row">
                    <div class="relative flex-1">
                        <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 transform text-gray-400" />
                        <Input v-model="filters.search" placeholder="Cari nopol atau nama agen..." class="pl-10" />
                    </div>

                    <Select
                        v-model="filters.area_id"
                        :options="[{ value: '', label: 'Semua Area' }, ...page_data.areas.map((area) => ({ value: area.id, label: area.name }))]"
                        placeholder="Semua Area"
                        class="w-full sm:w-40"
                    />

                    <Select
                        v-model="filters.keur_status"
                        :options="[
                            { value: '', label: 'Semua Status' },
                            { value: 'near_expiry', label: 'KEUR Near Expiry' },
                            { value: 'not_expired', label: 'KEUR Not Expired' },
                        ]"
                        placeholder="Status KEUR"
                        class="w-full sm:w-40"
                    />

                    <Select
                        v-model="filters.vehicle_age_status"
                        :options="[
                            { value: '', label: 'Semua Status' },
                            { value: 'near_expired', label: 'Usia \u2265 9 Tahun' },
                            { value: 'not_expired', label: 'Usia < 9 Tahun' },
                        ]"
                        placeholder="Status Usia"
                        class="w-full sm:w-40"
                    />

                    <Select
                        v-model="filters.status"
                        :options="[
                            { value: '', label: 'Semua Status' },
                            { value: 'active', label: 'Aktif' },
                            { value: 'inactive', label: 'Non-aktif' },
                        ]"
                        placeholder="Status"
                        class="w-full sm:w-40"
                    />

                    <Button v-if="hasActiveFilters" variant="outline" @click="clearFilters">
                        <X class="mr-2 h-4 w-4" />
                        Clear
                    </Button>
                </div>

                <!-- Fleets Table -->
                <Table>
                    <TableHeader>
                        <TableRow>
                            <SortableTableHead field="license_plate" :sort="sort" :toggle-sort="toggleSort"> Nomor Polisi </SortableTableHead>
                            <TableHead>Agen</TableHead>
                            <TableHead>Tahun</TableHead>
                            <TableHead>Status KEUR</TableHead>
                            <TableHead>Status Usia Armada</TableHead>
                            <TableHead>Supir</TableHead>
                            <TableHead class="text-right">Aksi</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="fleet in page_data.fleets.data" :key="fleet.id">
                            <TableCell class="font-medium">{{ fleet.formatted_license_plate }}</TableCell>
                            <TableCell>
                                <div v-if="fleet.agency">
                                    {{ fleet.agency.name }}
                                    <div class="text-xs text-gray-500">{{ fleet.agency.area?.name }}</div>
                                </div>
                            </TableCell>
                            <TableCell>{{ fleet.year_manufacture }}</TableCell>
                            <TableCell>
                                <Badge :class="getKeurStatusColor(fleet.keur_status)">
                                    {{ fleet.keur_status }}
                                </Badge>
                            </TableCell>
                            <TableCell>
                                <Badge :class="getVehicleAgeStatusColor(fleet.vehicle_age_status)">
                                    {{ fleet.vehicle_age_status }}
                                </Badge>
                            </TableCell>
                            <TableCell>{{ fleet.active_drivers_count || 0 }}</TableCell>
                            <TableCell class="text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <Button
                                        v-if="can('fleets.edit')"
                                        variant="outline"
                                        size="sm"
                                        @click="toggleFleetStatus(fleet)"
                                        :title="fleet.is_active ? 'Non-aktifkan' : 'Aktifkan'"
                                    >
                                        <ToggleRight v-if="fleet.is_active" class="h-4 w-4" />
                                        <ToggleLeft v-else class="h-4 w-4" />
                                    </Button>

                                    <Button v-if="can('fleets.view')" variant="outline" size="sm" as-child title="Lihat Detail">
                                        <Link :href="route('admin.fleets.show', fleet)">
                                            <Eye class="h-4 w-4" />
                                        </Link>
                                    </Button>

                                    <Button
                                        v-if="can('fleets.edit')"
                                        variant="outline"
                                        size="sm"
                                        as-child
                                    >
                                        <Link :href="route('admin.fleets.edit', fleet)">
                                            <Edit class="h-4 w-4" />
                                        </Link>
                                    </Button>

                                    <Button
                                        v-if="can('fleets.delete')"
                                        variant="outline"
                                        size="sm"
                                        @click="
                                            () => {
                                                deleteFleet = fleet;
                                                showDeleteDialog = true;
                                            }
                                        "
                                        :disabled="fleet.active_drivers_count > 0"
                                        :title="fleet.active_drivers_count > 0 ? 'Tidak dapat dihapus (memiliki supir)' : 'Hapus armada'"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>

                    <TableEmpty v-if="page_data.fleets.data.length === 0">
                        <div class="py-8 text-center">
                            <p class="mb-4 text-gray-500">Tidak ada data armada</p>
                            <Button v-if="can('fleets.create')" as-child>
                                <Link :href="route('admin.fleets.create')">
                                    <Plus class="mr-2 h-4 w-4" />
                                    Tambah Armada Pertama
                                </Link>
                            </Button>
                        </div>
                    </TableEmpty>
                </Table>

                <!-- Pagination -->
                <div class="mt-6">
                    <Pagination :data="page_data.fleets" />
                </div>
            </CardContent>
        </Card>

        <!-- Delete Confirmation Dialog -->
        <AlertDialog v-model:open="showDeleteDialog">
            <template #title> Hapus Armada </template>
            <template #description>
                Apakah Anda yakin ingin menghapus armada "{{ deleteFleet?.formatted_license_plate }}"? Tindakan ini tidak dapat dibatalkan.
            </template>
            <template #cancel>
                <Button variant="outline" @click="showDeleteDialog = false"> Batal </Button>
            </template>
            <template #confirm>
                <Button variant="destructive" @click="confirmDelete">
                    <Trash2 class="mr-2 h-4 w-4" />
                    Hapus
                </Button>
            </template>
        </AlertDialog>
    </AppLayout>
</template>
