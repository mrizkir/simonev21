import { defineStore } from 'pinia'

export const userStore = defineStore('userStore', {
  state: () => ({
    token: '',
    profile: '',
    permissions: {},
  }),
})