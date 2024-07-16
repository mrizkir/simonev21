import { defineStore } from 'pinia'

export const usesUserStore = defineStore('userStore', {
  state: () => ({
    token: null,
    profile: null,
    permissions: null,
  }),
  getters: {
    getToken() {
      return this.token
    },
    getProfile() {
      let profile = this.profile
      return this.profile
    },
    getPermissions() {
      return this.permissions
    },
  },
  actions: {
    afterLoginSuccess(user) {
      this.token = user.token
      this.profile = user.profile
      this.permissions = user.permissions
    },
    updateFoto() {

    },
    logout() {

    },
  },
  persist: {
    key: 'evarpjmd',
  },
})