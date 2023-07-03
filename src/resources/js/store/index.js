import { createStore } from 'vuex';
import admin from './admin.js';
import auth from './auth.js';
import alert from './alert.js';
import error from './error.js';

const store = createStore({
  modules: {
    admin: admin,
    auth: auth,
    alert: alert,
    error: error,
  },
});

export default store;
