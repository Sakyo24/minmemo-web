const error = {
  namespaced: true,
  state: () => ({
    code: null,
  }),
  getters: {
    code(state) {
      return state.code;
    },
  },
  mutations: {
    setCode(state, code) {
      state.code = code;
    },
  },
};

export default error;
