<template>
  <div class="inquiries">
    <div class="page-wrap">
      <h2 class="page-title">お問い合わせ一覧</h2>
      <div class="contents-area">
        <template v-if="inquiries.length > 0">
          <div class="table-header">
            件数：{{ total }}件
          </div>
          <div class="table-area">
            <table class="index-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>状態</th>
                  <th>タイトル</th>
                  <th>お問い合わせ者</th>
                  <th>編集</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="inquiry in inquiries" :key="inquiry.id">
                  <td class="id-cell">{{ inquiry.id }}</td>
                  <td>{{ inquiry.status_name }}</td>
                  <td>{{ inquiry.title }}</td>
                  <td>
                    <template v-if="inquiry.user">
                      <router-link :to="`/admin/users/${inquiry.user.id}/edit`">
                        {{ inquiry.user.name }}
                      </router-link>
                    </template>
                    <template v-else>
                      {{ inquiry.name }}
                    </template>
                  </td>
                  <td class="btn-cell">
                    <router-link class="btn green-btn" :to="`/admin/inquiries/${inquiry.id}/edit`">編集</router-link>
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
const inquiries = ref([]);

onMounted(() => {
  getInquiries();
});

// methods
const getInquiries = async () => {
  const response = await axios.get(`/api/admin/inquiries?page=${current_page.value}`);

  if (response.status === STATUS.OK) {
    last_page.value = response.data.inquiries.last_page;
    total.value = response.data.inquiries.total;
    inquiries.value = response.data.inquiries.data;
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
  getInquiries();
};
</script>
