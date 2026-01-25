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
import type { BreadcrumbItem, Driver, PaginatedData } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Edit, Plus, Search, ToggleLeft, ToggleRight, Trash2, X, User, CreditCard, CheckCircle, Clock, AlertCircle, XCircle } from 'lucide-vue-next';
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
        drivers: PaginatedData<Driver>;
        filters: {
            search?: string;
            area_id?: string;
            sim_status?: string;
            status?: string;
        };
        areas: any[];
        stats: {
            sim: { not_expired: number; near_expiry: number; expired: number };
            status: { active: number; inactive: number; total: number };
        };
    };
}>();

const { can } = usePermission();

const { filters, sort, toggleSort, hasActiveFilters, clearFilters } = useDataTable({
    routeName: 'admin.drivers.index',
    filters: {
        search: props.page_data.filters.search,
        area_id: props.page_data.filters.area_id,
        sim_status: props.page_data.filters.sim_status,
        status: props.page_data.filters.status,
    },
    sortColumn: 'name',
    sortDirection: 'asc' as SortDirection,
});

const deleteDriver = ref<Driver | null>(null);
const showDeleteDialog = ref(false);

const confirmDelete = () => {
    if (deleteDriver.value) {
        router.delete(route('admin.drivers.destroy', { driver: deleteDriver.value.id }), {
            onSuccess: () => {
                showDeleteDialog.value = false;
                deleteDriver.value = null;
            },
            onError: (errors) => {
                console.error('Delete error:', errors);
            },
        });
    }
};

const toggleDriverStatus = (driver: Driver) => {
    router.post(route('admin.drivers.toggle-status', { driver: driver.id }), {
        onSuccess: () => {
            // Status updated successfully
        },
        onError: (errors) => {
            console.error('Toggle status error:', errors);
        },
    });
};

const getSimStatusColor = (status: string) => {
    switch (status) {
        case 'EXPIRED':
            return 'bg-red-100 text-red-800';
        case 'NEAR EXPIRY':
            return 'bg-orange-100 text-orange-800';
        case 'NOT EXPIRED':
            return 'bg-green-100 text-green-800';
        default:
            return 'bg-gray-100 text-gray-800';
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
                        <CardTitle>Database Armada - Drivers</CardTitle>
                        <CardDescription> Kelola data supir beserta status SIM </CardDescription>
                    </div>
                    <Button v-if="can('drivers.create')" as-child>
                        <Link :href="route('admin.drivers.create')">
                            <Plus class="mr-2 h-4 w-4" />
                            Tambah Supir
                        </Link>
                    </Button>
                </div>
            </CardHeader>

            <CardContent>
                <!-- Summary Sections -->
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">
                    <SummarySection title="Status SIM" :icon="CreditCard">
                        <SummaryCard title="Not Expired" :count="page_data.stats.sim.not_expired" :icon="CheckCircle" variant="success" />
                        <SummaryCard title="Near Expiry" :count="page_data.stats.sim.near_expiry" :icon="Clock" variant="warning" />
                        <SummaryCard title="Expired" :count="page_data.stats.sim.expired" :icon="AlertCircle" variant="destructive" />
                    </SummarySection>

                    <SummarySection title="Status Supir" :icon="User">
                        <SummaryCard title="Total Supir" :count="page_data.stats.status.total" :icon="User" />
                        <SummaryCard title="Aktif" :count="page_data.stats.status.active" :icon="CheckCircle" variant="success" />
                        <SummaryCard title="Non-aktif" :count="page_data.stats.status.inactive" :icon="XCircle" variant="destructive" />
                    </SummarySection>
                </div>

                <!-- Filters -->
                <div class="mb-6 flex flex-col gap-4 sm:flex-row">
                    <div class="relative flex-1">
                        <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 transform text-gray-400" />
                        <Input v-model="filters.search" placeholder="Cari nama supir atau nopol armada..." class="pl-10" />
                    </div>

                    <Select
                        v-model="filters.area_id"
                        :options="[{ value: '', label: 'Semua Area' }, ...page_data.areas.map((area) => ({ value: area.id, label: area.name }))]"
                        placeholder="Semua Area"
                        class="w-full sm:w-40"
                    />

                    <Select
                        v-model="filters.sim_status"
                        :options="[
                            { value: '', label: 'Semua Status' },
                            { value: 'expired', label: 'SIM Expired' },
                            { value: 'near_expiry', label: 'SIM Near Expiry' },
                            { value: 'not_expired', label: 'SIM Not Expired' },
                        ]"
                        placeholder="Status SIM"
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

                <!-- Drivers Table -->
                <Table>
                    <TableHeader>
                        <TableRow>
                            <SortableTableHead field="name" :sort="sort" :toggle-sort="toggleSort"> Nama Supir </SortableTableHead>
                            <TableHead>Umur</TableHead>
                            <TableHead>Armada</TableHead>
                            <TableHead>Status SIM</TableHead>
                            <TableHead>Expired In</TableHead>
                            <TableHead class="text-right">Aksi</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="driver in page_data.drivers.data" :key="driver.id">
                            <TableCell class="font-medium">{{ driver.name }}</TableCell>
                            <TableCell>{{ driver.age }} tahun</TableCell>
                            <TableCell>
                                <div v-if="driver.fleet">
                                    {{ driver.fleet.formatted_license_plate }}
                                    <div class="text-xs text-gray-500">{{ driver.fleet.agency?.name }}</div>
                                </div>
                            </TableCell>
                            <TableCell>
                                <Badge :class="getSimStatusColor(driver.sim_status)">
                                    {{ driver.sim_status }}
                                </Badge>
                            </TableCell>
                            <TableCell>{{ driver.days_until_sim_expiry > 0 ? driver.days_until_sim_expiry + ' hari' : 'Expired' }}</TableCell>
                            <TableCell class="text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <Button
                                        v-if="can('drivers.edit')"
                                        variant="outline"
                                        size="sm"
                                        @click="toggleDriverStatus(driver)"
                                        :title="driver.is_active ? 'Non-aktifkan' : 'Aktifkan'"
                                    >
                                        <ToggleRight v-if="driver.is_active" class="h-4 w-4" />
                                        <ToggleLeft v-else class="h-4 w-4" />
                                    </Button>

                                    <Button
                                        v-if="can('drivers.edit')"
                                        variant="outline"
                                        size="sm"
                                        as-child
                                    >
                                        <Link :href="route('admin.drivers.edit', driver)">
                                            <Edit class="h-4 w-4" />
                                        </Link>
                                    </Button>

                                    <Button
                                        v-if="can('drivers.delete')"
                                        variant="outline"
                                        size="sm"
                                        @click="
                                            () => {
                                                deleteDriver = driver;
                                                showDeleteDialog = true;
                                            }
                                        "
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>

                    <TableEmpty v-if="page_data.drivers.data.length === 0">
                        <div class="py-8 text-center">
                            <p class="mb-4 text-gray-500">Tidak ada data supir</p>
                            <Button v-if="can('drivers.create')" as-child>
                                <Link :href="route('admin.drivers.create')">
                                    <Plus class="mr-2 h-4 w-4" />
                                    Tambah Supir Pertama
                                </Link>
                            </Button>
                        </div>
                    </TableEmpty>
                </Table>

                <!-- Pagination -->
                <div class="mt-6">
                    <Pagination :data="page_data.drivers" />
                </div>
            </CardContent>
        </Card>

        <!-- Delete Confirmation Dialog -->
        <AlertDialog v-model:open="showDeleteDialog">
            <template #title> Hapus Supir </template>
            <template #description>
                Apakah Anda yakin ingin menghapus supir "{{ deleteDriver?.name }}"? Tindakan ini tidak dapat dibatalkan.
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
