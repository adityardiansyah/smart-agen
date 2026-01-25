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
import type { Agency, BreadcrumbItem, PaginatedData } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Edit, Plus, Search, ToggleLeft, ToggleRight, Trash2, X, Building2, CheckCircle, XCircle } from 'lucide-vue-next';
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
        agencies: PaginatedData<Agency>;
        filters: {
            search?: string;
            area_id?: string;
            status?: string;
        };
        areas: any[];
        stats: {
            active: number;
            inactive: number;
            total: number;
        };
    };
}>();

const { can } = usePermission();

const { filters, sort, toggleSort, hasActiveFilters, clearFilters } = useDataTable({
    routeName: 'admin.agencies.index',
    filters: {
        search: props.page_data.filters.search,
        area_id: props.page_data.filters.area_id,
        status: props.page_data.filters.status,
    },
    sortColumn: 'name',
    sortDirection: 'asc' as SortDirection,
});

const deleteAgency = ref<Agency | null>(null);
const showDeleteDialog = ref(false);

const confirmDelete = () => {
    if (deleteAgency.value) {
        router.delete(route('admin.agencies.destroy', { agency: deleteAgency.value.id }), {
            onSuccess: () => {
                showDeleteDialog.value = false;
                deleteAgency.value = null;
            },
            onError: (errors) => {
                console.error('Delete error:', errors);
            },
        });
    }
};

const toggleAgencyStatus = (agency: Agency) => {
    router.post(route('admin.agencies.toggle-status', { agency: agency.id }), {}, {
        onSuccess: () => {
            // Status updated successfully
        },
        onError: (errors) => {
            console.error('Toggle status error:', errors);
        },
    });
};
</script>

<template>
    <Head :title="page_setting.title" />

    <AppLayout :page-setting="page_setting">
        <div class="p-container">
            <Card class="shadow-smart-card">
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle class="text-h1 font-bold text-text-primary">Data Agen</CardTitle>
                            <CardDescription class="text-text-secondary">Kelola data agen LPG berdasarkan wilayah</CardDescription>
                        </div>
                        <Button
                            v-if="can('agencies.create')"
                            as-child
                            class="rounded-smart-md bg-brand-primary text-white hover:bg-brand-primary/90"
                        >
                            <Link :href="route('admin.agencies.create')">
                                <Plus class="mr-2 h-4 w-4" />
                                Tambah Agen
                            </Link>
                        </Button>
                    </div>
                </CardHeader>

                <CardContent>
                    <!-- Summary Section -->
                    <SummarySection title="Ringkasan Agen" :icon="Building2">
                        <SummaryCard title="Total Agen" :count="page_data.stats.total" :icon="Building2" />
                        <SummaryCard title="Agen Aktif" :count="page_data.stats.active" :icon="CheckCircle" variant="success" />
                        <SummaryCard title="Agen Non-aktif" :count="page_data.stats.inactive" :icon="XCircle" variant="destructive" />
                    </SummarySection>

                    <!-- Filters -->
                    <div class="mb-6 flex flex-col gap-4 sm:flex-row">
                        <div class="relative flex-1">
                            <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 transform text-text-secondary" />
                            <Input v-model="filters.search" placeholder="Cari nama agen atau alamat..." class="rounded-smart-md border-input pl-10" />
                        </div>

                        <Select
                            v-model="filters.area_id"
                            :options="[{ value: '', label: 'Semua Area' }, ...page_data.areas.map((area) => ({ value: area.id, label: area.name }))]"
                            placeholder="Semua Area"
                            class="w-full rounded-smart-md border-input sm:w-40"
                        />

                        <Select
                            v-model="filters.status"
                            :options="[
                                { value: '', label: 'Semua Status' },
                                { value: 'active', label: 'Aktif' },
                                { value: 'inactive', label: 'Non-aktif' },
                            ]"
                            placeholder="Status"
                            class="w-full rounded-smart-md border-input sm:w-40"
                        />


                        <Button v-if="hasActiveFilters" variant="outline" @click="clearFilters" class="rounded-smart-md">
                            <X class="mr-2 h-4 w-4" />
                            Clear
                        </Button>
                    </div>

                    <!-- Agencies Table -->
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <SortableTableHead column="name" :sort="sort" :toggle-sort="toggleSort"> Nama Agen </SortableTableHead>
                                <TableHead>Area</TableHead>
                                <TableHead>Alamat</TableHead>
                                <TableHead>Jumlah Tabung</TableHead>
                                <TableHead>Alokasi/Hari</TableHead>
                                <TableHead>Armada Aktif</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead class="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="agency in page_data.agencies.data" :key="agency.id">
                                <TableCell class="font-medium">{{ agency.name }}</TableCell>
                                <TableCell>
                                    <div class="font-medium">{{ agency.area?.name }}</div>
                                    <div class="text-sm text-muted-foreground">{{ agency.area?.code }}</div>
                                </TableCell>
                                <TableCell>
                                    <div class="font-medium">{{ agency.address }}</div>
                                </TableCell>
                                <TableCell>
                                    {{ agency.cylinder_count }}
                                </TableCell>
                                <TableCell>
                                    {{ agency.daily_allocation }}
                                </TableCell>
                                <TableCell>
                                    <Badge variant="outline"> {{ agency.active_fleets_count }} Armada </Badge>
                                </TableCell>
                                <TableCell>
                                    <Badge :variant="agency.is_active ? 'default' : 'secondary'">
                                        {{ agency.is_active ? 'Aktif' : 'Non-aktif' }}
                                    </Badge>
                                </TableCell>
                                <TableCell class="text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <Button
                                            v-if="can('agencies.edit')"
                                            variant="outline"
                                            size="sm"
                                            @click="toggleAgencyStatus(agency)"
                                            :title="agency.is_active ? 'Non-aktifkan' : 'Aktifkan'"
                                        >
                                            <ToggleRight v-if="agency.is_active" class="h-4 w-4" />
                                            <ToggleLeft v-else class="h-4 w-4" />
                                        </Button>

                                        <Button
                                            v-if="can('agencies.edit')"
                                            variant="outline"
                                            size="sm"
                                            as-child
                                        >
                                            <Link :href="route('admin.agencies.edit', { agency: agency.id })">
                                                <Edit class="h-4 w-4" />
                                            </Link>
                                        </Button>

                                        <Button
                                            v-if="can('agencies.delete')"
                                            variant="outline"
                                            size="sm"
                                            @click="
                                                () => {
                                                    deleteAgency = agency;
                                                    showDeleteDialog = true;
                                                }
                                            "
                                            :disabled="(agency.active_fleets_count || 0) > 0"
                                            :title="(agency.active_fleets_count || 0) > 0 ? 'Tidak dapat dihapus (memiliki armada)' : 'Hapus agen'"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                        </TableBody>

                        <TableEmpty v-if="page_data.agencies.data.length === 0">
                            <div class="py-8 text-center">
                                <p class="mb-4 text-gray-500">Tidak ada data agen</p>
                                <Button v-if="can('agencies.create')" as-child>
                                    <Link :href="route('admin.agencies.create')">
                                        <Plus class="mr-2 h-4 w-4" />
                                        Tambah Agen Pertama
                                    </Link>
                                </Button>
                            </div>
                        </TableEmpty>
                    </Table>

                    <!-- Pagination -->
                    <div class="mt-6">
                        <Pagination :data="page_data.agencies" />
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Delete Confirmation Dialog -->
        <AlertDialog v-model:open="showDeleteDialog">
            <template #title> Hapus Agen </template>
            <template #description>
                Apakah Anda yakin ingin menghapus agen "{{ deleteAgency?.name }}"? Tindakan ini tidak dapat dibatalkan.
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
