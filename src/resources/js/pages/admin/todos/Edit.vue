<template>
  <div class="todos">
    <div class="page-wrap">
      <h2 class="page-title">メモ編集</h2>
      <div class="contents-area">
        <template v-if="todo">
          <div class="table-area">
            <table class="edit-table">
              <tbody>
                <tr>
                  <th>ID</th>
                  <td>{{ todo.id }}</td>
                </tr>
                <tr>
                  <th>タイトル</th>
                  <td>
                    <input v-model="title" type="text">
                  </td>
                </tr>
                <tr>
                  <th>詳細</th>
                  <td>
                    <textarea v-model="detail" cols="30" rows="10"></textarea>
                  </td>
                </tr>
                <tr>
                  <th>作成者</th>
                  <td>
                    <router-link :to="`/admin/users/${todo.user.id}/edit`">
                      {{ todo.user.name }}
                    </router-link>
                  </td>
                </tr>
                <tr>
                  <th>グループ</th>
                  <td>
                    <router-link v-if="todo.group" :to="`/admin/groups/${todo.group.id}/edit`">
                      {{ todo.group.name }}
                    </router-link>
                  </td>
                </tr>
                <tr>
                  <th>作成日時</th>
                  <td>{{ todo.created_at }}</td>
                </tr>
                <tr>
                  <th>最終更新日時</th>
                  <td>{{ todo.updated_at }}</td>
                </tr>
                <tr>
                  <th>削除日時</th>
                  <td>{{ todo.deleted_at }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="edit-btn-area">
            <router-link class="btn" to="/admin/todos">一覧</router-link>
            <div class="btn green-btn" @click="updateTodo">更新</div>
          </div>
        </template>
        <template v-else>
          <p class="no-data-message">データがありません。</p>
        </template>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useStore } from 'vuex';
import axios from 'axios';
import { STATUS } from '../../../util/status';

const route = useRoute();
const router = useRouter();
const store = useStore();

const todo = ref(null);
const title = ref(null);
const detail = ref(null);
const errors = ref(null);

onMounted(() => {
  getTodo();
});

// methods
const getTodo = async () => {
  const response = await axios.get(`/api/admin/todos/${route.params.id}`);

  if (response.status === STATUS.OK) {
    todo.value = response.data.todo;
    title.value = response.data.todo.title;
    detail.value = response.data.todo.detail;
    return;
  }

  store.commit('alert/setAlert', {
    message: response.statusText,
    type: 'error',
  });
};

const updateTodo = async () => {
  const data = {
    id: todo.value.id,
    title: title.value,
    detail: detail.value,
  };
  const response = await axios.put(`/api/admin/todos/${route.params.id}`, data);

  if (response.status === STATUS.OK) {
    store.commit('alert/setAlert', {
      message: 'メモを更新しました。',
      type: 'success',
    });
    router.push('/admin/todos');
    return;
  }

  if (response.status === STATUS.UNPROCESSABLE_ENTITY) {
    errors.value = response.data.errors;
    return;
  }

  store.commit('alert/setAlert', {
    message: response.statusText,
    type: 'error',
  });
};
</script>