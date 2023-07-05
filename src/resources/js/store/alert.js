const alert = {
  namespaced: true,
  state: () => ({
    message: '',
    type: 'success'
  }),
  getters: {
    getAlertMessage(state) {
      return state.message;
    },
    getAlertType(state) {
      return state.type;
    },
  },
  mutations: {
    setAlert(state, { message, type, timeout }) {
      if (typeof message === 'undefined') {
        state.message = 'エラーが発生しました。';
      } else {
        state.message = message;
      }

      if (typeof type !== 'undefined') {
        state.type = type;
      }

      if (typeof timeout === 'undefined') {
        timeout = 3000;
      }
  
      setTimeout(() => {
        state.message = '';
        state.type = 'success';
      }, timeout);
    },
  },
};

export default alert;