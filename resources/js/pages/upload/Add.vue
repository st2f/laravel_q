<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Upload image',
        href: '/upload',
    },
];

const form = useForm({
    image: '',
});

const submit = () => {
    form.post(route('image.store'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Upload an image" />

            <div class="flex flex-col space-y-6 px-10 py-5">
                <HeadingSmall title="Add an image" />

                <form @submit.prevent="submit" class="space-y-6">

                    <div class="grid w-full max-w-sm items-center gap-1.5">
                        <Label for="picture">Picture</Label>
                        <Input id="picture" type="file" @input="form.image = $event.target.files[0]" />
                        <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                            {{ form.progress.percentage }}%
                        </progress>
                        <InputError class="mt-2" :message="form.errors.image" />
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="form.processing">Save</Button>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Done.</p>
                        </Transition>
                    </div>
                </form>
                <!-- <pre class="text-xs text-red-500">{{ form.errors }}</pre>-->

            </div>

    </AppLayout>
</template>
