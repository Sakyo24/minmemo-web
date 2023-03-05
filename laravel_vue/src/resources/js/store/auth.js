import axios from 'axios';
import { STATUS } from '../util/status';

const auth = {
  namespaced: true,
  state: () => ({
    user: null,
    apiStatus: null,
    registerErrors: {},
    loginErrors: {},
    unauthrizedMessage: '',
  }),
  getters: {
    isLogged(state) {
      return !!state.user;
    },
    apiStatus(state) {
      return state.apiStatus;
    },
    registerErrors(state) {
      return state.registerErrors;
    },
    loginErrors(state) {
      return state.loginErrors;
    },
    unauthrizedMessage(state) {
      return state.unauthrizedMessage;
    },
  },
  mutations: {
    setUser(state, user) {
      state.user = user;
    },
    setApiStatus(state, apiStatus) {
      state.apiStatus = apiStatus;
    },
    setRegisterErrors(state, registerErrors) {
      state.registerErrors = registerErrors;
    },
    setLoginErrors(state, loginErrors) {
      state.loginErrors = loginErrors;
    },
    setUnauthrizedMessage(state, unauthrizedMessage) {
      state.unauthrizedMessage = unauthrizedMessage;
    },
  },
  actions: {
    /**
     * ユーザー登録アクション
     *
     * @param {*} context
     * @param {*} data
     */
    async register(context, data) {
      // 初期化
      context.commit('setApiStatus', null);
      context.commit('setUser', null);
      context.commit('setRegisterErrors', {});

      // API実行
      const response = await axios.post('/register', data);

      // 成功時
      if (response.status === STATUS.CREATED) {
        context.commit('setApiStatus', true);
        return false;
      }

      // エラー時
      context.commit('setApiStatus', false);
      if (response.status === STATUS.UNPROCESSABLE_ENTITY) {
        context.commit('setRegisterErrors', response.data.errors);
      } else {
        context.commit('error/setCode', response.status, { root: true });
      }
    },
    /**
     * ログインアクション
     *
     * @param {*} context
     * @param {*} data
     */
    async login(context, data) {
      // 初期化
      context.commit('setApiStatus', null);
      context.commit('setUser', null);
      context.commit('setLoginErrors', {});
      context.commit('setUnauthrizedMessage', '');

      // API実行
      const response = await axios.post('/login', data);

      // 成功時
      if (response.status === STATUS.OK) {
        context.commit('setApiStatus', true);
        context.commit('setUser', response.data.user);
        return false;
      }

      // エラー時
      context.commit('setApiStatus', false);
      if (response.status === STATUS.UNPROCESSABLE_ENTITY) {
        context.commit('setLoginErrors', response.data.errors);
      } else if (response.status === STATUS.UNAUTHORIZED) {
        context.commit('setUnauthrizedMessage', response.data.message);
      } else {
        context.commit('error/setCode', response.status, { root: true });
      }
    },
    /**
     * ログアウトアクション
     *
     * @param {*} context
     */
    async logout(context) {
      // 初期化
      context.commit('setApiStatus', null);

      // API実行
      const response = await axios.post('/logout');

      // 成功時
      if (response.status === STATUS.NO_CONTENT) {
        context.commit('setApiStatus', true);
        context.commit('setUser', null);
        return false;
      }

      // エラー時
      context.commit('setApiStatus', false);
      context.commit('error/setCode', response.status, { root: true });
    },
    /**
     * ログインユーザー取得アクション
     *
     * @param {*} context
     */
    async getLoginUser(context) {
      // 初期化
      context.commit('setApiStatus', null);
      context.commit('setUser', null);

      // API実行
      const response = await axios.get('/api/auth');

      // 成功時
      if (response.status === STATUS.OK) {
        context.commit('setApiStatus', true);
        context.commit('setUser', response.data.user);
        return false;
      }
      if (response.status === STATUS.NO_CONTENT) {
        context.commit('setApiStatus', true);
        context.commit('setUser', null);
        return false;
      }

      // エラー時
      context.commit('setApiStatus', false);
      context.commit('error/setCode', response.status, { root: true });
    },
  },
};

export default auth;
