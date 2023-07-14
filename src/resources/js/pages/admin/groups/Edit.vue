<template>
  <div class="groups">
    <div class="page-wrap">
      <h2 class="page-title">グループ編集</h2>
      <div class="contents-area">
        <template v-if="group">
          <div class="table-area">
            <table class="edit-table">
              <tbody>
                <tr>
                  <th>ID</th>
                  <td>{{ group.id }}</td>
                </tr>
                <tr>
                  <th>グループ名</th>
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
                  <th>オーナー</th>
                  <td>
                    <router-link :to="`/admin/users/${group.owner.id}/edit`">
                      {{ group.owner.name }}
                    </router-link>
                  </td>
                </tr>
                <tr>
                  <th>作成日時</th>
                  <td>{{ group.created_at }}</td>
                </tr>
                <tr>
                  <th>最終更新日時</th>
                  <td>{{ group.updated_at }}</td>
                </tr>
                <tr>
                  <th>削除日時</th>
                  <td>{{ group.deleted_at }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="edit-btn-area">
            <router-link class="btn" to="/admin/groups">一覧</router-link>
            <div class="btn green-btn" @click="updateGroup">更新</div>
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

const group = ref(null);
const name = ref(null);
const errors = ref(null);

onMounted(() => {
  getGroup();
});

// methods
const getGroup = async () => {
  const response = await axios.get(`/api/admin/groups/${route.params.id}`);

  if (response.status === STATUS.OK) {
    group.value = response.data.group;
    name.value = response.data.group.name;
    return;
  }

  store.commit('alert/setAlert', {
    message: response.statusText,
    type: 'error',
  });
};

const updateGroup = async () => {
  const data = {
    id: group.value.id,
    name: name.value,
  };
  const response = await axios.put(`/api/admin/groups/${route.params.id}`, data);

  if (response.status === STATUS.OK) {
    store.commit('alert/setAlert', {
      message: 'グループを更新しました。',
      type: 'success',
    });
    router.push('/admin/groups');
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
