<template>
  <div v-show="alert" class="alert" :class="alertType">
    {{ alert }}
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useStore } from 'vuex';

const store = useStore();

// computed
const alert = computed(() => store.getters['alert/getAlertMessage']);
const alertType = computed(() => store.getters['alert/getAlertType'] + '-alert');
</script>

<style scoped lang="scss">
.alert {
  z-index: 100;
  position: fixed;
  top: 30px;
  left: 50%;
  -webkit-transform: translate(-50%, 0);
          transform: translate(-50%, 0);
  padding: 20px;
}

.success-alert {
  background-color: rgb(170, 255, 170);
  border: 1px solid rgb(25, 135, 84);
}

.error-alert {
  background-color: rgb(255, 170, 170);
  border: 1px solid red;
}
</style>
