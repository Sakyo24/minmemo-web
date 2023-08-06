<template>
  <div class="password-reset">
    <h2>パスワードリセット メール送信</h2>
    <form @submit.prevent>
      <div class="input-area">
        <label for="email">メールアドレス</label>
        <input
          id="email"
          v-model="email"
          type="email"
          placeholder="test@example.com"
        />
        <ul v-if="errors">
          <li v-for="error in errors.email" :key="error" class="validation_message">
            {{ error }}
          </li>
        </ul>
      </div>
      <div class="btn-area">
        <router-link class="btn gray-btn" to="/admin/login">戻る</router-link>
        <button class="btn" @click="submit">送信</button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useStore } from 'vuex';
import axios from 'axios';
import { STATUS } from '../../util/status';

const store = useStore();

// data
const email = ref(null);
const errors = ref(null);

// methods
const submit = async () => {
  errors.value = null;

  const data = {
    email: email.value,
  };
  const response = await axios.post('/api/admin/forgot-password', data);

  if (response.status === STATUS.OK) {
    store.commit('alert/setAlert', {
      message: 'メールを送信しました。',
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
