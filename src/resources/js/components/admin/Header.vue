<template>
  <header>
    <h1 class="header-title">みんメモ 管理画面</h1>

    <div v-show="isAdmin" class="header-items-area">
      <div class="admin-name">
        <router-link to="/admin/password/update">{{ adminName }}さん</router-link>
      </div>
      <div class="header-btn-area">
        <div class="btn header-btn" @click="logout">ログアウト</div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { computed } from 'vue';
import { useStore } from 'vuex';
import { useRouter } from 'vue-router';

const store = useStore();
const router = useRouter();

// computed
const isAdmin = computed(() => store.getters['admin/isAdmin']);

const adminName = computed(() => {
  if (isAdmin.value) {
    return store.state.admin.admin.name;
  } else {
    return '';
  }
});

// methods
const logout = async () => {
  await store.dispatch('admin/logout');
  router.push('/admin/login');
};
</script>