<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Driver } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, User, Truck, Building2, Image, Calendar, Info } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    page_setting: {
        title: string;
        subtitle: string;
        breadcrumbs: BreadcrumbItem[];
    };
    driver: {
        data: Driver;
    };
}>();

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
                                    <User class="h-5 w-5" />
                                    {{ driver.data.name }}
                                </CardTitle>
                                <CardDescription>Profil Lengkap Supir</CardDescription>
                            </div>
                            <Badge :variant="driver.data.is_active ? 'default' : 'secondary'">
                                {{ driver.data.is_active ? 'Aktif' : 'Non-aktif' }}
                            </Badge>
                        </div>
                        <Button variant="outline" size="sm" as-child>
                            <Link :href="route('admin.fleets.show', { fleet: driver.data.fleet?.id || 0 })">
                                <ArrowLeft class="mr-2 h-4 w-4" />
                                Kembali ke Armada
                            </Link>
                        </Button>
                    </div>
                </CardHeader>
            </Card>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Driver Info Card -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Info class="h-5 w-5" />
                            Informasi Pribadi
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="flex justify-between items-center py-2 border-b">
                            <span class="text-muted-foreground">Nama Lengkap:</span>
                            <span class="font-medium text-lg">{{ driver.data.name }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b">
                            <span class="text-muted-foreground">Umur:</span>
                            <span class="font-medium">{{ driver.data.age }} Tahun</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b">
                            <span class="text-muted-foreground">Masa Berlaku SIM:</span>
                            <span class="font-medium">{{ driver.data.sim_expiry }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b">
                            <span class="text-muted-foreground">Status SIM:</span>
                            <Badge :class="getSimStatusColor(driver.data.sim_status || 'NOT EXPIRED')">
                                {{ driver.data.sim_status || 'NOT EXPIRED' }}
                            </Badge>
                        </div>
                        <div class="flex justify-between items-center pt-2">
                            <span class="text-muted-foreground text-sm">Dokumen SIM:</span>
                            <Button 
                                v-if="driver.data.sim_document_url" 
                                variant="outline" 
                                size="sm"
                                @click="openDocumentPreview('Dokumen SIM - ' + driver.data.name, driver.data.sim_document_url)"
                            >
                                <Image class="mr-2 h-4 w-4" />
                                Lihat Dokumen
                            </Button>
                            <span v-else class="text-sm text-muted-foreground italic">Dokumen tidak tersedia</span>
                        </div>
                    </CardContent>
                </Card>

                <!-- Assignment Info Card -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Truck class="h-5 w-5" />
                            Informasi Armada & Penugasan
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div v-if="driver.data.fleet" class="space-y-4">
                            <div class="flex justify-between items-center py-2 border-b">
                                <span class="text-muted-foreground">Armada:</span>
                                <Link 
                                    :href="route('admin.fleets.show', driver.data.fleet.id)"
                                    class="font-medium text-brand-primary hover:underline flex items-center gap-1"
                                >
                                    {{ driver.data.fleet.formatted_license_plate }}
                                    <Truck class="h-3 w-3" />
                                </Link>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b">
                                <span class="text-muted-foreground">Nama Agen:</span>
                                <span class="font-medium">{{ driver.data.fleet.agency?.name }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b">
                                <span class="text-muted-foreground">Wilayah:</span>
                                <span class="font-medium">{{ driver.data.fleet.agency?.area?.name }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b">
                                <span class="text-muted-foreground text-sm flex items-center gap-1">
                                    <Calendar class="h-3 w-3" /> Tanggal Ditugaskan:
                                </span>
                                <span class="text-sm">{{ driver.data.assigned_at || '-' }}</span>
                            </div>
                        </div>
                        <div v-else class="flex flex-col items-center justify-center py-8 text-center text-muted-foreground bg-gray-50 rounded-lg">
                            <Truck class="h-8 w-8 mb-2 opacity-50" />
                            <p>Supir ini sedang tidak ditugaskan ke armada manapun.</p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>

        <!-- Document Preview Modal -->
        <Dialog v-model:open="showDocumentModal">
            <DialogContent class="max-w-3xl">
                <DialogHeader>
                    <DialogTitle>{{ documentModalTitle }}</DialogTitle>
                    <DialogDescription>
                        Pratinjau dokumen SIM
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
