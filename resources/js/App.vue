<template>
    <component :is="layout">
        <router-view />
    </component>
</template>

<script setup>
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import AppLayout from '@/layouts/AppLayout.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import DefaultLayout from '@/layouts/DefaultLayout.vue';

const router = useRouter();

const layout = computed(() => {
    const meta = router.currentRoute.value?.meta?.layout;
    if (meta === AuthLayout) return AuthLayout;
    if (meta === AppLayout) return AppLayout;
    return DefaultLayout;
});
</script>
