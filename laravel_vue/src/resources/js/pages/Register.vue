<template>
  <div class="register">
    <h1>Register画面</h1>
    <form @submit.prevent>
      <div>
        <label for="name">ユーザー名</label>
        <input
          id="name"
          v-model="form.name"
          type="text"
          placeholder="ユーザー名"
        />
        <ul v-if="errors.name">
          <li v-for="error in errors.name" :key="error">
            {{ error }}
          </li>
        </ul>
      </div>
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
      <div>
        <label for="password_confirmation">確認用パスワード</label>
        <input
          id="password_confirmation"
          v-model="form.password_confirmation"
          type="password"
          placeholder="password"
        />
        <ul v-if="errors.password_confirmation">
          <li v-for="error in errors.password_confirmation" :key="error">
            {{ error }}
          </li>
        </ul>
      </div>
      <button @click="submit">登録</button>
    </form>
    <router-link to="/login">ログイン画面へ</router-link>
  </div>
</template>

<script>
import { reactive, computed } from 'vue';
import { useStore } from 'vuex';
import { useRouter } from 'vue-router';

export default {
  setup() {
    const store = useStore();
    const router = useRouter();

    // data
    const form = reactive({
      name: null,
      email: null,
      password: null,
      password_confirmation: null,
    });

    // computed
    const errors = computed(() => store.getters['auth/registerErrors']);

    // methods
    const submit = async () => {
      await store.dispatch('auth/register', form);
      router.push('/');
    };

    return {
      form,
      errors,
      submit,
    };
  },
};
</script>
