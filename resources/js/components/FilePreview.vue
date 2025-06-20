<script setup lang="ts">
import {computed} from 'vue';

const props = defineProps<{
  file: File | null;
}>();

// Detect MIME type
const isImage = computed(() =>
  props.file?.type.startsWith('image/')
);

const previewUrl = computed(() => {
  if (props.file && isImage.value) {
    return URL.createObjectURL(props.file);
  }
  return null;
});
</script>

<template>
  <div v-if="file" class="flex items-center gap-4 mt-2">
    <!-- If image, show thumbnail -->
    <img
      v-if="previewUrl"
      :src="previewUrl!"
      alt="Preview"
      class="w-24 h-24 object-cover rounded-md border"
    />

    <!-- If not image, show file icon -->
    <div v-else class="flex items-center gap-2 text-gray-700">
      <svg
        class="w-6 h-6 text-gray-400"
        fill="none"
        stroke="currentColor"
        stroke-width="1.5"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="M12 4v16m8-8H4"
        />
      </svg>
      <span class="text-sm">{{ file.name }}</span>
    </div>
  </div>
</template>
