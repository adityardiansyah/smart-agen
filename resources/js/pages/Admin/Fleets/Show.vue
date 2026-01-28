<script setup lang="ts">
import { AlertDialog } from '@/components/ui/alert-dialog';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableEmpty, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { usePermission } from '@/composables/usePermission';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Fleet, Region } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Plus, User, History, Truck, Building2, FileText, Image } from 'lucide-vue-next';
import { ref, watch } from 'vue';

interface Driver {
    id: number;
    name: string;
    age: number;
    sim_expiry: string;
    sim_status?: string;
    sim_document?: string;
    sim_document_url?: string;
    assigned_at?: string;
    deactivated_at?: string;
}

interface InactiveDriver {
    id: number;
    name: string;
    fleet_id: number;
}

const props = defineProps<{
    page_setting: {
        title: string;
        subtitle: string;
        breadcrumbs: BreadcrumbItem[];
    };
    fleet: {
        data: Fleet;
    };
    region: {
        data: Region;
    };
    activeDriver: Driver | null;
    driverHistory: Driver[];
    inactiveDrivers: InactiveDriver[];
}>();

const { can } = usePermission();

// Add Driver Dialog
const showAddDriverDialog = ref(false);
const driverMode = ref<'new' | 'existing'>('new');
const newDriverForm = useForm({
    name: '',
    age: '',
    sim_expiry: '',
});
const existingDriverId = ref<number | null>(null);

const submitAddDriver = () => {
    if (driverMode.value === 'new') {
        router.post(route('admin.fleets.assign-driver', { fleet: props.fleet.data.id }), {
            mode: 'new',
            name: newDriverForm.name,
            age: newDriverForm.age,
            sim_expiry: newDriverForm.sim_expiry,
        }, {
            onSuccess: () => {
                showAddDriverDialog.value = false;
                newDriverForm.reset();
            },
        });
    } else {
        router.post(route('admin.fleets.assign-driver', { fleet: props.fleet.data.id }), {
            mode: 'existing',
            driver_id: existingDriverId.value,
        }, {
            onSuccess: () => {
                showAddDriverDialog.value = false;
                existingDriverId.value = null;
            },
        });
    }
};

const getSimStatusColor = (status: string) => {
    switch (status) {
        case 'NEAR EXPIRY':
            return 'bg-orange-100 text-orange-800';
        case 'EXPIRED':
            return 'bg-red-100 text-red-800';
        case 'NOT EXPIRED':
            return 'bg-green-100 text-green-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};

// Document Preview Modal
const showDocumentModal = ref(false);
const documentModalTitle = ref('');
const documentModalUrl = ref('');

const openDocumentPreview = (title: string, url: string) => {
    documentModalTitle.value = title;
    documentModalUrl.value = url;
    showDocumentModal.value = true;
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
                            <div>
                                <CardTitle class="flex items-center gap-2">
                                    <Truck class="h-5 w-5" />
                                    {{ fleet.data.formatted_license_plate }}
                                </CardTitle>
                                <CardDescription>Detail armada dan histori supir</CardDescription>
                            </div>
                            <Badge :variant="fleet.data.is_active ? 'default' : 'secondary'">
                                {{ fleet.data.is_active ? 'Aktif' : 'Non-aktif' }}
                            </Badge>
                        </div>
                        <Button variant="outline" size="sm" as-child>
                            <Link :href="route('admin.fleets.index')">
                                <ArrowLeft class="mr-2 h-4 w-4" />
                                Kembali
                            </Link>
                        </Button>
                    </div>
                </CardHeader>
            </Card>

            <!-- Fleet & Agency Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Building2 class="h-5 w-5" />
                            Informasi Agen
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Agen:</span>
                            <span class="font-medium">{{ fleet.data.agency?.name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Alamat:</span>
                            <span class="font-medium">{{ fleet.data.agency?.address }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Kabupaten:</span>
                            <span class="font-medium">{{ fleet.data.region?.city }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Provinsi:</span>
                            <span class="font-medium">{{ fleet.data.agency?.area?.name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Wilayah SBM:</span>
                            <span class="font-medium">{{ fleet.data.region?.region_sbm }}</span>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Truck class="h-5 w-5" />
                            Informasi Armada
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Tahun Pembuatan:</span>
                            <span class="font-medium">{{ fleet.data.year_manufacture }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Nomor KEUR:</span>
                            <span class="font-medium">{{ fleet.data.keur_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Masa Berlaku KEUR:</span>
                            <span class="font-medium">{{ fleet.data.keur_expiry }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-muted-foreground">Status KEUR:</span>
                            <Badge :class="fleet.data.keur_status === 'NEAR EXPIRY' ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800'">
                                {{ fleet.data.keur_status }}
                            </Badge>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Masa Berlaku STNK:</span>
                            <span class="font-medium">{{ fleet.data.stnk_expiry }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-muted-foreground">Dokumen KEUR:</span>
                            <Button
                                v-if="fleet.data.keur_document_url"
                                variant="outline"
                                size="sm"
                                @click="openDocumentPreview('Dokumen KEUR', fleet.data.keur_document_url)"
                            >
                                <Image class="mr-2 h-4 w-4" /> Lihat Dokumen
                            </Button>
                            <span v-else class="text-sm text-muted-foreground italic">Tidak ada</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-muted-foreground">Dokumen STNK:</span>
                            <Button
                                v-if="fleet.data.stnk_document_url"
                                variant="outline"
                                size="sm"
                                @click="openDocumentPreview('Dokumen STNK', fleet.data.stnk_document_url)"
                            >
                                <Image class="mr-2 h-4 w-4" /> Lihat Dokumen
                            </Button>
                            <span v-else class="text-sm text-muted-foreground italic">Tidak ada</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-muted-foreground">Status Usia:</span>
                            <Badge :class="fleet.data.vehicle_age_status === 'NEAR EXPIRED' ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800'">
                                {{ fleet.data.vehicle_age_status }}
                            </Badge>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Masa Berlaku Kendaraan:</span>
                            <span class="font-medium">{{ fleet.data.vehicle_expiry }}</span>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Active Driver -->
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle class="flex items-center gap-2">
                                <User class="h-5 w-5" />
                                Supir Aktif
                            </CardTitle>
                            <CardDescription>Supir yang saat ini bertugas pada armada ini</CardDescription>
                        </div>
                        <Button v-if="can('drivers.create')" @click="showAddDriverDialog = true">
                            <Plus class="mr-2 h-4 w-4" />
                            Tambah Supir
                        </Button>
                    </div>
                </CardHeader>
                <CardContent>
                    <div v-if="activeDriver" class="p-4 bg-green-50 rounded-lg border border-green-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-semibold text-lg">{{ activeDriver.name }}</div>
                                <div class="text-sm text-muted-foreground">
                                    Umur: {{ activeDriver.age }} tahun | SIM berlaku: {{ activeDriver.sim_expiry }}
                                </div>
                                <div class="text-sm text-muted-foreground">
                                    Ditugaskan: {{ activeDriver.assigned_at || 'N/A' }}
                                </div>
                            </div>
                            <Badge :class="getSimStatusColor(activeDriver.sim_status || 'NOT EXPIRED')">
                                {{ activeDriver.sim_status || 'NOT EXPIRED' }}
                            </Badge>
                            <Button
                                v-if="activeDriver.sim_document_url"
                                variant="outline"
                                size="sm"
                                @click="openDocumentPreview('Dokumen SIM - ' + activeDriver.name, activeDriver.sim_document_url)"
                            >
                                <Image class="mr-2 h-4 w-4" /> Lihat SIM
                            </Button>
                        </div>
                    </div>
                    <div v-else class="p-8 text-center border-2 border-dashed rounded-lg">
                        <User class="h-12 w-12 mx-auto text-muted-foreground mb-4" />
                        <p class="text-muted-foreground mb-4">Belum ada supir yang ditugaskan</p>
                        <Button v-if="can('drivers.create')" @click="showAddDriverDialog = true">
                            <Plus class="mr-2 h-4 w-4" />
                            Tambah Supir Pertama
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <!-- Driver History -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <History class="h-5 w-5" />
                        Histori Supir
                    </CardTitle>
                    <CardDescription>Daftar supir yang pernah bertugas pada armada ini</CardDescription>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Nama</TableHead>
                                <TableHead>Umur</TableHead>
                                <TableHead>Masa Berlaku SIM</TableHead>
                                <TableHead>Tanggal Ditugaskan</TableHead>
                                <TableHead>Tanggal Berakhir</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="driver in driverHistory" :key="driver.id">
                                <TableCell class="font-medium">{{ driver.name }}</TableCell>
                                <TableCell>{{ driver.age }} tahun</TableCell>
                                <TableCell>{{ driver.sim_expiry }}</TableCell>
                                <TableCell>{{ driver.assigned_at || '-' }}</TableCell>
                                <TableCell>{{ driver.deactivated_at || '-' }}</TableCell>
                            </TableRow>
                        </TableBody>
                        <TableEmpty v-if="driverHistory.length === 0">
                            <div class="py-8 text-center">
                                <p class="text-muted-foreground">Belum ada histori supir</p>
                            </div>
                        </TableEmpty>
                    </Table>
                </CardContent>
            </Card>
        </div>

        <!-- Add Driver Dialog -->
        <Dialog v-model:open="showAddDriverDialog">
            <DialogContent class="max-w-md">
                <DialogHeader>
                    <DialogTitle>Tambah Supir</DialogTitle>
                    <DialogDescription>
                        Pilih untuk menambahkan supir baru atau menggunakan supir yang sudah ada.
                    </DialogDescription>
                </DialogHeader>
                
                <!-- Mode Selection -->
                <div class="flex gap-4 mb-4">
                    <Button
                        :variant="driverMode === 'new' ? 'default' : 'outline'"
                        @click="driverMode = 'new'"
                        class="flex-1"
                    >
                        Supir Baru
                    </Button>
                    <Button
                        :variant="driverMode === 'existing' ? 'default' : 'outline'"
                        @click="driverMode = 'existing'"
                        class="flex-1"
                        :disabled="inactiveDrivers.length === 0"
                    >
                        Supir Existing
                    </Button>
                </div>

                <form @submit.prevent="submitAddDriver" class="space-y-4">
                    <!-- New Driver Form -->
                    <div v-if="driverMode === 'new'" class="space-y-4">
                        <div class="space-y-2">
                            <Label for="name">Nama Supir *</Label>
                            <Input
                                id="name"
                                v-model="newDriverForm.name"
                                placeholder="Masukkan nama supir"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label for="age">Umur *</Label>
                            <Input
                                id="age"
                                v-model="newDriverForm.age"
                                type="number"
                                placeholder="Umur supir"
                                min="18"
                                max="65"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label for="sim_expiry">Masa Berlaku SIM *</Label>
                            <Input
                                id="sim_expiry"
                                v-model="newDriverForm.sim_expiry"
                                type="date"
                            />
                        </div>
                    </div>

                    <!-- Existing Driver Selection -->
                    <div v-else class="space-y-4">
                        <div class="space-y-2">
                            <Label for="existing_driver">Pilih Supir Existing *</Label>
                            <Select
                                v-model="existingDriverId"
                                :options="inactiveDrivers.map(d => ({ value: d.id, label: d.name }))"
                                placeholder="Pilih supir..."
                            />
                        </div>
                        <p class="text-sm text-muted-foreground">
                            Supir yang dipilih akan diaktifkan kembali dan ditugaskan ke armada ini.
                        </p>
                    </div>

                    <DialogFooter>
                        <Button type="button" variant="outline" @click="showAddDriverDialog = false">
                            Batal
                        </Button>
                        <Button type="submit">
                            Simpan
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Document Preview Modal -->
        <Dialog v-model:open="showDocumentModal">
            <DialogContent class="max-w-3xl">
                <DialogHeader>
                    <DialogTitle>{{ documentModalTitle }}</DialogTitle>
                    <DialogDescription>
                        Pratinjau dokumen
                    </DialogDescription>
                </DialogHeader>
                <div class="flex items-center justify-center p-4 bg-gray-50 rounded-lg min-h-[400px]">
                    <img 
                        :src="documentModalUrl" 
                        :alt="documentModalTitle"
                        class="max-w-full max-h-[60vh] object-contain rounded-md shadow-sm"
                    />
                </div>
                <DialogFooter>
                    <Button variant="outline" @click="showDocumentModal = false">
                        Tutup
                    </Button>
                    <Button as-child>
                        <a :href="documentModalUrl" target="_blank" rel="noopener noreferrer">
                            Buka di Tab Baru
                        </a>
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
