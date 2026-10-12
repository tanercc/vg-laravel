<template>
    <div class="container flex justify-center items-center min-h-screen flex-col bg-gray-100">
        <h1 class="text-2xl font-bold mb-4">Edit Task</h1>
        <div class="w-full max-w-md bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <form @submit.prevent="updateTask" class="flex flex-col space-y-4">
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
                    Update Task
                </button>
                <p v-if="error" class="text-red-500">{{ error.error || error.message }}</p>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">

import { router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { TaskInput } from '@/types';

const props = defineProps<{
    taskId: number;
}>();

const task = ref<TaskInput>({ title: '', description: '', time_estimate: 0 });
const error = ref<any | null>(null);

onMounted(async () => {
    try {
        const response = await fetch(`/api/tasks/${props.taskId}`);
        if (!response.ok) {
            error.value = await response.json();
            throw new Error(response.statusText);
        }
        task.value = await response.json();
    } catch (exception: any) {
        console.error('Error fetching task:', exception);
        console.error(error.value);
    }
});

const updateTask = async () => {
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const response = await fetch(`/api/tasks/${props.taskId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken || '',
            },
            body: JSON.stringify(task.value),
        });

        if (!response.ok) {
            error.value = await response.json();
            throw new Error(response.statusText);
        }

        console.log('Task updated successfully');

        router.visit('/tasks'); // Redirect to the tasks list page after successful update
    } catch (exception: any) {
        console.error('Error updating task:', exception);
    }
};

</script>