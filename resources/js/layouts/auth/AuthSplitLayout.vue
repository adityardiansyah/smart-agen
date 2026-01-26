<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const name = page.props.name;
const quote = page.props.quote;

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
        <div class="relative hidden h-full flex-col bg-muted p-10 text-white dark:border-r lg:flex">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600 to-indigo-800" />
            <div class="relative z-20 flex items-center text-lg font-medium">
                <img src="/logo.png" alt="Logo" class="mr-2 size-10" />
                Smart Agen
            </div>
            <div class="relative z-20 flex flex-1 items-center justify-center">
                <img src="/img/ilu-login.svg" alt="Login Illustration" class="max-w-[50%]" />
            </div>
            <div class="relative z-20 mt-auto">
                <blockquote class="space-y-2">
                    <p class="text-lg">&ldquo;Sistem Manajemen Distribusi LPG yang Efisien dan Terintegrasi.&rdquo;</p>
                </blockquote>
            </div>
        </div>
        <div class="lg:p-8">
            <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]">
                <div class="flex flex-col space-y-2 text-center">
                    <h1 class="text-xl font-medium tracking-tight" v-if="title">{{ title }}</h1>
                    <p class="text-sm text-muted-foreground" v-if="description">{{ description }}</p>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
