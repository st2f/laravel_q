<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const images = ref<string[]>([])
const loading = ref(true)

const fetchImages = async () => {
    try {
        const response = await axios.get(route('images.index'))
        images.value = response.data
    } finally {
        loading.value = false
    }
}

onMounted(fetchImages);
async function deleteImage(filename: string) {
    try {
        let url = route('image.destroy', { file: filename });
        //console.log(url);
        await axios.post(url);
        // Remove image from the screen
        images.value = images.value.filter(img => img !== filename)
    } catch (error) {
        console.error('Delete failed', error)
    }
}
</script>

<template>
    <Head title="Dashboard" />
     <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <div
                    v-for="(image, index) in images"
                    :key="index"
                    class="relative aspect-video overflow-hidden rounded-xl border border-gray-200 dark:border-gray-600"
                >
                    <img :src="`/upload/${image}`" alt="User image" class="w-full h-full object-cover" />

                    <!-- Filename overlay -->
                    <div class="absolute top-0 left-0 w-full bg-black/50 text-white text-sm px-2 py-1 truncate-start">
                        {{ image }}
                    </div>

                    <!-- Delete button -->
                    <button
                        @click="deleteImage(image)"
                        class="absolute bottom-1 right-1 bg-red-700 text-white cursor-pointer text-xs px-2 py-1 rounded shadow hover:bg-red-600 transition"
                    >
                        delete
                    </button>
                </div>

                <div v-if="loading">Loading...</div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.truncate-start {
    direction: rtl;
    text-align: left;
    text-overflow: ellipsis;
    overflow: hidden;
    white-space: nowrap;
}
</style>
