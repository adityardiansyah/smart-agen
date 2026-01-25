<script setup lang="ts">
import { AlertDialog } from '@/components/ui/alert-dialog';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Pagination } from '@/components/ui/pagination';
import { Select } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableEmpty, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { useDataTable, type SortDirection } from '@/composables/useDataTable';
import { usePermission } from '@/composables/usePermission';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Area, BreadcrumbItem, PaginatedData } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Building2, ChevronRight, Edit, Eye, Plus, Search, ToggleLeft, ToggleRight, Trash2, X, User, Map, CheckCircle, XCircle } from 'lucide-vue-next';
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
        areas: PaginatedData<Area>;
        filters: {
            search?: string;
            status?: string;
        };
        stats: {
            active: number;
            inactive: number;
            total: number;
        };
    };
}>();

const { can } = usePermission();

const { filters, sort, toggleSort, hasActiveFilters, clearFilters } = useDataTable({
    routeName: 'admin.areas.index',
    filters: {
        search: props.page_data.filters.search,
        status: props.page_data.filters.status,
    },
    sortColumn: 'name',
    sortDirection: 'asc' as SortDirection,
});

const deleteArea = ref<Area | null>(null);
const showDeleteDialog = ref(false);

const confirmDelete = () => {
    if (deleteArea.value) {
        router.delete(route('admin.areas.destroy', { area: deleteArea.value.id }), {
            onSuccess: () => {
                showDeleteDialog.value = false;
                deleteArea.value = null;
            },
            onError: (errors) => {
                console.error('Delete error:', errors);
            },
        });
    }
};

const toggleAreaStatus = (area: Area) => {
    router.post(route('admin.areas.toggle-status', { area: area.id }));
};

const expandedAreas = ref<number[]>([]);

const toggleArea = (id: number) => {
    if (expandedAreas.value.includes(id)) {
        expandedAreas.value = expandedAreas.value.filter((itemId) => itemId !== id);
    } else {
        expandedAreas.value.push(id);
    }
};

const expandedAgency = ref<number[]>([]);

const toggleAgency = (id: number) => {
    if (expandedAgency.value.includes(id)) {
        expandedAgency.value = expandedAgency.value.filter((itemId) => itemId !== id);
    } else {
        expandedAgency.value.push(id);
    }
};

const expandedFleets = ref<number[]>([]);

const toggleFleet = (id: number) => {
    if (expandedFleets.value.includes(id)) {
        expandedFleets.value = expandedFleets.value.filter((itemId) => itemId !== id);
    } else {
        expandedFleets.value.push(id);
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
                        <CardTitle>Manajemen Area</CardTitle>
                        <CardDescription> Kelola data wilayah operasional SMART AGEN LPG </CardDescription>
                    </div>
                    <Button v-if="can('areas.create')" as-child>
                        <Link :href="route('admin.areas.create')">
                            <Plus class="mr-2 h-4 w-4" />
                            Tambah Area
                        </Link>
                    </Button>
                </div>
            </CardHeader>

            <CardContent>
                <!-- Summary Section -->
                <SummarySection title="Ringkasan Area" :icon="Map">
                    <SummaryCard title="Total Area" :count="page_data.stats.total" :icon="Map" />
                    <SummaryCard title="Area Aktif" :count="page_data.stats.active" :icon="CheckCircle" variant="success" />
                    <SummaryCard title="Area Non-aktif" :count="page_data.stats.inactive" :icon="XCircle" variant="destructive" />
                </SummarySection>

                <!-- Filters -->
                <div class="mb-6 flex flex-col gap-4 sm:flex-row">
                    <div class="relative flex-1">
                        <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 transform text-gray-400" />
                        <Input v-model="filters.search" placeholder="Cari nama atau kode area..." class="pl-10" />
                    </div>

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

                <!-- Areas Table -->
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-12"></TableHead>
                            <TableHead>Nama Area</TableHead>
                            <TableHead>Kode</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Agen</TableHead>
                            <TableHead class="text-right">Aksi</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <template v-for="area in page_data.areas.data" :key="area.id">
                            <TableRow class="group">
                                <TableCell>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        class="h-6 w-6 p-0"
                                        @click="toggleArea(area.id)"
                                    >
                                        <ChevronRight
                                            class="h-4 w-4 transition-transform duration-200"
                                            :class="{ 'rotate-90': expandedAreas.includes(area.id) }"
                                        />
                                    </Button>
                                </TableCell>
                                <TableCell class="font-medium">{{ area.name }}</TableCell>
                                <TableCell>
                                    <Badge variant="secondary">{{ area.formatted_code }}</Badge>
                                </TableCell>
                                <TableCell>
                                    <Badge :variant="area.is_active ? 'default' : 'secondary'">
                                        {{ area.is_active ? 'Aktif' : 'Non-aktif' }}
                                    </Badge>
                                </TableCell>
                                <TableCell>{{ area.agencies_count || 0 }}</TableCell>
                                <TableCell class="text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <Button
                                            v-if="can('areas.edit')"
                                            variant="outline"
                                            size="sm"
                                            @click="toggleAreaStatus(area)"
                                            :title="area.is_active ? 'Non-aktifkan' : 'Aktifkan'"
                                        >
                                            <ToggleRight v-if="area.is_active" class="h-4 w-4" />
                                            <ToggleLeft v-else class="h-4 w-4" />
                                        </Button>

                                        <Button v-if="can('areas.view')" variant="outline" size="sm" as-child title="Lihat Detail">
                                            <Link :href="route('admin.areas.show', area)">
                                                <Eye class="h-4 w-4" />
                                            </Link>
                                        </Button>

                                        <Button v-if="can('areas.edit')" variant="outline" size="sm" as-child>
                                            <Link :href="route('admin.areas.edit', area)">
                                                <Edit class="h-4 w-4" />
                                            </Link>
                                        </Button>

                                        <Button
                                            v-if="can('areas.delete')"
                                            variant="outline"
                                            size="sm"
                                            @click="
                                                () => {
                                                    deleteArea = area;
                                                    showDeleteDialog = true;
                                                }
                                            "
                                            :disabled="area.agencies_count > 0"
                                            :title="
                                                area.agencies_count > 0 ? 'Tidak dapat dihapus (memiliki relasi)' : 'Hapus area'
                                            "
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                            <!-- Collapsible Content Row -->
                            <TableRow v-if="expandedAreas.includes(area.id)">
                                <TableCell colspan="7" class="p-0 bg-muted/30">
                                    <div class="p-4 pl-12 rounded-b-lg">
                                        <h4 class="mb-2 text-sm font-semibold text-muted-foreground flex items-center gap-2">
                                            <Building2 class="h-4 w-4" /> <!-- Assuming Building2 is imported or I should import it, or use text -->
                                            Daftar Agen
                                        </h4>
                                        <div class="rounded-md border bg-white">
                                            <Table>
                                                <TableHeader>
                                                    <TableRow>
                                                        <TableHead>Nama Agen</TableHead>
                                                        <TableHead>Alamat</TableHead>
                                                        <TableHead>Jumlah Armada</TableHead>
                                                        <TableHead class="text-right">Aksi</TableHead>
                                                    </TableRow>
                                                </TableHeader>
                                                <TableBody>
                                                    <template v-for="agency in area.agencies" :key="agency.id">
                                                        <TableRow>
                                                            <TableCell>
                                                                <Button
                                                                    variant="ghost"
                                                                    size="sm"
                                                                    class="h-6 w-6 p-0"
                                                                    @click="toggleAgency(agency.id)"
                                                                >
                                                                    <ChevronRight
                                                                        class="h-4 w-4 transition-transform duration-200"
                                                                        :class="{ 'rotate-90': expandedAgency.includes(agency.id) }"
                                                                    />
                                                                </Button>
                                                            </TableCell>
                                                            <TableCell>{{ agency.name }}</TableCell>
                                                            <TableCell>{{ agency.address }}</TableCell>
                                                            <TableCell>{{ agency.fleets_count }}</TableCell>
                                                            <TableCell class="text-right">
                                                                <Button v-if="can('agencies.edit')" variant="ghost" size="sm" as-child>
                                                                    <Link :href="route('admin.agencies.edit', { agency: agency.id })">
                                                                        <Edit class="h-3 w-3 mr-1" />
                                                                        Edit
                                                                    </Link>
                                                                </Button>
                                                            </TableCell>
                                                        </TableRow>
                                                        <!-- Collapsible Content Row -->
                                                        <TableRow v-if="expandedAgency.includes(agency.id)">
                                                            <TableCell colspan="5" class="p-0 bg-muted/30">
                                                                <div class="p-4 pl-12 rounded-b-lg">
                                                                    <h4 class="mb-2 text-sm font-semibold text-muted-foreground flex items-center gap-2">
                                                                        <Building2 class="h-4 w-4" />
                                                                        Daftar Armada
                                                                    </h4>
                                                                    <div class="rounded-md border bg-white">
                                                                        <Table>
                                                                            <TableHeader>
                                                                                <TableRow>
                                                                                    <TableHead>No. Plat Armada</TableHead>
                                                                                    <TableHead>Tahun Armada</TableHead>
                                                                                    <TableHead>No. KEUR</TableHead>
                                                                                    <TableHead>Expired KEUR</TableHead>
                                                                                    <TableHead>Status KEUR</TableHead>
                                                                                    <TableHead>Expired STNK</TableHead>
                                                                                    <TableHead>Status STNK</TableHead>
                                                                                    <TableHead>Expired Armada</TableHead>
                                                                                    <TableHead>Status Armada</TableHead>
                                                                                    <TableHead class="text-right">Aksi</TableHead>
                                                                                </TableRow>
                                                                            </TableHeader>
                                                                            <TableBody>
                                                                                <template v-for="fleet in agency.fleets" :key="fleet.id">
                                                                                    <TableRow>
                                                                                        <TableCell>
                                                                                            <div class="flex items-center gap-2">
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
                                                                                                {{ fleet.license_plate }}
                                                                                            </div>
                                                                                        </TableCell>
                                                                                        <TableCell>{{ fleet.year_manufacture }}</TableCell>
                                                                                        <TableCell>{{ fleet.keur_number }}</TableCell>
                                                                                        <TableCell>{{ fleet.keur_expiry_date }}</TableCell>
                                                                                        <TableCell>{{ fleet.keur_status }}</TableCell>
                                                                                        <TableCell>{{ fleet.stnk_expiry_date }}</TableCell>
                                                                                        <TableCell>{{ fleet.stnk_status }}</TableCell>
                                                                                        <TableCell>{{ fleet.vehicle_expiry }}</TableCell>
                                                                                        <TableCell>{{ fleet.vehicle_age_status }}</TableCell>
                                                                                        <TableCell class="text-right">
                                                                                            <Button v-if="can('agencies.edit')" variant="ghost" size="sm" as-child>
                                                                                                <Link :href="route('admin.fleets.edit', fleet.id)">
                                                                                                    <Edit class="h-3 w-3 mr-1" />
                                                                                                    Edit
                                                                                                </Link>
                                                                                            </Button>
                                                                                        </TableCell>
                                                                                    </TableRow>
                                                                                    <!-- Drivers Sub-table -->
                                                                                    <TableRow v-if="expandedFleets.includes(fleet.id)">
                                                                                        <TableCell colspan="10" class="p-0 bg-muted/20">
                                                                                            <div class="p-4 pl-12 rounded-b-lg">
                                                                                                <h4 class="mb-2 text-sm font-semibold text-muted-foreground flex items-center gap-2">
                                                                                                    <User class="h-4 w-4" />
                                                                                                    Daftar Supir
                                                                                                </h4>
                                                                                                <div class="rounded-md border bg-white">
                                                                                                    <Table>
                                                                                                        <TableHeader>
                                                                                                            <TableRow>
                                                                                                                <TableHead>Nama Supir</TableHead>
                                                                                                                <TableHead>Umur</TableHead>
                                                                                                                <TableHead>Expired SIM</TableHead>
                                                                                                                <TableHead>Status SIM</TableHead>
                                                                                                                <TableHead>Status Aktif</TableHead>
                                                                                                                <TableHead class="text-right">Aksi</TableHead>
                                                                                                            </TableRow>
                                                                                                        </TableHeader>
                                                                                                        <TableBody>
                                                                                                            <TableRow v-for="driver in fleet.drivers" :key="driver.id">
                                                                                                                <TableCell>{{ driver.name }}</TableCell>
                                                                                                                <TableCell>{{ driver.age }} Tahun</TableCell>
                                                                                                                <TableCell>{{ driver.sim_expiry_date }}</TableCell>
                                                                                                                <TableCell>{{ driver.sim_status }}</TableCell>
                                                                                                                <TableCell>
                                                                                                                    <Badge :variant="driver.is_active ? 'default' : 'secondary'">
                                                                                                                        {{ driver.is_active ? 'Aktif' : 'Non-aktif' }}
                                                                                                                    </Badge>
                                                                                                                </TableCell>
                                                                                                                <TableCell class="text-right">
                                                                                                                    <Button v-if="can('drivers.edit')" variant="ghost" size="sm" as-child>
                                                                                                                        <Link :href="route('admin.drivers.edit', driver.id)">
                                                                                                                            <Edit class="h-3 w-3 mr-1" />
                                                                                                                            Edit
                                                                                                                        </Link>
                                                                                                                    </Button>
                                                                                                                </TableCell>
                                                                                                            </TableRow>
                                                                                                            <TableRow v-if="!fleet.drivers || fleet.drivers.length === 0">
                                                                                                                <TableCell colspan="6" class="text-center text-muted-foreground py-4">
                                                                                                                    Tidak ada supir terdaftar di armada ini
                                                                                                                </TableCell>
                                                                                                            </TableRow>
                                                                                                        </TableBody>
                                                                                                    </Table>
                                                                                                </div>
                                                                                            </div>
                                                                                        </TableCell>
                                                                                    </TableRow>
                                                                                </template>
                                                                                <TableRow v-if="!agency.fleets || agency.fleets.length === 0">
                                                                                    <TableCell colspan="10" class="text-center text-muted-foreground py-4">
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
                                                    <TableRow v-if="!area.agencies || area.agencies.length === 0">
                                                        <TableCell colspan="4" class="text-center text-muted-foreground py-4">
                                                            Tidak ada agen terdaftar di area ini
                                                        </TableCell>
                                                    </TableRow>
                                                </TableBody>
                                            </Table>
                                        </div>
                                    </div>
                                </TableCell>
                            </TableRow>
                        </template>
                    </TableBody>

                    <TableEmpty v-if="page_data.areas.data.length === 0">
                        <div class="py-8 text-center">
                            <p class="mb-4 text-gray-500">Tidak ada data area</p>
                            <Button v-if="can('areas.create')" as-child>
                                <Link :href="route('admin.areas.create')">
                                    <Plus class="mr-2 h-4 w-4" />
                                    Tambah Area Pertama
                                </Link>
                            </Button>
                        </div>
                    </TableEmpty>
                </Table>

                <!-- Pagination -->
                <div class="mt-6">
                    <Pagination :data="page_data.areas" />
                </div>
            </CardContent>
        </Card>

        <!-- Delete Confirmation Dialog -->
        <AlertDialog v-model:open="showDeleteDialog">
            <template #title> Hapus Area </template>
            <template #description> Apakah Anda yakin ingin menghapus area "{{ deleteArea?.name }}"? Tindakan ini tidak dapat dibatalkan. </template>
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
