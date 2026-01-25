<script setup lang="ts">
import { LucideIcon } from 'lucide-vue-next';

defineProps<{
  title: string;
  icon?: LucideIcon;
}>();
</script>

<template>
  <div class="mb-6">
    <div class="flex items-center gap-2 mb-4">
      <div v-if="icon" class="p-1.5 bg-primary/10 rounded-md text-primary">
        <component :is="icon" class="h-5 w-5" />
      </div>
      <h3 class="font-semibold text-lg text-gray-800">{{ title }}</h3>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
      <slot />
    </div>
  </div>
</template>
