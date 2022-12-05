<template>
  <div>
    <div>{{ message }}</div>
    <div>{{ test }}</div>
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
