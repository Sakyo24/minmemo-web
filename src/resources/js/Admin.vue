<template>
  <div class="admin">
    <Header />
    <main>
      <SideMenu />
      <router-view class="content" :class="isAdmin ? 'auth-content' : 'no-auth-content'"></router-view>
    </main>
  </div>
  <Alert />
</template>

<script setup>
import Alert from './components/Alert.vue';
import Header from './components/admin/Header.vue';
import SideMenu from './components/admin/SideMenu.vue';
import { computed } from 'vue';
import { useStore } from 'vuex';

const store = useStore();

// computed
const isAdmin = computed(() => store.getters['admin/isAdmin']);
</script>
