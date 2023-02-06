<script setup>
import { ref } from 'vue';
import { useStore } from 'vuex';
import { useRouter } from 'vue-router';

const store = useStore();
const router = useRouter();

// data
const message = ref('管理者さん、こんにちは');

// methods
const logout = async () => {
  await store.dispatch('admin/logout');
  router.push('/admin/login');
};
</script>

<template>
  <div class="home">
    <h1>管理者画面</h1>
    <div>{{ message }}</div>
    <div @click="logout">ログアウト</div>
  </div>
</template>
