<script setup>
    import Task from './Task.vue';
    import { ref } from 'vue';

    const props = defineProps({
        contentPopup: String
    });

    const emit = defineEmits(['showPopup', 'setContentPopup', 'setTaskid']);

    function showPopup() {
        emit('showPopup');
    }

    function setContentPopup(content) {
        emit('setContentPopup', content);
    }

    function setTaskid(value) {
        emit('setTaskid', value);
    }

    async function getTasks(jwt) {
        const response = await fetch (`http://localhost:5174/tasks/get_tasks?jwt=${jwt}`, {
            method: 'GET',
            headers: { 'Content-Type' : 'application/json' }
        });

        if (!response.ok) {
            alert('Network response while fetching task was not ok');
            return null;
        }

        const data = await response.json();
        
        return data.tasks;
    }

    const tasks = ref([]);

    (async () => {
        try {
            let jwt = localStorage.getItem('JWT');
            if (!jwt) throw new Error('JWT not exist!');

            let fetchedTasks = await getTasks(jwt);
            if (!fetchedTasks) return; // If tasks not found, end proccess

            tasks.value = fetchedTasks;
        } catch (error) { console.error('Error:', error); }
    })();
</script>

<template>
    <Suspense>
        <template #default>
            <div class="task__list__container">
                <Task 
                    v-for="task in tasks"
                    :key="task.id"
                    :taskId="task.id"
                    :title="task.title"
                    :description="task.description"
                    :userId="task.author_id"
                    :completed="task.completed"
                    @showPopup="showPopup"
                    @setContentPopup="setContentPopup"
                    @setTaskid='setTaskid'
                />
            </div>
        </template>
        <template #fallback>
            <div>Loading...</div>
        </template>
    </Suspense>
</template>

<style scoped>
    .task__list__container {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
</style>