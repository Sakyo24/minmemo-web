<template>
  <div class="admins">
    <div class="page-wrap">
      <h2 class="page-title">管理者一覧</h2>
      <div class="contents-area">
        <div class="invite-btn-area">
          <div class="btn yellow-btn" @click="openModal">管理者の招待</div>
        </div>
        <template v-if="admins.length > 0">
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
                <tr v-for="admin in admins" :key="admin.id">
                  <td class="id-cell">{{ admin.id }}</td>
                  <td>{{ admin.name }}</td>
                  <td>{{ admin.email }}</td>
                  <td class="btn-cell">
                    <router-link class="btn green-btn" :to="`/admin/admins/${admin.id}/edit`">編集</router-link>
                  </td>
                  <td class="btn-cell">
                    <div v-if="loginAdminId != admin.id" class="btn red-btn" @click="deleteAdmin(admin.id)">削除</div>
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

    <!------------------------------ モーダルエリア ------------------------------>
    <div v-if="isShowModal" class="modal-wrap">
      <div class="modal-bg" @click="closeModal"></div>
      <div class="modal-area">
        <div class="modal-close-btn-area">
          <span class="material-icons modal-close-btn" @click="closeModal">close</span>
        </div>
        <form @submit.prevent>
          <div class="input-area">
            <label for="name">名前</label>
            <input id="name" v-model="form.name" type="text" placeholder="太郎" />
            <ul v-if="errors.name">
              <li v-for="error in errors.name" :key="error" class="validation_message">
                {{ error }}
              </li>
            </ul>
          </div>
          <div class="input-area">
            <label for="email">メールアドレス</label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              placeholder="test@example.com"
            />
            <ul v-if="errors.email">
              <li v-for="error in errors.email" :key="error" class="validation_message">
                {{ error }}
              </li>
            </ul>
          </div>
          <div class="modal-btn-area">
            <button class="btn" @click="submit">招待</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import Pagination from '../../../components/admin/Pagination.vue';
import { ref, reactive, computed, onMounted } from 'vue';
import { useStore } from 'vuex';
import axios from 'axios';
import { STATUS } from '../../../util/status';

const store = useStore();

const current_page = ref(1);
const last_page = ref(1);
const total = ref(0);
const admins = ref([]);
const isShowModal = ref(false);
const form = reactive({
  name: null,
  email: null,
});

onMounted(() => {
  getAdmins();
});

// computed
const apiStatus = computed(() => store.getters['admin/apiStatus']);
const errors = computed(() => store.getters['admin/inviteErrors']);
const loginAdminId = computed(() => store.state.admin.admin.id);

// methods
const getAdmins = async () => {
  const response = await axios.get(`/api/admin/admins?page=${current_page.value}`);

  if (response.status === STATUS.OK) {
    last_page.value = response.data.admins.last_page;
    total.value = response.data.admins.total;
    admins.value = response.data.admins.data;
    return;
  }

  store.commit('alert/setAlert', {
    message: response.statusText,
    type: 'error',
  });
};

const deleteAdmin = async (id) => {
  if (!confirm('削除して宜しいですか？')) {
    return;
  }

  const response = await axios.delete(`/api/admin/admins/${id}`);

  if (response.status === STATUS.NO_CONTENT) {
    store.commit('alert/setAlert', {
      message: '管理者を削除しました。',
      type: 'success',
    });
    current_page.value = 1;
    getAdmins();
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
  getAdmins();
};

const closeModal = () => {
  isShowModal.value = false;
  form.name = null;
  form.email = null;
};

const openModal = () => {
  isShowModal.value = true;
};

const submit = async () => {
  await store.dispatch('admin/invite', form);

  if (apiStatus.value) {
    closeModal();
    store.commit('alert/setAlert', {
      message: '招待メールを送信しました。',
      type: 'success',
    });
    getAdmins();
  }
};
</script>

<style scoped lang="scss">
.invite-btn-area {
  text-align: right;
}

.modal-wrap {
  .modal-bg {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background-color: rgba(0,0,0,.7);
  }

  .modal-area {
    position: fixed;
    top: 50%;
    left: 50%;
    -webkit-transform: translate(-50%, -50%);
          transform: translate(-50%, -50%);
    background-color: #fff;
    padding: 30px;
    width: 500px;

    .modal-close-btn-area {
      text-align: right;
      margin-bottom: 20px;
      .modal-close-btn {
        cursor: pointer;
      }
    }

    .input-area {
      margin-bottom: 10px;
      label {
        display: inline-block;
        width: 120px;
        text-align: right;
        margin-right: 10px;
      }
      input {
        font-size: 16px;
        width: calc(100% - 130px);
      }
      .validation_message {
        margin-left: 130px;
      }
    }

    .modal-btn-area {
      text-align: center;
      margin-top: 20px;
    }
  }
}
</style>
