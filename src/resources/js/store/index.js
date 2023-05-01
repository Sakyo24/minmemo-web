import { createStore } from 'vuex';
import auth from './auth.js';
import admin from './admin.js';
import error from './error.js';

const store = createStore({
  modules: {
    auth: auth,
    admin: admin,
    error: error,
  },
});

export default store;
