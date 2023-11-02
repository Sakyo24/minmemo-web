<template>
  <div class="password-reset">
    <h2>パスワードリセット</h2>
    <form @submit.prevent>
      <div class="input-area">
        <label for="password">パスワード</label>
        <input
          id="password"
          v-model="password"
          type="password"
          placeholder="password"
        />
        <ul v-if="errors">
          <li v-for="error in errors.password" :key="error" class="validation_message">
            {{ error }}
          </li>
        </ul>
      </div>
      <div class="input-area">
        <label for="password_confirmation">パスワード(確認)</label>
        <input
          id="password_confirmation"
          v-model="password_confirmation"
          type="password"
          placeholder="password"
        />
        <ul v-if="errors">
          <li v-for="error in errors.password_confirmation" :key="error" class="validation_message">
            {{ error }}
          </li>
        </ul>
      </div>
      <button class="btn orange-btn" @click="submit">送信</button>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useStore } from 'vuex';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import { STATUS } from '../util/status';

const route = useRoute();
const router = useRouter();
const store = useStore();

// data
const password = ref(null);
const password_confirmation = ref(null);
const errors = ref(null);

// methods
const submit = async () => {
  const data = {
    password: password.value,
    password_confirmation: password_confirmation.value,
    email: route.query.email,
    token: route.params.token,
  };
  const response = await axios.post('/api/reset-password', data);

  if (response.status === STATUS.OK) {
    store.commit('alert/setAlert', {
      message: 'パスワードをリセットしました。',
      type: 'success',
    });
    router.push('/user/password/reset-finish');
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
