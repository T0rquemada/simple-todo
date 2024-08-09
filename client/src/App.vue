<script setup>
  import { ref, onMounted } from 'vue';

  import Popup from './components/Popup.vue'
  import Header from './components/Header.vue'
  import TaskList from './components/TaskList.vue'

  const isAuthenticated = ref(false);
  const isPopupVisible = ref(false);
  const contentPopup = ref('');

  function showPopup() {
    isPopupVisible.value = true;
  }

  function closePopup() {
    isPopupVisible.value = false;
  }

  function setAuthenticated(value) {
    console.log(`setAuthenticated: ${value}`);
    isAuthenticated.value = value;
  }

  function setContentPopup(value) {
    console.log(`setContentPopup: ${value}`);
    contentPopup.value = value;
  }

async function autologinRequest() {
  try {
    const jwt = localStorage.getItem('JWT');
    if (!jwt) {console.log('JWT not finded!'); return;}
    
    const response = await fetch('http://localhost:5174/users/autologin', {
      method: 'POST',
      headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${jwt}`
      }
    });

    if (!response.ok) throw new Error('Network response was not ok');

    const data = await response.json();

    if (!data) throw new Error('Error while registration! Empty response');
    if (data.code !== 200) throw new Error(`Error while registration: ${data.message}`);
    
    console.log('Auto login response:', data);

    setAuthenticated(true);
  } catch (error) { console.error('Auto login failed:', error); }
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
  />

  <main v-if="isAuthenticated">
    <TaskList />
  </main>
  
</template>

<style scoped></style>
