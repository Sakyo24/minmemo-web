import { createStore } from 'vuex';
import auth from './auth.js';
import error from './error.js';

const store = createStore({
  modules: {
    auth: auth,
    error: error,
  },
});

export default store;
