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

    function validateUserdata(email, password, username=undefined) {

        if (email.includes(' ')) return [false, "Email can't contain spaces!"];
        if (password.includes(' ')) return [false, "Password can't contein spaces!"];

        //if (!email.includes('@')) return [false, "Email must contain '@'!"];
        //if (email.length < 3) return [false, 'Email must be longer than 2 characters!'];
        //if (password.length < 8) return [false, 'Password must be longer than 7 characters!'];
        //if (password.length > 16) return [false, 'Password must be shorter than 17 characters!'];
        
        return [true];
    }

    function validateTaskdata(title) {
        if (title.length < 3) return [false, 'Task title must be longer than 2 characters!'];

        return [true];
    }

    async function handleSubmit() {
        let formData = null;
        let validate = null;

        if (props.content.includes('sign')) {
            formData = {
                email: email.value,
                password: password.value,
            };

            if (props.content === 'sign up') {
                formData.username = username.value;
                validate = validateUserdata(formData.email, formData.password, formData.username);
            } else { validate = validateUserdata(formData.email, formData.password); }
        } else if (props.content.includes('task')) {
            formData = {
                jwt: localStorage.getItem('JWT'),
                title: title.value,
                description: description.value
            };

            if (props.content === 'edit task') {
                formData.task_id = props.taskId;
            }

            validate = validateTaskdata(formData.title);
        }

        if (!validate[0]) {
            alert(validate[1]);
        } else {
            console.log('Form submitted, form data:', formData);

            if (props.content === 'sign up') await request('users/registration', 'POST', formData, true, emit);
            if (props.content === 'sign in') await request('users/login', 'POST', formData, true, emit);
            if (props.content === 'create task') await request('tasks/create_task', 'POST', formData);
            if (props.content === 'edit task') await request('tasks/edit_task', 'PUT', formData);

            emit('close');
            clearInputs();
            window.location.reload();
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
        <div v-if="content === 'sign in'" class="popup__header popup__part">Sign in</div>
        <div v-if="content === 'sign up'" class="popup__header popup__part">Sign up</div>
        <div v-if="content === 'create task'" class="popup__header popup__part">Create task</div>
        <div v-if="content === 'edit task'" class="popup__header popup__part">Edit task</div>

        <div class="popup__body popup__part">

            <form v-if="content === 'sign in'" action="">
                <input v-model="email" placeholder="Email..." autocomplete="off" />
                <input v-model="password" type="password" placeholder="Password..." autocomplete="off" />
            </form>

            <form v-if="content === 'sign up'" action="">
                <input v-model="username" placeholder="Username..." autocomplete="off" />
                <input v-model="email" placeholder="Email..." autocomplete="off" />
                <input v-model="password" type="password" placeholder="Password..." autocomplete="off" />
            </form>

            <form v-if="content.includes('task')" action="">
                <input v-model="title" placeholder="Title..." autocomplete="off" />
                <input v-model="description" placeholder="Description..." autocomplete="off" />
            </form>

        </div>

        <div class="popup__footer popup__part">
            <button @click="emit('close')" id="popup__closebtn popup" class="popup__btns">Close</button>
            <button @click="handleSubmit" id="popup__submitbtn" class="popup__btns">Submit</button>
        </div>
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

    .popup__part {
        padding: 1rem;
    }

    form {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    input {
        padding: 0.25rem;
        margin: 0.5rem;
        width: 80%;
        color: #000;
        border: none;
        border-radius: 3px;
    }

    .popup__footer {
        display: flex;
        justify-content: space-around;
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