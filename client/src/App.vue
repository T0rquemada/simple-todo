<script setup>
  import { ref, onMounted } from 'vue';

  import Popup from './components/Popup.vue'
  import Header from './components/Header.vue'
  import TaskList from './components/TaskList.vue'
  import { request } from './api/request.js';

  const isAuthenticated = ref(false);
  const isPopupVisible = ref(false);
  const contentPopup = ref('');
  const taskId = ref(0);

  function showPopup() {
    isPopupVisible.value = true;
  }

  function closePopup() {
    isPopupVisible.value = false;
  }

  function setAuthenticated(value) {
    isAuthenticated.value = value;
  }

  function setContentPopup(value) {
    contentPopup.value = value;
  }

  function setTaskid(value) {
    taskId.value = value;
  }

  async function autologinRequest() {
    const jwt = localStorage.getItem('JWT');
    let response = await request('users/autologin', 'GET', jwt);

    if (response) {
      console.log('Auto login response:', response);
      setAuthenticated(true);
    }
  }

  onMounted(() => {
    autologinRequest();
  });
</script>

<template>
  <Header 
    @showPopup="showPopup"
    @setContentPopup="setContentPopup"
    @setAuthenticated='setAuthenticated'
    :contentPopup="contentPopup"
    :isAuthenticated="isAuthenticated"
  />

  <Popup 
    :isVisible="isPopupVisible" 
    :content="contentPopup" 
    :isAuthenticated='isAuthenticated' 
    @setAuthenticated='setAuthenticated'
    @close="closePopup" 
    :taskId='taskId' 
    @setTaskid='setTaskid'
  />

  <main v-if="isAuthenticated">
      <TaskList 
      @showPopup="showPopup"
      @setContentPopup="setContentPopup"
      :contentPopup="contentPopup"
      @setTaskid='setTaskid'
    />
  </main>
  
</template>

<style scoped></style>