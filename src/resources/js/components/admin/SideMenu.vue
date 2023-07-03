<template>
  <div v-show="isAdmin" class="side-menu">
    <nav>
      <ul>
        <li class="menu__link">
          <router-link to="/admin/users">ユーザー</router-link>
        </li>
        <li class="menu__link">
          <router-link to="/admin/todos">メモ</router-link>
        </li>
        <li class="menu__link">
          <router-link to="/admin/groups">グループ</router-link>
        </li>
        <li class="menu__link">
          <router-link to="/admin/inquiries">お問い合わせ</router-link>
        </li>
        <li class="menu__link">
          <router-link to="/admin/admins">管理者</router-link>
        </li>
      </ul>
    </nav>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useStore } from 'vuex';

const store = useStore();

// computed
const isAdmin = computed(() => store.getters['admin/isAdmin']);
</script>

<style scoped lang="scss">
.side-menu {
  width: 200px;
  min-height: calc(100vh - 80px);
  display: inline-block;
  vertical-align: top;
  color: #000;
  background-color: #fff;

  li {
    a {
      display: inline-block;
      width: 100%;
      padding: 10px 20px;
      &:hover {
        background-color: rgb(207, 226, 255);
      }
    }
  }
}
</style>
