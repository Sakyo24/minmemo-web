<script setup>
import { reactive, computed } from 'vue';
import { useStore } from 'vuex';
import { useRouter } from 'vue-router';

const store = useStore();
const router = useRouter();

// data
const form = reactive({
  email: null,
  password: null,
});

// computed
const errors = computed(() => store.getters['auth/loginErrors']);

// methods
const submit = async () => {
  await store.dispatch('auth/login', form);
  router.push('/');
};
</script>

<template>
  <div class="login">
    <h1>Login画面</h1>
    <form @submit.prevent>
      <div>
        <label for="email">メールアドレス</label>
        <input
          id="email"
          v-model="form.email"
          type="email"
          placeholder="test@example.com"
        />
        <ul v-if="errors.email">
          <li v-for="error in errors.email" :key="error">
            {{ error }}
          </li>
        </ul>
      </div>
      <div>
        <label for="password">パスワード</label>
        <input
          id="password"
          v-model="form.password"
          type="password"
          placeholder="password"
        />
        <ul v-if="errors.password">
          <li v-for="error in errors.password" :key="error">
            {{ error }}
          </li>
        </ul>
      </div>
      <button @click="submit">ログイン</button>
    </form>
    <router-link to="/register">ユーザー登録画面へ</router-link>
  </div>
</template>
