<template>
  <div class="user">
    <Header />
    <main>
      <router-view class="content"></router-view>
    </main>
  </div>
  <Alert />
</template>

<script setup>
import Alert from './components/Alert.vue';
import Header from './components/user/Header.vue';
</script>
