<script setup>
    import { ref, onMounted } from 'vue';

    const props = defineProps({
        taskId: Number,
        title: String,
        description: String,
        userId: Number,
        completed: Number
    });

    async function getUsername(userId) {
        const response = await fetch (`http://localhost:5174/users/get_username?user_id=${userId}`, {
            method: 'GET',
            headers: { 'Content-Type' : 'application/json' }
        });

        if (!response.ok) throw new Error('Network response was not ok');

        const data = await response.json();

        if (!data) throw new Error('Error while fetching username! Empty response');
        if (data.code !== 200) throw new Error(`Error while fetching username: ${data.message}`);
        
        console.log('Fetching tasks response:', data);

        username.value = data.username;
    }

    async function updateCompleteRequest(taskID, complete) {
        const response = await fetch('http://localhost:5174/tasks/update_task', {
            method: 'PUT',
            headers: { 'Content-Type' : 'application/json' },
            body: JSON.stringify({task_id: taskID, complete: complete})
        });

        if (!response.ok) throw new Error('Network response was not ok');

        const data = await response.json();

        if (!data) throw new Error('Error while updating complete status task! Empty response');
        if (data.code !== 200) throw new Error(`Error while updating complete status task: ${data.message}`);
        console.log('Response while updating task status: ', data);
    }

    function setComplete() {
        if (isComplete.value === 1) { isComplete.value = 0; }
        else if (isComplete.value === 0) { isComplete.value = 1; }
        updateCompleteRequest(props.taskId, isComplete.value);
    }

    const isComplete = ref(props.completed);
    const username = ref('');

    onMounted(() => {
        getUsername(props.userId);
    });
</script>

<template>
    <div class="task__container">
        <div class="task__header">
            <div class="task__complete" @click="setComplete">
                <div v-if="isComplete === 1">X</div>
            </div>
            <div class="task__title">{{ props.title }}</div>
        </div>

        <div class="task__body">
            <div class="task__desc">{{ props.description }}</div>
        </div>
        
        <div class="task__footer">
            <div class="task__creator">{{ 'Username: ' + username }}</div>
        </div>
    </div>
</template>

<style scoped>
    .task__container {
        margin: 1rem;
        padding: 0.5rem;
        border: 2px solid grey;
        border-radius: 5px;
        box-shadow: rgba(255, 255, 255, 0.2) 0px 2px 8px 0px;
    }    

    .task__header {
        display: flex;
        align-items: center;
    }

    .task__header>* {
        margin: 0 0.5rem;
    }

    .task__complete {
        width: 1.85rem;
        height: 1.85rem;
        text-align: center;
        border: 1px solid grey;
    }

    .task__title {
        font-size: 1.25rem;
        font-weight: 700;
    }
</style>