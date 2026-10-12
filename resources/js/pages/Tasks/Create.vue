<template>
    <div class="container flex justify-center items-center min-h-screen flex-col bg-gray-100">
        <h1 class="text-2xl font-bold mb-4">New Task</h1>
        <div class="w-full max-w-md bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <form @submit.prevent="createTask" class="flex flex-col space-y-4">
                <div class="mb-4">
                    <label for="title" class="block text-sm font-medium text-gray-700">Title:</label>
                    <input type="text" id="title"
                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                        v-model="task.title" required />
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description:</label>
                    <textarea id="description"
                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                        v-model="task.description" required></textarea>
                </div>
                <div>
                    <label for="time_estimate">Time Estimate (minutes):</label>
                    <input type="number" id="time_estimate" v-model="task.time_estimate" required />
                </div>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Create Task
                </button>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { TaskInput } from '@/types';

const task = ref<TaskInput>({ title: '', description: '', time_estimate: 0 });

const createTask = async () => {
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const response = await fetch('/api/tasks', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken || '',
            },
            body: JSON.stringify(task.value),
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        // Optionally, you can handle the response here, e.g., redirect to the tasks list
        console.log('Task created successfully');

        router.visit('/tasks'); // Redirect to the tasks list page after successful creation
    } catch (error) {
        console.error('Error creating task:', error);
    }
};

</script>