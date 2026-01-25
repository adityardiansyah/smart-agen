<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card';
import { cn } from '@/lib/utils';
import type { Component } from 'vue';

interface Props {
  title: string;
  count: number;
  icon: Component;
  variant?: 'default' | 'success' | 'warning' | 'destructive' | 'info';
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'default',
});

const variants = {
  default: 'text-gray-600 bg-gray-100',
  success: 'text-green-600 bg-green-100',
  warning: 'text-yellow-600 bg-yellow-100',
  destructive: 'text-red-600 bg-red-100',
  info: 'text-blue-600 bg-blue-100',
};
</script>

<template>
  <Card class="overflow-hidden shadow-sm">
    <CardContent class="p-4 flex items-center justify-between">
      <div class="flex flex-col gap-1">
        <div :class="cn('p-2 w-fit rounded-full mb-2', variants[variant])">
          <component :is="icon" class="h-5 w-5" />
        </div>
        <p class="text-sm font-medium text-gray-500">{{ title }}</p>
      </div>
      <div class="text-2xl font-bold">{{ count }}</div>
    </CardContent>
  </Card>
</template>
