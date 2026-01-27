<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Area } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowLeft, ArrowRight, Plus, Check, AlertCircle } from 'lucide-vue-next';
import { ref, computed, onMounted, watch } from 'vue';

const props = defineProps<{
    page_setting: {
        title: string;
        subtitle: string;
        breadcrumbs: BreadcrumbItem[];
    };
    areas: Area[];
    agencyId: string | null;
}>();

const form = useForm({
    agency_id: '',
    license_plate: '',
    year_manufacture: '',
    keur_number: '',
    keur_expiry: '',
    keur_document: null as File | null,
    stnk_expiry: '',
    stnk_document: null as File | null,
    vehicle_expiry: '',
    is_active: true,
    drivers: [
        {
            name: '',
            age: '',
            sim_expiry: '',
            sim_document: null as File | null,
        },
        {
            name: '',
            age: '',
            sim_expiry: '',
            sim_document: null as File | null,
        },
    ],
});

const currentStep = ref(1);
const selectedAgency = ref<Area | null>(null);
const agencies = ref<Area[]>([]);
const isLoading = ref(false);
const errors = ref<Record<string, string[]>>({});
const successMessage = ref('');

// Parse URL parameters
onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search);
    const agencyId = urlParams.get('agency_id');
    if (agencyId) {
        form.agency_id = agencyId;
    }
    
    const step = urlParams.get('step');
    if (step) {
        currentStep.value = parseInt(step);
    }
});

// Watch for step changes
watch(currentStep, (newStep) => {
    if (newStep === 2) {
        loadSelectedAgency();
    }
});

const loadSelectedAgency = async () => {
    if (!form.agency_id) return;
    
    isLoading.value = true;
    try {
        const response = await fetch(`/admin/fleet-update/get-agencies?search=${form.agency_id}`);
        const data = await response.json();
        agencies.value = data;
        
        // Auto-select if only one agency matches
        if (agencies.value.length === 1) {
            selectedAgency.value = agencies.value[0];
        }
    } catch (error) {
        console.error('Error loading agencies:', error);
    } finally {
        isLoading.value = false;
    }
};

const filteredAgencies = computed(() => {
    if (!form.agency_id) return agencies.value;
    return agencies.value.filter(agency => 
        agency.name.toLowerCase().includes(form.agency_id.toLowerCase()) ||
        agency.area?.name.toLowerCase().includes(form.agency_id.toLowerCase())
    );
});

const addDriverField = () => {
    form.drivers.push({
        name: '',
        age: '',
        sim_expiry: '',
        sim_document: null,
    });
};

const removeDriverField = (index: number) => {
    form.drivers.splice(index, 1);
};

const searchAgencies = () => {
    loadSelectedAgency();
};

// Step validation
const validateStep = (step: number) => {
    errors.value = {};
    successMessage.value = '';
    
    if (step === 1) {
        if (!form.agency_id) {
            errors.value.agency_id = ['Silakan pilih agen terlebih dahulu'];
            return false;
        }
        
        if (!filteredAgencies.value.length) {
            errors.value.agency_id = ['Agen tidak ditemukan. Silakan buat agen baru atau periksa kembali.'];
            return false;
        }
        
        selectedAgency.value = filteredAgencies.value[0];
    } else if (step === 2) {
        return validateStep2();
    } else if (step === 3) {
        return validateStep3();
    }
    
    return true;
};

const validateStep2 = () => {
    errors.value = {};
    successMessage.value = '';
    
    if (!form.license_plate) {
        errors.value.license_plate = ['Nomor polisi wajib diisi'];
        return false;
    }
    
    if (!form.year_manufacture || form.year_manufacture < 1990 || form.year_manufacture > new Date().getFullYear()) {
        errors.value.year_manufacture = ['Tahun pembuatan harus antara 1990 dan ' + new Date().getFullYear()];
        return false;
    }
    
    if (!form.keur_expiry) {
        errors.value.keur_expiry = ['Tanggal KEUR wajib diisi'];
        return false;
    }
    
    if (!form.stnk_expiry) {
        errors.value.stnk_expiry = ['Tanggal STNK wajib diisi'];
        return false;
    }
    
    if (!form.vehicle_expiry) {
        errors.value.vehicle_expiry = ['Tanggal kadaluarsa kendaraan wajib diisi'];
        return false;
    }
    
    // Validate drivers
    const validDrivers = form.drivers.filter(driver => driver.name.trim() && driver.age && driver.sim_expiry);
    
    if (validDrivers.length === 0) {
        errors.value.drivers = ['Setidak ada data supir yang valid. Setiap supir harus memiliki nama, umur, dan masa berlaku SIM'];
        return false;
    }
    
    return true;
};

const validateStep3 = () => {
    errors.value = {};
    successMessage.value = '';
    
    if (!form.drivers[0].name || !form.drivers[0].age || !form.drivers[0].sim_expiry) {
        errors.value.drivers = ['Supir pertama harus memiliki nama, umur, dan masa berlaku SIM yang lengkap'];
        return false;
    }
    
    if (form.drivers[1].name || !form.drivers[1].age || !form.drivers[1].sim_expiry) {
        errors.value.drivers = ['Supir kedua harus memiliki nama, umur, dan masa berlaku SIM yang lengkap'];
        return false;
    }
    
    return true;
};

const submit = () => {
    if (!validateStep(currentStep.value)) return;
    
    form.post(route('admin.fleet-update.store'), {
        forceFormData: true,
        onSuccess: () => {
            successMessage.value = 'Data armada berhasil disimpan!';
            setTimeout(() => {
                window.location.href = route('admin.fleets.index');
            }, 2000);
        },
        onError: (err) => {
            errors.value = err;
        }
    });
};

const nextStep = () => {
    if (!validateStep(currentStep.value)) return;
    currentStep.value++;
};

const prevStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--;
    }
};

const goToStep = (step: number) => {
    currentStep.value = step;
};
</script>

<template>
    <Head :title="page_setting.title" />
    
    <AppLayout :page-setting="page_setting">
        <div class="space-y-6 p-container max-w-4xl mx-auto">
            <!-- Progress Indicator -->
            <Card class="shadow-smart-card">
                <CardContent class="py-4">
                    <div class="flex items-center justify-center">
                        <div class="flex items-center space-x-2">
                            <div 
                                class="w-8 h-1 rounded-full"
                                :class="{
                                    'bg-brand-primary': currentStep >= 1,
                                    'bg-gray-300': currentStep < 1
                                }"
                            ></div>
                            <div 
                                class="w-8 h-1 rounded-full"
                                :class="{
                                    'bg-brand-primary': currentStep >= 2,
                                    'bg-gray-300': currentStep < 2
                                }"
                            ></div>
                            <div 
                                class="w-8 h-1 rounded-full"
                                :class="{
                                    'bg-brand-primary': currentStep >= 3,
                                    'bg-gray-300': currentStep < 3
                                }"
                            ></div>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <h3 class="text-lg font-semibold">Langkah {{ currentStep }} dari 3 - {{ currentStep === 1 ? 'Pilih Agen' : currentStep === 2 ? 'Data Armada' : 'Data Supir' }}</h3>
                        <p class="text-text-secondary mt-2">Form wizard untuk menambah data armada LPG</p>
                    </div>
                </CardContent>
            </Card>

            <!-- Error Messages -->
            <Card v-if="Object.keys(errors).length > 0" class="shadow-smart-card border-red-200">
                <CardContent class="py-4">
                    <div class="flex items-center text-red-600">
                        <AlertCircle class="h-5 w-5" />
                        <span class="ml-2 font-medium">Terdapat kesalahan:</span>
                    </div>
                    <ul class="mt-2 list-disc list-inside text-red-600">
                        <li v-for="(messages, field) in errors" :key="field">
                            <strong>{{ field }}:</strong>
                            <span v-for="message in messages">{{ message }}</span>
                        </li>
                    </ul>
                </CardContent>
            </Card>

            <!-- Success Message -->
            <Card v-if="successMessage" class="shadow-smart-card border-green-200">
                <CardContent class="py-4">
                    <div class="flex items-center text-green-600">
                        <Check class="h-5 w-5" />
                        <span class="ml-2 font-medium">{{ successMessage }}</span>
                    </div>
                </CardContent>
            </Card>

            <!-- Step 1: Agency Selection -->
            <div v-show="currentStep === 1" class="space-y-6">
                <Card class="shadow-smart-card">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <span class="text-2xl font-bold">Langkah 1: Pilih Agen</span>
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full">1</span>
                        </CardTitle>
                        <CardDescription>
                            Pilih agen LPG yang akan menambahkan data armada
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <!-- Agency Selection -->
                        <div class="space-y-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Cari Agen LPG
                            </label>
                            <div class="relative">
                                <input
                                    v-model="form.agency_id"
                                    type="text"
                                    placeholder="Masukkan nama agen untuk mencari..."
                                    class="w-full px-3 py-2 rounded-smart-md border-input shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    @input="searchAgencies"
                                    :disabled="isLoading"
                                />
                                <div v-if="isLoading" class="absolute right-3 top-1/2 transform -translate-y-1/2">
                                    <div class="animate-spin rounded-full h-4 w-4 border-2 border-blue-500 border-t-transparent"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Selected Agency Info -->
                        <div v-if="selectedAgency" class="mt-6 p-4 bg-blue-50 rounded-smart-md border border-blue-200">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center text-white font-bold">
                                        {{ selectedAgency.name.charAt(0).toUpperCase() }}
                                    </div>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900">{{ selectedAgency.name }}</div>
                                    <div class="text-sm text-gray-500">{{ selectedAgency.area?.name }}</div>
                                    <div class="text-sm text-gray-500">{{ selectedAgency.active_fleets_count || 0 }} armada aktif</div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Step 2: Fleet Data -->
            <div v-show="currentStep === 2" class="space-y-6">
                <Card class="shadow-smart-card">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Button variant="outline" size="sm" @click="prevStep">
                                <ArrowLeft class="h-4 w-4" />
                            </Button>
                            <span class="text-2xl font-bold">Langkah 2: Data Armada</span>
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full">2</span>
                        </CardTitle>
                        <CardDescription>
                            Masukkan data armada dan dokumen kendaraan
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <div class="space-y-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Agen Terpilih: <span v-if="selectedAgency" class="font-semibold text-blue-600">{{ selectedAgency.name }}</span>
                                <span v-else class="text-gray-500">{{ form.agency_id || 'Belum ada agen yang dipilih' }}</span>
                            </label>
                            
                            <!-- Fleet Form Fields -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Polisi *</label>
                                    <input
                                        v-model="form.license_plate"
                                        type="text"
                                        placeholder="Contoh: BG 1234 ABC"
                                        class="w-full px-3 py-2 rounded-smart-md border-input shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        :class="{ 'border-red-500': errors.license_plate && errors.license_plate.length > 0 }"
                                    />
                                    <p v-if="errors.license_plate" class="mt-1 text-sm text-red-600">
                                        {{ errors.license_plate.join(', ') }}
                                    </p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Pembuatan *</label>
                                    <input
                                        v-model="form.year_manufacture"
                                        type="number"
                                        placeholder="Contoh: 2020"
                                        class="w-full px-3 py-2 rounded-smart-md border-input shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        :class="{ 'border-red-500': errors.year_manufacture && errors.year_manufacture.length > 0 }"
                                    />
                                    <p v-if="errors.year_manufacture" class="mt-1 text-sm text-red-600">
                                        {{ errors.year_manufacture.join(', ') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nomor KEUR *</label>
                            <input
                                v-model="form.keur_number"
                                        type="text"
                                placeholder="Contoh: KEUR-123456"
                                        class="w-full px-3 py-2 rounded-smart-md border-input shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        :class="{ 'border-red-500': errors.keur_number && errors.keur_number.length > 0 }"
                                    />
                                    <p v-if="errors.keur_number" class="mt-1 text-sm text-red-600">
                                        {{ errors.keur_number.join(', ') }}
                                    </p>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Berlaku KEUR *</label>
                                    <input
                                        v-model="form.keur_expiry"
                                        type="date"
                                        class="w-full px-3 py-2 rounded-smart-md border-input shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        :class="{ 'border-red-500': errors.keur_expiry && errors.keur_expiry.length > 0 }"
                                    />
                                    <p v-if="errors.keur_expiry" class="mt-1 text-sm text-red-600">
                                        {{ errors.keur_expiry.join(', ') }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload Dokumen KEUR *</label>
                                    <input
                                        type="file"
                                        @input="form.keur_document = $event.target.files[0]"
                                        class="w-full px-3 py-2 rounded-smart-md border-input shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        accept=".pdf,.jpg,.jpeg,.png"
                                    />
                                    <p v-if="errors.keur_document" class="mt-1 text-sm text-red-600">
                                        {{ errors.keur_document.join(', ') }}
                                    </p>
                                </div>
                            </div>

                        <div class="space-y-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Berlaku STNK *</label>
                            <input
                                v-model="form.stnk_expiry"
                                        type="date"
                                        class="w-full px-3 py-2 rounded-smart-md border-input shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        :class="{ 'border-red-500': errors.stnk_expiry && errors.stnk_expiry.length > 0 }"
                                    />
                                    <p v-if="errors.stnk_expiry" class="mt-1 text-sm text-red-600">
                                        {{ errors.stnk_expiry.join(', ') }}
                                    </p>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Kadaluarsa Kendaraan *</label>
                                    <input
                                        v-model="form.vehicle_expiry"
                                        type="date"
                                        class="w-full px-3 py-2 rounded-smart-md border-input shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        :class="{ 'border-red-500': errors.vehicle_expiry && errors.vehicle_expiry.length > 0 }"
                                    />
                                    <p v-if="errors.vehicle_expiry" class="mt-1 text-sm text-red-600">
                                        {{ errors.vehicle_expiry.join(', ') }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload Dokumen STNK *</label>
                                    <input
                                        type="file"
                                        @input="form.stnk_document = $event.target.files[0]"
                                        class="w-full px-3 py-2 rounded-smart-md border-input shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        accept=".pdf,.jpg,.jpeg,.png"
                                    />
                                    <p v-if="errors.stnk_document" class="mt-1 text-sm text-red-600">
                                        {{ errors.stnk_document.join(', ') }}
                                    </p>
                                </div>
                            </div>

                        <div class="flex items-center">
                            <label class="flex items-center space-x-2">
                                <input v-model="form.is_active" type="checkbox" id="is_active" class="rounded border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-500" />
                                <span class="text-sm font-medium text-gray-700">Status Aktif</span>
                            </label>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Step 3: Driver Data -->
            <div v-show="currentStep === 3" class="space-y-6">
                <Card class="shadow-smart-card">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Button variant="outline" size="sm" @click="prevStep">
                                <ArrowLeft class="h-4 w-4" />
                            </Button>
                            <span class="text-2xl font-bold">Langkah 3: Data Supir</span>
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full">3</span>
                        </CardTitle>
                        <CardDescription>
                            Masukkan data supir dan masa berlaku SIM
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <div v-for="(driver, index) in form.drivers" :key="index" class="space-y-6 p-6 bg-gray-50 rounded-smart-md">
                            <div class="flex justify-between items-center mb-4">
                                <div class="text-lg font-medium">Supir {{ index + 1 }}</div>
                                <Button 
                                    variant="outline"
                                    size="sm"
                                    @click="removeDriverField(index)"
                                    class="text-red-500 hover:bg-red-100"
                                >
                                    Hapus
                                </Button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Supir *</label>
                                    <input
                                        v-model="driver.name"
                                        type="text"
                                        placeholder="Masukkan nama supir"
                                        class="w-full px-3 py-2 rounded-smart-md border-input shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        :class="{ 'border-red-500': errors.drivers && errors.drivers[`drivers.${index}.name`] && errors.drivers[`drivers.${index}.name`].length > 0 }"
                                    />
                                    <p v-if="errors.drivers && errors.drivers[`drivers.${index}.name`]" class="mt-1 text-sm text-red-600">
                                        {{ errors.drivers[`drivers.${index}.name`].join(', ') }}
                                    </p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Umur *</label>
                                    <input
                                        v-model="driver.age"
                                        type="number"
                                        placeholder="Umur supir"
                                        min="18"
                                        max="65"
                                        class="w-full px-3 py-2 rounded-smart-md border-input shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        :class="{ 'border-red-500': errors.drivers && errors.drivers[`drivers.${index}.age`] && errors.drivers[`drivers.${index}.age`].length > 0 }"
                                    />
                                    <p v-if="errors.drivers && errors.drivers[`drivers.${index}.age`]" class="mt-1 text-sm text-red-600">
                                        {{ errors.drivers[`drivers.${index}.age`].join(', ') }}
                                    </p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Masa Berlaku SIM *</label>
                                    <input
                                        v-model="driver.sim_expiry"
                                        type="date"
                                        class="w-full px-3 py-2 rounded-smart-md border-input shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        :class="{ 'border-red-500': errors.drivers && errors.drivers[`drivers.${index}.sim_expiry`] && errors.drivers[`drivers.${index}.sim_expiry`].length > 0 }"
                                    />
                                    <p v-if="errors.drivers && errors.drivers[`drivers.${index}.sim_expiry`]" class="mt-1 text-sm text-red-600">
                                        {{ errors.drivers[`drivers.${index}.sim_expiry`].join(', ') }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload Dokumen SIM *</label>
                                    <input
                                        type="file"
                                        @input="form.drivers[index].sim_document = $event.target.files[0]"
                                        class="w-full px-3 py-2 rounded-smart-md border-input shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        accept=".pdf,.jpg,.jpeg,.png"
                                    />
                                    <p v-if="errors.drivers && errors.drivers[`drivers.${index}.sim_document`]" class="mt-1 text-sm text-red-600">
                                        {{ errors.drivers[`drivers.${index}.sim_document`].join(', ') }}
                                    </p>
                                </div>
                            <div class="flex items-center">
                                <label class="flex items-center space-x-2">
                                    <input v-model="driver.is_active" type="checkbox" :id="`driver_is_active_${index}`" class="rounded border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-500" />
                                    <span class="text-sm font-medium text-gray-700">Status Aktif</span>
                                </label>
                            </div>
                        </div>

                        <!-- Add New Driver Button -->
                        <div class="mt-4">
                            <Button 
                                variant="outline"
                                class="w-full"
                                @click="addDriverField"
                            >
                                <Plus class="h-4 w-4 mr-2" />
                                Tambah Supir
                            </Button>
                        </div>
                </CardContent>
            </Card>
            </div>

            <!-- Navigation -->
            <div class="flex justify-between">
                <Button variant="outline" @click="goToStep(1)">
                    <ArrowLeft class="h-4 w-4 mr-2" />
                    Kembali ke Pilih Agen
                </Button>
                
                <Button 
                    v-if="currentStep < 3"
                    @click="nextStep"
                    class="flex items-center"
                >
                    Lanjut
                    <ArrowRight class="h-4 w-4 ml-2" />
                </Button>

                <Button 
                    v-if="currentStep === 3"
                    @click="submit"
                    :disabled="isLoading"
                    class="flex items-center"
                >
                    <span v-if="isLoading">
                        <div class="animate-spin rounded-full h-4 w-4 border-2 border-blue-500 border-t-transparent mr-2"></div>
                        Menyimpan...
                    </span>
                    <span v-else>
                        <Check class="h-4 w-4 mr-2" />
                        Simpan Data
                    </span>
                </Button>
            </div>
        </div>
    </AppLayout>
</template>