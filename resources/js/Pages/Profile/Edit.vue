<script setup>
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import AvatarPreviewModal from './Partials/AvatarPreviewModal.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const page = usePage();
const user = page.props.auth.user;
const showModal = ref(false);
const avatarPreview = ref(user?.avatar ? `/storage/${user.avatar}` : 'https://placehold.co/120x120?text=Avatar');
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
                <div class="d-flex justify-content-center mb-3 bg-white">
                    <img
                        :src="avatarPreview"
                        class="rounded-circle"
                        alt="Avatar"
                        style="width: 250px; height: 250px; object-fit: cover;"
                        @click="showModal = true"
                    />
                </div>
                <AvatarPreviewModal :show="showModal" :onClose="() => showModal = false" />
                    <div class="bg-white p-4 sm:rounded-lg sm:p-8 mb-6">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-3xl w-full mx-auto"
                    />
                </div>

                <!-- Update Password Form -->
                <div class="bg-white p-4 sm:rounded-lg sm:p-8 mb-6">
                    <UpdatePasswordForm class="max-w-3xl w-full mx-auto" />
                </div>

                <!-- Delete User Form -->
                <div class="bg-white p-4 sm:rounded-lg sm:p-8 mb-6">
                    <DeleteUserForm class="max-w-3xl w-full mx-auto" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
