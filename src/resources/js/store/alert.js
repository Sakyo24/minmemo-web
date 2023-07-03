const alert = {
  namespaced: true,
  state: () => ({
    messgate: '',
    type: 'success'
  }),
  getters: {
    getAlertMessage(state) {
      return state.messgate;
    },
    getAlertType(state) {
      return state.type;
    },
  },
  mutations: {
    setAlert(state, { messgate, type, timeout }) {
      if (typeof messgate === 'undefined') {
        state.messgate = 'エラーが発生しました。';
      } else {
        state.messgate = messgate;
      }

      if (typeof type !== 'undefined') {
        state.type = type;
      }

      if (typeof timeout === 'undefined') {
        timeout = 3000;
      }
  
      setTimeout(() => {
        state.messgate = '';
        state.type = 'success';
      }, timeout);
    },
  },
};

export default alert;