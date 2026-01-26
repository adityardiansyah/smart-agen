<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Link } from '@inertiajs/vue3';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="flex min-h-svh flex-col items-center justify-center gap-6 bg-bg-main p-container md:p-10">
        <div class="flex w-full max-w-md flex-col gap-6">
            <Link :href="route('home')" class="flex items-center gap-2 self-center font-medium">
                <div class="flex h-9 w-9 items-center justify-center">
                    <img src="/logo.png" alt="Logo" class="size-9" />
                </div>
            </Link>

            <div class="flex flex-col gap-6">
                <Card class="rounded-smart-lg bg-bg-card shadow-smart-card">
                    <CardHeader class="px-10 pb-0 pt-8 text-center">
                        <CardTitle class="text-h2 font-semibold">{{ title }}</CardTitle>
                        <CardDescription class="text-text-secondary">
                            {{ description }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="px-10 py-8">
                        <slot />
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
