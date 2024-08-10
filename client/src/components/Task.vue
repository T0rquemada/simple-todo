<script setup>
    import { ref } from 'vue';

    const props = defineProps({
        taskId: Number,
        title: String,
        description: String,
        userId: Number,
        completed: Number
    });

    async function updateCompleteRequest(taskID, complete) {
        try {
            console.log(complete);
            const jwt = localStorage.getItem('JWT');
            const response = await fetch('http://localhost:5174/tasks/update_complete', {
                method: 'PUT',
                headers: { 'Content-Type' : 'application/json' },
                body: JSON.stringify({task_id: taskID, complete: complete, jwt: jwt})
            });

            const data = await response.json();

            if (!data) { 
                alert('Error while updating complete status task! Empty response');
                return false;
            }

            if (data.code !== 200) { 
                alert(`Error while updating complete status task: ${data.message}`);
                return false;
            }
            
            console.log('Response while updating task status: ', data);
            return true;
        } catch (err) {
            console.error(err);
        }
    }

    async function setComplete() {
        if (isComplete.value === 1) { isComplete.value = 0; }
        else if (isComplete.value === 0) { isComplete.value = 1; }

        let result = await updateCompleteRequest(props.taskId, isComplete.value);

        // If request unsuccessful, return previous value
        if (!result) {
            if (isComplete.value === 1) { isComplete.value = 0; }
            else if (isComplete.value === 0) { isComplete.value = 1; }
        }
    }

    const isComplete = ref(props.completed);
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
            
        </div>
    </div>
</template>

<style scoped>
    .task__container {
        width: 60%;
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