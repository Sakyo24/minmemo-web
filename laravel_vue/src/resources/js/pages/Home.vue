<template>
  <div class="home">
    <h1>Home画面</h1>
    <div>{{ message }}</div>
    <div @click="logout">ログアウト</div>
  </div>
</template>

<script>
import { ref } from 'vue';
import { useStore } from 'vuex';
import { useRouter } from 'vue-router';

export default {
  setup() {
    const store = useStore();
    const router = useRouter();

    // data
    const message = ref('こんにちは');

    // methods
    const logout = async () => {
      await store.dispatch('auth/logout');
      router.push('/login');
    };

    return {
      message,
      logout,
    };
  },
};
</script>
