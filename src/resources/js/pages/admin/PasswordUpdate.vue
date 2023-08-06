<template>
  <div class="password-update">
    <div class="page-wrap">
      <h2 class="page-title">パスワード変更</h2>
      <div class="contents-area">
        <form @submit.prevent>
          <div class="input-area">
            <label for="current_password">現在のパスワード</label>
            <input
              id="current_password"
              v-model="current_password"
              type="password"
              placeholder="password"
            />
            <ul v-if="errors">
              <li v-for="error in errors.current_password" :key="error" class="validation_message">
                {{ error }}
              </li>
            </ul>
          </div>
          <div class="input-area">
            <label for="new_password">新しいパスワード</label>
            <input
              id="new_password"
              v-model="new_password"
              type="password"
              placeholder="password"
            />
            <ul v-if="errors">
              <li v-for="error in errors.new_password" :key="error" class="validation_message">
                {{ error }}
              </li>
            </ul>
          </div>
          <div class="input-area">
            <label for="new_password_confirmation">新しいパスワード(確認)</label>
            <input
              id="new_password_confirmation"
              v-model="new_password_confirmation"
              type="password"
              placeholder="password"
            />
            <ul v-if="errors">
              <li v-for="error in errors.new_password_confirmation" :key="error" class="validation_message">
                {{ error }}
              </li>
            </ul>
          </div>
          <div class="btn-area">
            <button class="btn" @click="submit">変更</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useStore } from 'vuex';
import axios from 'axios';
import { STATUS } from '../../util/status';

const store = useStore();

// data
const current_password = ref(null);
const new_password = ref(null);
const new_password_confirmation = ref(null);
const errors = ref(null);

// methods
const submit = async () => {
  errors.value = null;

  const data = {
    current_password: current_password.value,
    new_password: new_password.value,
    new_password_confirmation: new_password_confirmation.value,
  };
  const response = await axios.post('/api/admin/password/update', data);

  if (response.status === STATUS.OK) {
    store.commit('alert/setAlert', {
      message: 'パスワードを変更しました。',
      type: 'success',
    });
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

<style scoped lang="scss">
.contents-area {
  width: 500px;
  margin: auto;

  .input-area {
    margin-bottom: 20px;

    label {
      display: inline-block;
      width: 180px;
      margin-right: 20px;
    }
    input {
      display: inline-block;
      width: calc(100% - 200px);
      font-size: 16px;
    }
  }

  .btn-area {
    text-align: center;
  }
}
</style>
