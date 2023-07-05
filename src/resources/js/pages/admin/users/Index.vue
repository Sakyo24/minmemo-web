<template>
  <div class="users">
    <div class="page-wrap">
      <h2 class="page-title">ユーザー一覧</h2>
      <div class="contents-area">
        <template v-if="users.length > 0">
          <div class="table-header">
            件数：{{ total }}件
          </div>
          <div class="table-area">
            <table class="index-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>名前</th>
                  <th>メールアドレス</th>
                  <th>編集</th>
                  <th>削除</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="user in users" :key="user.id">
                  <td class="id-cell">{{ user.id }}</td>
                  <td>{{ user.name }}</td>
                  <td>{{ user.email }}</td>
                  <td class="btn-cell">
                    <router-link class="btn green-btn" :to="`/admin/users/${user.id}/edit`">編集</router-link>
                  </td>
                  <td class="btn-cell">
                    <div class="btn red-btn">削除</div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <Pagination
            v-show="last_page != 1"
            :current-page="current_page"
            :last-page="last_page"
            @change-page="changePage"
          />
        </template>
        <template v-else>
          <p class="no-data-message">データがありません。</p>
        </template>
      </div>
    </div>
  </div>
</template>

<script setup>
import Pagination from '../../../components/admin/Pagination.vue';
import { ref, onMounted } from 'vue';
import { useStore } from 'vuex';
import axios from 'axios';
import { STATUS } from '../../../util/status';

const store = useStore();

const current_page = ref(1);
const last_page = ref(1);
const total = ref(0);
const users = ref([]);

onMounted(() => {
  getUsers();
});

// methods
const getUsers = async () => {
  const response = await axios.get(`/api/admin/users?page=${current_page.value}`);

  if (response.status === STATUS.OK) {
    last_page.value = response.data.users.last_page;
    total.value = response.data.users.total;
    users.value = response.data.users.data;
    return;
  }

  store.commit('alert/setAlert', {
    message: response.statusText,
    type: 'error',
  });
};

const changePage = (page) => {
  if (current_page.value === page) return;
  current_page.value = page;
  getUsers();
};
</script>
