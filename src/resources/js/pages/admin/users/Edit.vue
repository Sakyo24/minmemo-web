<template>
  <div class="users">
    <div class="page-wrap">
      <h2 class="page-title">ユーザー編集</h2>
      <div class="contents-area">
        <template v-if="user">
          <div class="table-area">
            <table class="edit-table">
              <tbody>
                <tr>
                  <th>ID</th>
                  <td>{{ user.id }}</td>
                </tr>
                <tr>
                  <th>名前</th>
                  <td>
                    <input v-model="name" type="text" />
                    <ul v-if="errors && errors.name">
                      <li v-for="error in errors.name" :key="error" class="validation_message">
                        {{ error }}
                      </li>
                    </ul>
                  </td>
                </tr>
                <tr>
                  <th>メールアドレス</th>
                  <td>
                    <input v-model="email" type="text" />
                    <ul v-if="errors && errors.email">
                      <li v-for="error in errors.email" :key="error" class="validation_message">
                        {{ error }}
                      </li>
                    </ul>
                  </td>
                </tr>
                <tr>
                  <th>登録日時</th>
                  <td>{{ user.created_at }}</td>
                </tr>
                <tr>
                  <th>最終更新日時</th>
                  <td>{{ user.updated_at }}</td>
                </tr>
                <tr>
                  <th>退会日時</th>
                  <td>{{ user.deleted_at }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="edit-btn-area">
            <router-link class="btn" to="/admin/users">一覧</router-link>
            <div class="btn green-btn" @click="updateUser">更新</div>
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

const user = ref(null);
const name = ref(null);
const email = ref(null);
const errors = ref(null);

onMounted(() => {
  getUser();
});

// methods
const getUser = async () => {
  const response = await axios.get(`/api/admin/users/${route.params.id}`);

  if (response.status === STATUS.OK) {
    user.value = response.data.user;
    name.value = response.data.user.name;
    email.value = response.data.user.email;
    return;
  }

  store.commit('alert/setAlert', {
    message: response.statusText,
    type: 'error',
  });
};

const updateUser = async () => {
  const data = {
    id: user.value.id,
    name: name.value,
    email: email.value,
  };
  const response = await axios.put(`/api/admin/users/${route.params.id}`, data);

  if (response.status === STATUS.OK) {
    store.commit('alert/setAlert', {
      message: 'ユーザーを更新しました。',
      type: 'success',
    });
    router.push('/admin/users');
    return;
  }

  if (response.status === STATUS.UNPROCESSABLE_ENTITY) {
    console.log(response.data.errors);
    errors.value = response.data.errors;
    return;
  }

  store.commit('alert/setAlert', {
    message: response.statusText,
    type: 'error',
  });
};
</script>
