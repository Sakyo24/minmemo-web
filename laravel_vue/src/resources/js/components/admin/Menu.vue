<script setup>
import { computed } from 'vue';
import { useStore } from 'vuex';
import { useRouter } from 'vue-router';

const store = useStore();
const router = useRouter();

// computed
const isAdmin = computed(() => store.getters['admin/isAdmin']);

// methods
const logout = async () => {
  await store.dispatch('admin/logout');
  router.push('/admin/login');
};
</script>

<template>
  <div class="menu">
    <nav>
      <ul>
        <template v-if="isAdmin">
          <li class="menu__link">
            <router-link to="/admin">ホーム</router-link>
          </li>
          <li class="menu__link">
            <router-link to="/admin/admins">管理者</router-link>
          </li>
          <li class="menu__link" @click="logout">ログアウト</li>
        </template>
      </ul>
    </nav>
  </div>
</template>
