<script setup>
    import Task from './Task.vue';
    import { ref } from 'vue';

    async function getTasks(userId) {
        const response = await fetch (`http://localhost:5174/tasks/get_tasks?jwt=${userId}`, {
            method: 'GET',
            headers: { 'Content-Type' : 'application/json' }
        });

        if (!response.ok) {
            const errorData = await response.json();

            if (errorData.code === 404) { console.error(errorData.message); } 
            else { alert(errorData.message); }

            throw new Error('Network response was not ok');
        }

        const data = await response.json();

        if (!data) throw new Error('Error while fetching tasks! Empty response');
        if (data.code !== 200) throw new Error(`Error while fetching tasks: ${data.message}`);
        
        console.log('Fetching tasks response:', data);

        return data.tasks;
    }

    const tasks = ref([]);

    (async () => {
        try {
            let jwt = localStorage.getItem('JWT');
            if (!jwt) throw new Error('JWT not exist!');

            let fetchedTasks = await getTasks(jwt);
            if (fetchedTasks === undefined) return; // If tasks not found, end proccess

            tasks.value = fetchedTasks;

            console.log(tasks.value);
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