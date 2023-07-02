<script setup>
import { ref } from 'vue';

// data
const message = ref('管理者さん、こんにちは');
</script>

<template>
  <div class="home">
    <h1>管理者画面</h1>
    <div>{{ message }}</div>
  </div>
</template>
