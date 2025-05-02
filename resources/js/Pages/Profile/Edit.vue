<script setup>
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const page = usePage();
page.layout = AppLayout;

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profile" />
    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Profile
            </h2>
        </template>

        <div class="py-12">
            <div>
                <!-- Update Profile Information Form -->
                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8 mb-6">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-3xl w-full mx-auto"
                    />
                </div>

                <!-- Update Password Form -->
                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8 mb-6">
                    <UpdatePasswordForm class="max-w-3xl w-full mx-auto" />
                </div>

                <!-- Delete User Form -->
                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8 mb-6">
                    <DeleteUserForm class="max-w-3xl w-full mx-auto" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
