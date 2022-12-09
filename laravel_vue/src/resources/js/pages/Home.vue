<template>
  <div class="home">
    <h1>Home画面</h1>
    <div>{{ message }}</div>
    <div>{{ test }}</div>
    <router-link to="/about">About画面へ</router-link>
  </div>
</template>

<script>
import { ref, computed } from 'vue';
import { useStore } from 'vuex';

export default {
  setup() {
    const message = ref('こんにちは');
    const store = useStore();

    return {
      message,
      test: computed(() => store.state.test.message),
    };
  },
};
</script>
