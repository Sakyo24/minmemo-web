<script setup>
import Menu from './components/admin/Menu.vue';
</script>

<template>
  <div class="admin">
    <Menu />
    <router-view class="content"></router-view>
  </div>
</template>
