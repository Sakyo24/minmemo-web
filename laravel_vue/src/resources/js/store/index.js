import { createStore } from 'vuex';
import test from './test.js';

const store = createStore({
  modules: {
    test: test,
  },
});

export default store;
