<script setup>
    const props = defineProps({
        showPopup: Boolean,
        contentPopup: String,
        isAuthenticated: Boolean
    });

    const emit = defineEmits(['showPopup', 'setContentPopup', 'setAuthenticated']);

    function signOut() {
        localStorage.removeItem('JWT'); 
        emit('setAuthenticated', false);
    }
</script>

<template>
    <header>
        <div class="page__title">ToDo</div>
        
        <div class="header__btns" v-if="!isAuthenticated">
            <button 
            @click="() => { emit('showPopup'); emit('setContentPopup', 'sign in'); }" 
            class="header__btn">Sign in</button>

            <button 
            @click="() => { emit('showPopup'); emit('setContentPopup', 'sign up'); }" 
            class="header__btn">Sign up</button>
        </div>

        <div class="header__btns" v-if="isAuthenticated">
            <button 
            @click="() => { emit('showPopup'); emit('setContentPopup', 'create task'); }" 
            class="header__btn">Create task</button>

            <button 
            @click="signOut" 
            class="header__btn">Sign out</button>
        </div>
    </header>
</template>

<style scoped>
    header {
        padding: 0 0 1rem 0;
        display: flex;
        justify-content: space-between;
    }

    .page__title { user-select: none; }

    .header__btns {
        display: flex;
    }

    .header__btns>* {
        margin: 0 0.5rem;
    }

    .header__btn {
        background-color: transparent;
        border: none;
        color: #fff;
        cursor: pointer;
        user-select: none;
    }
</style>