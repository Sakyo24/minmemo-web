import axios from 'axios';
import { STATUS } from '../util/status';

const admin = {
  namespaced: true,
  state: () => ({
    admin: null,
    apiStatus: null,
    loginErrors: {},
    unauthrizedMessage: '',
    inviteErrors: {},
  }),
  getters: {
    isAdmin(state) {
      return !!state.admin;
    },
    apiStatus(state) {
      return state.apiStatus;
    },
    loginErrors(state) {
      return state.loginErrors;
    },
    unauthrizedMessage(state) {
      return state.unauthrizedMessage;
    },
    inviteErrors(state) {
      return state.inviteErrors;
    },
  },
  mutations: {
    setAdmin(state, admin) {
      state.admin = admin;
    },
    setApiStatus(state, apiStatus) {
      state.apiStatus = apiStatus;
    },
    setLoginErrors(state, loginErrors) {
      state.loginErrors = loginErrors;
    },
    setUnauthrizedMessage(state, unauthrizedMessage) {
      state.unauthrizedMessage = unauthrizedMessage;
    },
    setInviteErrors(state, inviteErrors) {
      state.inviteErrors = inviteErrors;
    },
  },
  actions: {
    /**
     * ログインアクション
     *
     * @param {*} context
     * @param {*} data
     */
    async login(context, data) {
      // 初期化
      context.commit('setApiStatus', null);
      context.commit('setAdmin', null);
      context.commit('setLoginErrors', {});
      context.commit('setUnauthrizedMessage', '');

      // API実行
      const response = await axios.post('/admin/login', data);

      // 成功時
      if (response.status === STATUS.OK) {
        context.commit('setApiStatus', true);
        context.commit('setAdmin', response.data.admin);
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
      const response = await axios.post('/admin/logout');

      // 成功時
      if (response.status === STATUS.NO_CONTENT) {
        context.commit('setApiStatus', true);
        context.commit('setAdmin', null);
        return false;
      }

      // エラー時
      context.commit('setApiStatus', false);
      context.commit('error/setCode', response.status, { root: true });
    },
    /**
     * ログイン管理者取得アクション
     *
     * @param {*} context
     */
    async getLoginAdmin(context) {
      // 初期化
      context.commit('setApiStatus', null);
      context.commit('setAdmin', null);

      // API実行
      const response = await axios.get('/api/admin');

      // 成功時
      if (response.status === STATUS.OK) {
        context.commit('setApiStatus', true);
        context.commit('setAdmin', response.data.admin);
        return false;
      }
      if (response.status === STATUS.NO_CONTENT) {
        context.commit('setApiStatus', true);
        context.commit('setAdmin', null);
        return false;
      }

      // エラー時
      context.commit('setApiStatus', false);
      context.commit('error/setCode', response.status, { root: true });
    },
    /**
     * 招待アクション
     *
     * @param {*} context
     * @param {*} data
     */
    async invite(context, data) {
      // 初期化
      context.commit('setApiStatus', null);
      context.commit('setInviteErrors', {});

      // API実行
      const response = await axios.post('/api/admin/invite', data);

      // 成功時
      if (response.status === STATUS.CREATED) {
        context.commit('setApiStatus', true);
        return false;
      }

      // エラー時
      context.commit('setApiStatus', false);
      if (response.status === STATUS.UNPROCESSABLE_ENTITY) {
        context.commit('setInviteErrors', response.data.errors);
      } else {
        context.commit('error/setCode', response.status, { root: true });
      }
    },
  },
};

export default admin;
