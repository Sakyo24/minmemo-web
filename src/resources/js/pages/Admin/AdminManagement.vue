<script setup>
import { ref, reactive, computed } from 'vue';
import { useStore } from 'vuex';

import Alert from '../../components/Alert.vue';

const store = useStore();

// data
const isShowModal = ref(false);
const isShowAlert = ref(false);
const form = reactive({
  name: null,
  email: null,
});

// computed
const apiStatus = computed(() => store.getters['admin/apiStatus']);
const errors = computed(() => store.getters['admin/inviteErrors']);

// methods
const toggleModal = () => {
  isShowModal.value = !isShowModal.value;
};
const closeForm = () => {
  isShowModal.value = false;
  form.name = null;
  form.email = null;
};
const submit = async () => {
  await store.dispatch('admin/invite', form);

  if (apiStatus.value) {
    closeForm();
    isShowAlert.value = true;
    setTimeout(() => (isShowAlert.value = false), 3000);
  }
};
</script>

<template>
  <div class="admin-management">
    <h1>管理者</h1>
    <button @click="toggleModal">管理者の招待</button>
    <div v-if="isShowModal">
      <form @submit.prevent>
        <div>
          <label for="name">名前</label>
          <input id="name" v-model="form.name" type="text" placeholder="太郎" />
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
        <button @click="submit">招待</button>
      </form>
    </div>
    <Alert v-if="isShowAlert">招待メールを送信しました</Alert>
  </div>
</template>
