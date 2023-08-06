<template>
  <div class="login">
    <h2>管理者ログイン</h2>
    <form @submit.prevent>
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
      <div class="input-area">
        <label for="password">パスワード</label>
        <input
          id="password"
          v-model="form.password"
          type="password"
          placeholder="password"
        />
        <ul v-if="errors.password">
          <li v-for="error in errors.password" :key="error" class="validation_message">
            {{ error }}
          </li>
        </ul>
      </div>
      <div>
        <p>
          パスワードを忘れた場合は、
          <router-link class="password-reset-link" to="/admin/forgot-password">こちら</router-link>
        </p>
      </div>
      <div v-if="message" class="error validation_message">
        {{ message }}
      </div>
      <button class="btn" @click="submit">ログイン</button>
    </form>
  </div>
</template>

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
const errors = computed(() => store.getters['admin/loginErrors']);
const message = computed(() => store.getters['admin/unauthrizedMessage']);

// methods
const submit = async () => {
  await store.dispatch('admin/login', form);
  router.push('/admin/users');
};
</script>
