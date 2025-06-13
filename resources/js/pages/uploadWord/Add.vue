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
        title: 'Upload World',
        href: '/upload-word',
    },
];

const form = useForm({
    doc: '',
});

const submit = () => {
    form.post(route('word.store'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Upload a word file" />

            <div class="flex flex-col space-y-6 px-10 py-5">
                <HeadingSmall title="Add a word file" />

                <form @submit.prevent="submit" class="space-y-6">

                    <div class="grid w-full max-w-sm items-center gap-1.5">
                        <Label for="doc">Word document</Label>
                        <Input id="doc" type="file" @input="form.doc = $event.target.files[0]" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/msword" />
                        <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                            {{ form.progress.percentage }}%
                        </progress>
                        <InputError class="mt-2" :message="form.errors.doc" />
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
<!--                <pre class="text-xs text-red-500">{{ form.errors }}</pre>-->

            </div>

    </AppLayout>
</template>
