<script setup lang="ts">
import { AlertDialog } from '@/components/ui/alert-dialog';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Table, TableBody, TableCell, TableEmpty, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { usePermission } from '@/composables/usePermission';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Area, BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Edit, Plus, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';

interface Region {
    id: number;
    city: string;
    region_sbm: string;
    agencies_count: number;
}

const props = defineProps<{
    page_setting: {
        title: string;
        subtitle: string;
        breadcrumbs: BreadcrumbItem[];
    };
    area: {
        data: Area & {
            regions?: Region[];
            agencies?: any[];
        };
    };
}>();

const { can } = usePermission();

// Add Region Dialog
const showAddDialog = ref(false);
const addForm = useForm({
    city: '',
    region_sbm: '',
});

const submitAddRegion = () => {
    addForm.post(route('admin.areas.regions.store', { area: props.area.data.id }), {
        onSuccess: () => {
            showAddDialog.value = false;
            addForm.reset();
        },
    });
};

// Delete Region Dialog
const deleteRegion = ref<Region | null>(null);
const showDeleteDialog = ref(false);

const confirmDelete = () => {
    if (deleteRegion.value) {
        router.delete(route('admin.regions.destroy', { region: deleteRegion.value.id }), {
            onSuccess: () => {
                showDeleteDialog.value = false;
                deleteRegion.value = null;
            },
        });
    }
};
</script>

<template>
    <Head :title="page_setting.title" />

    <AppLayout :page-setting="page_setting">
        <div class="space-y-6">
            <!-- Back Button and Header -->
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <Button variant="outline" size="sm" as-child>
                                <Link :href="route('admin.areas.index')">
                                    <ArrowLeft class="mr-2 h-4 w-4" />
                                    Kembali
                                </Link>
                            </Button>
                            <div>
                                <CardTitle class="flex items-center gap-2">
                                    {{ area.data.name }}
                                    <Badge variant="secondary">{{ area.data.formatted_code }}</Badge>
                                </CardTitle>
                                <CardDescription>Detail area dan manajemen region</CardDescription>
                            </div>
                        </div>
                        <Badge :variant="area.data.is_active ? 'default' : 'secondary'">
                            {{ area.data.is_active ? 'Aktif' : 'Non-aktif' }}
                        </Badge>
                    </div>
                </CardHeader>
            </Card>

            <!-- Regions Section -->
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle>Daftar Region</CardTitle>
                            <CardDescription>Kelola kabupaten/kota dalam area ini</CardDescription>
                        </div>
                        <Button v-if="can('areas.edit')" @click="showAddDialog = true">
                            <Plus class="mr-2 h-4 w-4" />
                            Tambah Region
                        </Button>
                    </div>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Kabupaten/Kota</TableHead>
                                <TableHead>Wilayah SBM</TableHead>
                                <TableHead>Jumlah Agen</TableHead>
                                <TableHead class="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="region in area.data.regions" :key="region.id">
                                <TableCell class="font-medium">{{ region.city }}</TableCell>
                                <TableCell>{{ region.region_sbm }}</TableCell>
                                <TableCell>{{ region.agencies_count }}</TableCell>
                                <TableCell class="text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <Button
                                            v-if="can('areas.delete')"
                                            variant="outline"
                                            size="sm"
                                            @click="
                                                () => {
                                                    deleteRegion = region;
                                                    showDeleteDialog = true;
                                                }
                                            "
                                            :disabled="region.agencies_count > 0"
                                            :title="region.agencies_count > 0 ? 'Tidak dapat dihapus (memiliki agen)' : 'Hapus region'"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                        </TableBody>

                        <TableEmpty v-if="!area.data.regions || area.data.regions.length === 0">
                            <div class="py-8 text-center">
                                <p class="mb-4 text-gray-500">Belum ada region untuk area ini</p>
                                <Button v-if="can('areas.edit')" @click="showAddDialog = true">
                                    <Plus class="mr-2 h-4 w-4" />
                                    Tambah Region Pertama
                                </Button>
                            </div>
                        </TableEmpty>
                    </Table>
                </CardContent>
            </Card>

            <!-- Agencies Section -->
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle>Daftar Agen</CardTitle>
                            <CardDescription>Agen-agen yang terdaftar di area ini</CardDescription>
                        </div>
                    </div>
                </CardHeader>
                <CardContent>
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
                            <TableRow v-for="agency in area.data.agencies" :key="agency.id">
                                <TableCell class="font-medium">{{ agency.name }}</TableCell>
                                <TableCell>{{ agency.address }}</TableCell>
                                <TableCell>{{ agency.fleets_count }}</TableCell>
                                <TableCell class="text-right">
                                    <Button v-if="can('agencies.edit')" variant="outline" size="sm" as-child>
                                        <Link :href="route('admin.agencies.edit', { agency: agency.id })">
                                            <Edit class="h-4 w-4" />
                                        </Link>
                                    </Button>
                                </TableCell>
                            </TableRow>
                        </TableBody>

                        <TableEmpty v-if="!area.data.agencies || area.data.agencies.length === 0">
                            <div class="py-8 text-center">
                                <p class="text-gray-500">Belum ada agen untuk area ini</p>
                            </div>
                        </TableEmpty>
                    </Table>
                </CardContent>
            </Card>
        </div>

        <!-- Add Region Dialog -->
        <Dialog v-model:open="showAddDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Tambah Region Baru</DialogTitle>
                    <DialogDescription>
                        Tambahkan kabupaten/kota baru ke area {{ area.data.name }}
                    </DialogDescription>
                </DialogHeader>
                <form @submit.prevent="submitAddRegion" class="space-y-4">
                    <div class="space-y-2">
                        <Label for="city">Kabupaten/Kota *</Label>
                        <Input
                            id="city"
                            v-model="addForm.city"
                            placeholder="Contoh: Kota Palembang"
                            :class="{ 'border-red-500': addForm.errors.city }"
                        />
                        <p v-if="addForm.errors.city" class="text-sm text-red-600">
                            {{ addForm.errors.city }}
                        </p>
                    </div>
                    <div class="space-y-2">
                        <Label for="region_sbm">Wilayah SBM *</Label>
                        <Input
                            id="region_sbm"
                            v-model="addForm.region_sbm"
                            placeholder="Contoh: Sumsel 1"
                            :class="{ 'border-red-500': addForm.errors.region_sbm }"
                        />
                        <p v-if="addForm.errors.region_sbm" class="text-sm text-red-600">
                            {{ addForm.errors.region_sbm }}
                        </p>
                    </div>
                    <DialogFooter>
                        <Button type="button" variant="outline" @click="showAddDialog = false">
                            Batal
                        </Button>
                        <Button type="submit" :disabled="addForm.processing">
                            Simpan
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Delete Confirmation Dialog -->
        <AlertDialog v-model:open="showDeleteDialog">
            <template #title>Hapus Region</template>
            <template #description>
                Apakah Anda yakin ingin menghapus region "{{ deleteRegion?.city }}"? Tindakan ini tidak dapat dibatalkan.
            </template>
            <template #cancel>
                <Button variant="outline" @click="showDeleteDialog = false">Batal</Button>
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
