//state
const getDefaultState = () => {
  return {
    access_token: null,
    token_type: null,
    expires_in: null,
    user: null,
  };
};
const state = getDefaultState();

//mutations
const mutations = {
  setToken: (state, token) => {
    state.access_token = token.access_token;
    state.token_type = token.token_type;
    state.expires_in = token.expires_in;
  },
  setUser: (state, user) => {
    state.user = user;
  },
  setFoto: (state, foto) => {
    state.user['foto'] = foto;
  },
  resetState(state) {
    Object.assign(state, getDefaultState());
  },
};
const getters = {
  Authenticated: state => {
    return state.access_token != null && state.user != null;
  },
  AccessToken: state => {
    return state.access_token;
  },
  Token: state => {
    return state.token_type + " " + state.access_token;
  },
  Roles: state => {
    return state.user.role;
  },
  DefaultRole: state => {
    if (state.user === null || typeof state.user === "undefined") {
      return "N.A";
    } else {
      return state.user.default_role;
    }
  },
  Role: state => {
    var role = "";
    if (state.access_token != null && state.user != null) {
      let roles = state.user.role;
      for (var i = 0; i < roles.length; i++) {
        switch (roles[i]) {
          default:
            role = role + "[" + roles[i] + "] ";
        }
      }
    }
    return role;
  },
  User: state => {
    return state.user;
  },
  AttributeUser: state => key => {
    return state.user == null ? "" : state.user[key];
  },
  can: state => name => {
    if (state.user == null) {
      return false;
    } else if (state.user.issuperadmin) {
      return true;
    } else {
      let permissions = state.user.permissions;
      return name in permissions ? true : false;
    }
  },
  TahunSelected: state => {
    if (state.user == null) {
      return 0;
    } else {
      return state.user.tahun_selected;
    }
  },
};
const actions = {
  afterLoginSuccess({ commit }, data) {
    commit("setToken", data.token);
    commit("setUser", data.user);
  },
  updateFoto({ commit }, foto) {
    commit("setFoto", foto);
  },
  logout({ commit }) {
    commit("resetState");
  },
};
export default {
  namespaced: true,
  state,
  mutations,
  getters,
  actions,
};
