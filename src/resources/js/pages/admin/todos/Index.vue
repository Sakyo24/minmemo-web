<template>
  <div class="todos">
    <div class="page-wrap">
      <h2 class="page-title">メモ一覧</h2>
      <div class="contents-area">
        <template v-if="todos.length > 0">
          <div class="table-header">
            件数：{{ total }}件
          </div>
          <div class="table-area">
            <table class="index-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>タイトル</th>
                  <th>作成者</th>
                  <th>グループ</th>
                  <th>編集</th>
                  <th>削除</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="todo in todos" :key="todo.id">
                  <td class="id-cell">{{ todo.id }}</td>
                  <td>{{ todo.title }}</td>
                  <td>
                    <router-link :to="`/admin/users/${todo.user.id}/edit`">
                      {{ todo.user.name }}
                    </router-link>
                  </td>
                  <td>
                    <router-link v-if="todo.group" :to="`/admin/groups/${todo.group.id}/edit`">
                      {{ todo.group.name }}
                    </router-link>
                  </td>
                  <td class="btn-cell">
                    <router-link class="btn green-btn" :to="`/admin/todos/${todo.id}/edit`">編集</router-link>
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
const todos = ref([]);

onMounted(() => {
  getTodos();
});

// methods
const getTodos = async () => {
  const response = await axios.get(`/api/admin/todos?page=${current_page.value}`);

  if (response.status === STATUS.OK) {
    last_page.value = response.data.todos.last_page;
    total.value = response.data.todos.total;
    todos.value = response.data.todos.data;
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
  getTodos();
};
</script>
