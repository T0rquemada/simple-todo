<script setup>
    import { ref } from 'vue';
    import { request } from '../api/request.js';

    const props = defineProps({
        taskId: Number,
        title: String,
        description: String,
        userId: Number,
        completed: Number
    });

    const emit = defineEmits(['showPopup', 'setContentPopup', 'setTaskid']);

    async function setComplete() {
        if (isComplete.value === 1) { isComplete.value = 0; }
        else if (isComplete.value === 0) { isComplete.value = 1; }

        const jwt = localStorage.getItem('JWT');

        if (!jwt) { console.error('JWT not finded while setting task complete!'); return; }

        const body = { task_id: props.taskId, complete: isComplete.value, jwt: jwt };
        const result = await request('tasks/update_complete', 'PUT', body);

        // If request unsuccessful, return previous value
        if (!result) {
            if (isComplete.value === 1) { isComplete.value = 0; }
            else if (isComplete.value === 0) { isComplete.value = 1; }
        }
    }

    function setShowModal() {
        showModal.value = !showModal.value;
    }

    async function deleteTask() {
        const jwt = localStorage.getItem('JWT');
        const taskId = props.taskId;
        const body = { task_id: taskId, jwt: jwt };

        await request('tasks/delete_task', 'DELETE', body);

        showModal.value = false;
        window.location.reload();
    }

    async function editTask() {
        setTaskid(props.taskId);
        emit('showPopup'); 
        emit('setContentPopup', 'edit task'); 
        showModal.value = false;
    }

    function setTaskid(value) {
        emit('setTaskid', value);
    }

    const showModal = ref(false);
    const isComplete = ref(props.completed);
</script>

<template>
    <div class="task__container">
        <div class="task__header">
            <div style="display: flex;">
                <div @click="setShowModal" class="task__settings">
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                </div>
                <div v-if="showModal === true" class="task__settings__modal__container">
                    <div @click="editTask" class="task__settings__option">Edit</div>
                    <div @click="deleteTask" class="task__settings__option">Delete</div>
                </div>
                <div @click="setComplete" class="task__complete">
                    <div v-if="isComplete === 1" class="task__complete__value">X</div>
                </div>
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
        margin: 0 0.5rem 0 0;
        position: relative;
        cursor: pointer;
    }

    .dot {
        width: 4px; 
        height: 4px; 
        background-color: white; 
        border-radius: 50%; 
        margin: 5px 0;
    }


    .task__settings__modal__container {
        padding: 0.45rem;
        position: absolute;
        margin: 2rem 0 0 -2rem;
        background-color: rgb(59, 56, 56);
        border-radius: 5px;
    }

    .task__settings__option {
        cursor: pointer;
    }

    @media (max-width: 45rem) {
    .task__header {
        flex-direction: column; 
        align-items: flex-start; 
    }

    .task__title {
        margin: 0.5rem 0 0; 
    }
}
</style>