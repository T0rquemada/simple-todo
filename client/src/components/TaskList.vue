<script setup>
    import Task from './Task.vue';
    import { useStore } from 'vuex';
    import { onMounted, computed } from 'vue';

    function showPopup() {
        emit('showPopup');
    }

    function setContentPopup(content) {
        emit('setContentPopup', content);
    }

    function setTaskid(value) {
        emit('setTaskid', value);
    }

    const props = defineProps({
        contentPopup: String
    });

    const emit = defineEmits(['showPopup', 'setContentPopup', 'setTaskid']);

    const store = useStore();
    const tasks = computed(() => store.state.tasks);

    onMounted(async () => {
        await store.dispatch('refreshTasks');
    });
</script>

<template>
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

<style scoped>
    .task__list__container {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
</style>