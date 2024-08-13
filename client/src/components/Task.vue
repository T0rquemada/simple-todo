<script setup>
    import { ref } from 'vue';

    const props = defineProps({
        taskId: Number,
        title: String,
        description: String,
        userId: Number,
        completed: Number
    });

    async function request(route, method, body) {
        try {
            const response = await fetch(`http://localhost:5174/tasks/${route}`, {
                method: method,
                headers: { 'Content-Type' : 'application/json' },
                body: JSON.stringify(body)
            });

            const data = await response.json();

            if (!data) { 
                alert(`Error while ${route}! Empty response`);
                return false;
            }

            if (data.code !== 200) { 
                alert(`Error while ${route}, message: ${data.message}`);
                return false;
            }
            
            console.log(`Response while ${route} status: ${data}`);
            return true;
        } catch (err) {
            console.error(err);
        }
    }

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

        //let result = await updateCompleteRequest(props.taskId, isComplete.value);
        const jwt = localStorage.getItem('JWT');
        const body = { task_id: props.taskId, complete: isComplete.value, jwt: jwt };
        const result = await request('update_complete', 'PUT', body);

        // If request unsuccessful, return previous value
        if (!result) {
            if (isComplete.value === 1) { isComplete.value = 0; }
            else if (isComplete.value === 0) { isComplete.value = 1; }
        }
    }

    function setShowModal() {
        showModal.value = !showModal.value;
    }

    async function deleteRequest() {
        const response = await fetch('http://localhost:5174/tasks/delete_task', {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                task_id: taskId,
                jwt: jwt
            })
        });

        if (!response.ok) { throw new Error('Network response was not ok'); }

        const data = await response.json();

        if (!data || data.code !== 200) throw new Error(`Error while deleting tasks: ${data.message || 'empty response'}`);
        
        console.log('Deleting tasks response:', data);
    }

    async function deleteTask() {
        const jwt = localStorage.getItem('JWT');
        const taskId = props.taskId;
        const body = { task_id: taskId, jwt: jwt };

        await request('delete_task', 'DELETE', body);
        
        showModal.value = false;
        window.location.reload();
    }

    const showModal = ref(false);
    const isComplete = ref(props.completed);
</script>

<template>
    <div class="task__container">
        <div class="task__header">
            <div @click="setShowModal" class="task__settings">...</div>
            <div v-if="showModal === true" class="task__settings__modal__container">
                <div class="task__settings__option">Edit</div>
                <div @click="deleteTask" class="task__settings__option">Delete</div>
            </div>
            <div @click="setComplete" class="task__complete">
                <div v-if="isComplete === 1" class="task__complete__value">X</div>
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

    .task__title {
        margin: 0 0.5rem;
    }

    .task__complete {
        width: 1.85rem;
        height: 1.85rem;
        text-align: center;
        border: 1px solid grey;
        cursor: pointer;
    }

    .task__complete__value {
        user-select: none;
    }

    .task__title {
        font-size: 1.25rem;
        font-weight: 700;
    }

    .task__settings {
        width: fit-content;
        height: fit-content;
        transform: rotate(90deg);
        position: relative;
        cursor: pointer;
    }

    .task__settings__modal__container {
        padding: 0.45rem;
        position: absolute;
        margin: 5rem 0 0 -2rem;
        background-color: rgb(59, 56, 56);
        border-radius: 5px;
    }

    .task__settings__option {
        cursor: pointer;
    }
</style>