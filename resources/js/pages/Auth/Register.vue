<template>
    <div class="container flex justify-center items-center min-h-screen flex-col bg-gray-100">
        <h2 class="text-2xl font-bold mb-4">Register</h2>
        <div class="w-full max-w-xs">
            <form @submit.prevent="register" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">
                        Name:
                    </label>
                    <input
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        type="text" id="name" v-model="form.name" required />
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">
                        Email:
                    </label>
                    <input
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        type="email" id="email" v-model="form.email" required />
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">
                        Password:
                    </label>
                    <input
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        type="password" id="password" v-model="form.password" required />
                </div>
                <div class="mb-4">
                    <label for="password_confirmation" class="block text-gray-700 text-sm font-bold mb-2">
                        Confirm Password:
                    </label>
                    <input
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        type="password" id="password_confirmation" v-model="form.password_confirmation" required />
                </div>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Register
                </button>
                <p v-if="form.hasErrors">
                    <span v-for="(error, index) in form.errors" :key="index">{{ error }}</span>
                </p>
                <p>
                    <a href="/login">Already have an account? Login</a>
                </p>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">

import { router, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: ''
});

const register = () => {
    form.post('/register', {
        onSuccess: () => {
            console.log('Registration successful');
            router.visit('/tasks');
        },
        onError: (errors) => {
            console.error('Registration failed:', errors);
        }
    });
};

</script>