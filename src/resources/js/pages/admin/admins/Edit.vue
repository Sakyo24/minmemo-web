<template>
  <div class="admins">
    <div class="page-wrap">
      <h2 class="page-title">管理者編集</h2>
      <div class="contents-area">
        <template v-if="admin">
          <div class="table-area">
            <table class="edit-table">
              <tbody>
                <tr>
                  <th>ID</th>
                  <td>{{ admin.id }}</td>
                </tr>
                <tr>
                  <th>名前</th>
                  <td>
                    <input v-model="name" type="text">
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
                    <input v-model="email" type="text">
                    <ul v-if="errors && errors.email">
                      <li v-for="error in errors.email" :key="error" class="validation_message">
                        {{ error }}
                      </li>
                    </ul>
                  </td>
                </tr>
                <tr>
                  <th>作成日時</th>
                  <td>{{ admin.created_at }}</td>
                </tr>
                <tr>
                  <th>最終更新日時</th>
                  <td>{{ admin.updated_at }}</td>
                </tr>
                <tr>
                  <th>削除日時</th>
                  <td>{{ admin.deleted_at }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="edit-btn-area">
            <router-link class="btn" to="/admin/admins">一覧</router-link>
            <div class="btn green-btn" @click="updateAdmin">更新</div>
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

const admin = ref(null);
const name = ref(null);
const email = ref(null);
const errors = ref(null);

onMounted(() => {
  getAdmin();
});

// methods
const getAdmin = async () => {
  const response = await axios.get(`/api/admin/admins/${route.params.id}`);

  if (response.status === STATUS.OK) {
    admin.value = response.data.admin;
    name.value = response.data.admin.name;
    email.value = response.data.admin.email;
    return;
  }

  store.commit('alert/setAlert', {
    message: response.statusText,
    type: 'error',
  });
};

const updateAdmin = async () => {
  const data = {
    id: admin.value.id,
    name: name.value,
    email: email.value,
  };
  const response = await axios.put(`/api/admin/admins/${route.params.id}`, data);

  if (response.status === STATUS.OK) {
    store.commit('alert/setAlert', {
      message: '管理者を更新しました。',
      type: 'success',
    });
    router.push('/admin/admins');
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
