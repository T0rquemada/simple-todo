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
        
        <div class="btn__container" v-if="!isAuthenticated">
            <button 
            @click="() => { emit('showPopup'); emit('setContentPopup', 'sign in'); }" 
            class="btn">Sign in</button>

            <button 
            @click="() => { emit('showPopup'); emit('setContentPopup', 'sign up'); }" 
            class="btn">Sign up</button>
        </div>

        <div class="btn__container" v-if="isAuthenticated">
            <button 
            @click="() => { emit('showPopup'); emit('setContentPopup', 'create task'); }" 
            class="btn">Create task</button>

            <button 
            @click="signOut" 
            class="btn">Sign out</button>
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

    .btn__container {
        display: flex;
    }

    .btn__container>* {
        margin: 0 0.5rem;
    }
</style>