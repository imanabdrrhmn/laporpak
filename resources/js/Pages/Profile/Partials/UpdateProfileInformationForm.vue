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
    name: user.name,
    email: user.email,
});
</script>

<template>
  <section class="py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
      <header class="mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">
          Profile Information
        </h2>
        <p class="mt-1 text-sm text-gray-600">
          Update your account's profile information and email address.
        </p>
      </header>

      <form
        @submit.prevent="form.patch(route('profile.update'))"
        class="space-y-6 bg-white p-6 rounded-lg shadow-md"
      >
        <!-- Name Field -->
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name</label>
          <input
            id="name"
            type="text"
            v-model="form.name"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
            required
            autocomplete="name"
          />
          <div v-if="form.errors.name" class="mt-2 text-sm text-red-600">
            {{ form.errors.name }}
          </div>
        </div>

        <!-- Email Field -->
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 mb-2 mt-2">Email</label>
          <input
            id="email"
            type="email"
            v-model="form.email"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
            required
            autocomplete="username"
          />
          <div v-if="form.errors.email" class="mt-2 text-sm text-red-600">
            {{ form.errors.email }}
          </div>
        </div>

        <!-- Email Verification Link -->
        <div v-if="mustVerifyEmail && user.email_verified_at === null">
          <p class="mt-2 text-sm text-gray-800">
            Your email address is unverified.
            <Link
              :href="route('verification.send')"
              method="post"
              as="button"
              class="text-white-600 underline hover:text-indigo-800"
            >
              Click here to re-send the verification email.
            </Link>
          </p>
          <div v-show="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
            A new verification link has been sent to your email address.
          </div>
        </div>

        <!-- Save Button -->
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
    padding: 0.75rem; /* Padding inside input */
    margin-top: 0.5rem;
    font-size: 1rem; /* Adjust font size for readability */
    width: 100%; /* Ensure input fills the container */
    border-radius: 0.375rem; /* Rounded borders */
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
