import { defineStore } from 'pinia'

export const usesUserStore = defineStore('userStore', {
  state: () => ({
    token: '',
    profile: 'test',
    permissions: {},
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
})