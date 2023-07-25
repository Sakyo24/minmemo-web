<template>
  <div class="inquiries">
    <div class="page-wrap">
      <h2 class="page-title">お問い合わせ編集</h2>
      <div class="contents-area">
        <template v-if="inquiry">
          <div class="table-area">
            <table class="edit-table">
              <tbody>
                <tr>
                  <th>ID</th>
                  <td>{{ inquiry.id }}</td>
                </tr>
                <tr>
                  <th>状態</th>
                  <td>
                    <select v-model="selectStatus">
                      <option v-for="(status, index) in statuses" :key="index" :value="index">{{ status }}</option>
                    </select>
                  </td>
                </tr>
                <tr>
                  <th>お問い合わせ者</th>
                  <td>{{ inquiry.user ? inquiry.user.name : inquiry.name }}</td>
                </tr>
                <tr>
                  <th>メールアドレス</th>
                  <td>{{ inquiry.user ? inquiry.user.email : inquiry.email }}</td>
                </tr>
                <tr>
                  <th>タイトル</th>
                  <td>{{ inquiry.title }}</td>
                </tr>
                <tr>
                  <th>詳細</th>
                  <td style="white-space: pre-wrap;">{{ inquiry.detail }}</td>
                </tr>
                <tr>
                  <th>作成日時</th>
                  <td>{{ inquiry.created_at }}</td>
                </tr>
                <tr>
                  <th>最終更新日時</th>
                  <td>{{ inquiry.updated_at }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="edit-btn-area">
            <router-link class="btn" to="/admin/inquiries">一覧</router-link>
            <div class="btn green-btn" @click="updateInquiry">更新</div>
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

const inquiry = ref(null);
const selectStatus = ref(null);
const errors = ref(null);

const statuses = {
  1: '未回答',
  2: '回答中',
  3: '回答済',
};

onMounted(() => {
  getInquiry();
});

// methods
const getInquiry = async () => {
  const response = await axios.get(`/api/admin/inquiries/${route.params.id}`);

  if (response.status === STATUS.OK) {
    inquiry.value = response.data.inquiry;
    selectStatus.value = response.data.inquiry.status;
    return;
  }

  store.commit('alert/setAlert', {
    message: response.statusText,
    type: 'error',
  });
};

const updateInquiry = async () => {
  const data = {
    id: inquiry.value.id,
    status: selectStatus.value,
  };
  const response = await axios.put(`/api/admin/inquiries/${route.params.id}`, data);

  if (response.status === STATUS.OK) {
    store.commit('alert/setAlert', {
      message: 'お問い合わせを更新しました。',
      type: 'success',
    });
    router.push('/admin/inquiries');
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
