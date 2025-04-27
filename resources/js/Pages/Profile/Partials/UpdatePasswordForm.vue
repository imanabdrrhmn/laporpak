<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section class="py-12">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <header class="mb-6">
                <h2 class="text-2xl font-semibold text-gray-800">
                    Update Password
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Ensure your account is using a long, random password to stay secure.
                </p>
            </header>

            <form
                @submit.prevent="updatePassword"
                class="space-y-6 bg-white p-6 rounded-lg shadow-md"
            >
                <!-- Current Password -->
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                    <input
                        id="current_password"
                        ref="currentPasswordInput"
                        v-model="form.current_password"
                        type="password"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        autocomplete="current-password"
                    />
                    <div v-if="form.errors.current_password" class="mt-2 text-sm text-red-600">
                        {{ form.errors.current_password }}
                    </div>
                </div>

                <!-- New Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2 mt-2">New Password</label>
                    <input
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        autocomplete="new-password"
                    />
                    <div v-if="form.errors.password" class="mt-2 text-sm text-red-600">
                        {{ form.errors.password }}
                    </div>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2 mt-2">Confirm Password</label>
                    <input
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        autocomplete="new-password"
                    />
                    <div v-if="form.errors.password_confirmation" class="mt-2 text-sm text-red-600">
                        {{ form.errors.password_confirmation }}
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center gap-4 mt-3">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Save
                    </button>

                    <transition
                        enter-active-class="transition ease-in-out"
                        enter-from-class="opacity-0"
                        leave-active-class="transition ease-in-out"
                        leave-to-class="opacity-0"
                    >
                        <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">
                            Saved.
                        </p>
                    </transition>
                </div>
            </form>
        </div>
    </section>
</template>

<style scoped>
body {
  font-family: 'Poppins', sans-serif;
}

.bg-white {
  background-color: #ffffff;
}

.text-indigo-600 {
  color: #5a67d8;
}

.text-indigo-800 {
  color: #4c51bf;
}

.text-gray-600 {
  color: #718096;
}

.text-gray-700 {
  color: #4a5568;
}

.text-gray-800 {
  color: #2d3748;
}

.text-red-600 {
  color: #e53e3e;
}

.focus\:ring-indigo-500:focus {
  ring-color: #5a67d8;
}

.focus\:border-indigo-500:focus {
  border-color: #5a67d8;
}

input {
    background-color: #ffffff; 
    color: #333333; 
    border: 1px solid #ccc; 
    padding: 0.75rem; 
    margin-top: 0.5rem;
    font-size: 1rem; 
    width: 100%; 
    border-radius: 0.375rem; 
}

input:focus {
    background-color: #f7fafc; 
    border-color: #5a67d8; 
    outline: none; 
}

button {
    background-color: #2563EB; 
    color: white;
    padding: 0.75rem 1.5rem; 
    font-size: 1rem; 
    border-radius: 0.375rem;
    display: inline-block;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #2459ca; 
}

button:disabled {
    background-color: #d2d6dc; 
}

button:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.7); 
}

p.text-red-600 {
    color: #e53e3e;
}

p.text-gray-600 {
    color: #718096;
}
</style>
