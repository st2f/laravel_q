<script setup lang="ts">
import {ref, onMounted, type Ref} from 'vue';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import {type BreadcrumbItem} from '@/types';
import {Head} from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
  {title: 'Dashboard', href: '/dashboard'},
];

const images = ref<string[]>([]);
const docs = ref<string[]>([]);
const loading = ref(true);

const fetchAll = async () => {
  loading.value = true;

  try {
    const [imgRes, docRes] = await Promise.all([
      axios.get(route('images.index')),
      axios.get(route('doc.index')),
    ]);
    images.value = imgRes.data;
    docs.value = docRes.data;
  } catch (error) {
    console.error('Error loading dashboard', error);
  } finally {
    loading.value = false;
  }
};

onMounted(fetchAll);

async function deleteFile(routeName: string, file: string, list: Ref<string[]>) {
  try {
    const url = route(routeName, {file});
    await axios.post(url);
    list.value = list.value.filter(f => f !== file);
  } catch (error) {
    console.error(`Delete failed: ${file}`, error);
  }
}

const deleteImage = (file: string) => deleteFile('image.destroy', file, images);
const deleteDoc = (file: string) => deleteFile('doc.destroy', file, docs);
</script>


<template>
  <Head title="Dashboard"/>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">

      <!-- images -->
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
        <div
          v-for="image in images"
          :key="image"
          class="relative aspect-video overflow-hidden rounded-xl border border-gray-200 dark:border-gray-600"
        >
          <img :src="`/upload/${image}`" alt="User image" class="w-full h-full object-cover"/>
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
        <div v-if="!loading && images.length === 0" class="text-sm text-gray-500">
          No images uploaded yet.
        </div>
      </div>

      <!-- docs -->
      <div>
        <div
          v-for="(doc, index) in docs"
          :key="index"
          class="p-2"
        >
          <!-- Delete button -->
          <button
            @click="deleteDoc(doc)"
            class=" bottom-1 right-1 bg-red-700 text-white cursor-pointer text-xs px-2 py-1 rounded shadow hover:bg-red-600 transition"
          >
            delete
          </button>
          <span class="pl-2">
              <a :href="`/upload-word/${doc}`" target="_blank">{{ doc }}</a>
          </span>
        </div>
        <div v-if="!loading && docs.length === 0" class="text-sm text-gray-500">
          No documents uploaded yet.
        </div>
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
