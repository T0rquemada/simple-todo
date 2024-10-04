<script setup>
    import { ref, onMounted, onUnmounted } from 'vue';
    import { request } from '../api/request.js';

    const props = defineProps({
        isVisible: Boolean,
        content: String,
        isAuthenticated: Boolean,
        taskId: Number
    });

    const emit = defineEmits(['close', 'setAuthenticated']);

    // Input's field setup
    const username = ref('');
    const email = ref('');
    const password = ref('');

    const title = ref('');
    const description = ref('');

    // Handle the 'Esc' key press to close pop up
    function handleEsc(event) {
        if (event.key === 'Escape') {
            emit('close');
            clearInputs();
        }
    }

    // Handle the 'Enter' key press to submit pop up
    function handleEnter(event) {
        if (event.key === 'Enter') {
            handleSubmit();
        }
    }

    function clearInputs () {
      username.value = '';
      email.value = '';
      password.value = '';
      title.value = '';
      description.value = '';
    };

    async function handleSubmit(event) {
        event.preventDefault();
        let formData = null;

        if (props.content.includes('sign')) {
            formData = {
                email: email.value,
                password: password.value,
            };
        } else if (props.content.includes('task')) {
            formData = {
                jwt: localStorage.getItem('JWT'),
                title: title.value,
                description: description.value
            };

            if (props.content === 'edit task') {
                formData.task_id = props.taskId;
            }
        }

        console.log('Form submitted, form data:', formData);

        let response;

        if (props.content === 'sign up') response = await request('users/registration', 'POST', formData);
        if (props.content === 'sign in') response = await request('users/login', 'POST', formData);
        if (props.content === 'create task') response = await request('tasks/create_task', 'POST', formData);
        if (props.content === 'edit task') response = await request('tasks/edit_task', 'PUT', formData);

        if (response) {
            if (response.jwt) {
                localStorage.setItem('JWT', response.jwt);
                emit('setAuthenticated', true);
            }
            
            emit('close');
            clearInputs();
            //window.location.reload();
        } else {
            console.error('Error while receiving response')
        }
    }

    onMounted(() => {
        document.addEventListener('keydown', handleEsc);
        document.addEventListener('keydown', handleEnter);
    });

    onUnmounted(() => {
        document.removeEventListener('keydown', handleEsc);
        document.removeEventListener('keydown', handleEnter);
    });
</script>

<template>
    <div v-if="isVisible"  class="popup__container">
        <form  action="">
            <div class="popup__header popup__part">
                <div class="popup__title">{{ content[0].toUpperCase()+content.slice(1) }}</div>
                <button @click="emit('close')">X</button>
            </div>
    
            <div v-if="content.includes('task')" class="popup__body popup__part">
                <input v-model="title" placeholder="Title..." autocomplete="off" required />
                <input v-model="description" placeholder="Description..." autocomplete="off" required />
            </div>
            
            <div v-else class="popup__body popup__part">
                <input v-if="content === 'sign up'" v-model="username" placeholder="Username..." autocomplete="off" required />
                <input v-model="email" type="email" placeholder="Email..." autocomplete="off" required />
                <input v-model="password" type="password" placeholder="Password..." autocomplete="off" required />
            </div>

            <div class="popup__footer popup__part">
                <button @click="emit('close')" id="popup__closebtn popup" class="popup__btns">Close</button>
                <button @click="handleSubmit" id="popup__submitbtn" class="popup__btns">Submit</button>
            </div>
        </form>
    </div>
</template>

<style scoped>
    .popup__container {
        border: 1px solid #fff;
        width: 30%;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: #000;
        border-radius: 5px;
        z-index: 999;
    }

    form {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .popup__part {
        padding: 0.5rem 1rem;
    }

    .popup__header {
        display: flex;
        width: 100%;
        align-items: center;
        justify-content: space-between;
    }

    .popup__title {
        font-weight: 700;
        
    }

    input {
        padding: 0.25rem;
        margin: 0.5rem;
        width: 90%;
        color: #000;
        border: none;
        border-radius: 3px;
    }

    .popup__footer {
        width: 100%;
        display: flex;
        justify-content: space-between;
    }

    .popup__btns {
        padding: 0.5rem 0.75rem;
        cursor: pointer;
        border: 1px solid #fff;
        border-radius: 5px;
        background-color: transparent;
    }

    @media (max-width: 45rem) {
        .popup__container {
            width: 80%;
        }
    }
</style>