<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save } from 'lucide-vue-next';

const props = defineProps<{
    page_setting: {
        title: string;
        subtitle: string;
        breadcrumbs: BreadcrumbItem[];
    };
}>();

const form = useForm({
    name: '',
    code: '',
    is_active: true,
});

const submit = () => {
    form.post(route('admin.areas.store'), {
        onSuccess: () => {
            router.visit(route('admin.areas.index'));
        },
    });
};

const formatCode = (event: Event) => {
    const target = event.target as HTMLInputElement;
    form.code = target.value.toUpperCase();
};
</script>

<template>
    <Head :title="page_setting.title" />

    <AppLayout :page-setting="page_setting">
        <Card class="mx-auto max-w-2xl">
            <CardHeader>
                <div class="flex items-center gap-4">
                    <Button variant="outline" size="sm" as-child>
                        <Link :href="route('admin.areas.index')">
                            <ArrowLeft class="mr-2 h-4 w-4" />
                            Kembali
                        </Link>
                    </Button>
                    <div>
                        <CardTitle>Tambah Area Baru</CardTitle>
                        <CardDescription> Tambahkan wilayah operasional baru untuk SMART AGEN LPG </CardDescription>
                    </div>
                </div>
            </CardHeader>

            <CardContent>
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="name">Nama Area *</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                placeholder="Contoh: Sumatra Selatan"
                                :class="{ 'border-red-500': form.errors.name }"
                                required
                            />
                            <p v-if="form.errors.name" class="text-sm text-red-600">
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="code">Kode Area *</Label>
                            <Input
                                id="code"
                                v-model="form.code"
                                placeholder="Contoh: SS"
                                maxlength="10"
                                @input="formatCode"
                                :class="{ 'border-red-500': form.errors.code }"
                                required
                            />
                            <p v-if="form.errors.code" class="text-sm text-red-600">
                                {{ form.errors.code }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2">
                        <Switch id="is_active" v-model:checked="form.is_active" />
                        <Label for="is_active">Aktif</Label>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <Button type="button" variant="outline" as-child>
                            <Link :href="route('admin.areas.index')">Batal</Link>
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                            <Save class="mr-2 h-4 w-4" />
                            {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </AppLayout>
</template>
