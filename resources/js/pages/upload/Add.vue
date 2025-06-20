<script setup lang="ts">
import {Head, useForm} from '@inertiajs/vue3';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import {Button} from '@/components/ui/button';
import FilePreview from '@/components/FilePreview.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import {type BreadcrumbItem} from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Upload image',
    href: '/upload',
  },
];

const form = useForm({
  image: null as File | null,
});

const handleFileChange = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.files?.[0]) {
    form.image = target.files[0];
  }
};

const submit = () => {
  form.post(route('image.store'), {
    preserveScroll: true,
  });
};
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Upload an image"/>

    <div class="flex flex-col space-y-6 px-10 py-5">
      <HeadingSmall title="Add an image"/>

      <form @submit.prevent="submit" class="space-y-6 max-w-md">
        <!-- Dropzone-style File Upload -->
        <label
          for="picture"
          class="group flex flex-col items-center justify-center w-full h-44 border-2 border-dashed rounded-2xl cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition duration-200 text-center"
        >
          <svg
            class="w-10 h-10 mb-2 text-gray-400 group-hover:text-blue-500"
            fill="none"
            stroke="currentColor"
            stroke-width="1.5"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M3 16.5v.75A2.25 2.25 0 005.25 19.5h13.5A2.25 2.25 0 0021 17.25v-.75M3 9.75V9A2.25 2.25 0 015.25 6.75h13.5A2.25 2.25 0 0121 9v.75M3 12h18"
            />
          </svg>
          <span class="text-gray-500 group-hover:text-blue-500 text-sm">
              Click to upload or drag and drop
          </span>
          <input
            id="picture"
            type="file"
            class="hidden"
            @change="handleFileChange"
          />
        </label>

        <!-- Selected file name -->
        <FilePreview :file="form.image"/>

        <!-- Progress -->
        <progress
          v-if="form.progress"
          :value="form.progress.percentage"
          max="100"
          class="w-full h-2 rounded bg-gray-100"
        >
          {{ form.progress.percentage }}%
        </progress>
        <InputError class="mt-2" :message="form.errors.image"/>

        <!-- Submit -->
        <div class="flex items-center gap-4">
          <Button :disabled="form.processing">Save</Button>
          <Transition
            enter-active-class="transition ease-in-out"
            enter-from-class="opacity-0"
            leave-active-class="transition ease-in-out"
            leave-to-class="opacity-0"
          >
            <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">
              Done.
            </p>
          </Transition>
        </div>
      </form>
    </div>

  </AppLayout>
</template>

