<template>
    <div class="container flex items-center min-h-screen flex-col bg-gray-100">
        <h1 class="text-2xl font-bold mb-4">Tasks</h1>
        <a href="/tasks/create" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4">
            New Task
        </a>
        <ul class="max-w-md bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <li v-if="tasks.length === 0" class="text-gray-500">No tasks available.</li>
            <li v-for="task in tasks" :key="task.id" class="mb-2">
                {{ task.title }} - {{ task.description }}
                <a :href="`/tasks/${task.id}/edit`" class="text-blue-500 hover:underline ml-2">Edit</a>
                <button @click="deleteTask(task.id)" class="text-red-500 hover:underline ml-2">Delete</button>
            </li>
        </ul>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Task } from '@/types';

const tasks = ref<Task[]>([]);

onMounted(async () => {
    try {
        const response = await fetch('/api/tasks');
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        tasks.value = await response.json();
    } catch (error) {
        console.error('Error fetching tasks:', error);
    }
});

const deleteTask = async (taskId: number) => {
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const response = await fetch(`/api/tasks/${taskId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken || '',
            },
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        // Remove the deleted task from the tasks array
        tasks.value = tasks.value.filter(task => task.id !== taskId);
    } catch (error) {
        console.error('Error deleting task:', error);
    }
};

</script>