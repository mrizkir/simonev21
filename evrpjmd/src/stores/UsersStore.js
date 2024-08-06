import { defineStore } from 'pinia'

export const usesUserStore = defineStore('userStore', {
  state: () => ({
    access_token: null,
    token_type: null,
    expires_in: null,
    user: null,
  }),
  getters: {
    Authenticated() {
      return this.access_token != null && this.user != null
    },
    AccessToken() {
      return this.access_token
    },
    Token() {
      return this.token_type + ' ' + this.access_token
    },
    Roles() {
      return this.user.role
    },
    DefaultRole() {
      if (this.user === null || typeof this.user === 'undefined') {
        return 'N.A'
      } else {
        return this.user.default_role
      }
    },
    Role() {
      var role = ''
      if (this.access_token != null && this.user != null) {
        let roles = this.user.role
        for (var i = 0; i < roles.length; i++) {
          switch (roles[i]) {
            default:
              role = role + '[' + roles[i] + '] '
          }
        }
      }
      return role
    },
    User() {
      return this.user
    },
    PeriodeRPJMD() {
      if(this.user == null) {
        return '-'
      } else {
        let periode_rpjmd = this.user.periode_rpjmd
        return periode_rpjmd        
      }
    },
    AttributeUser(key) {
      return this.user == null ? '' : this.user[key]
    },
    Can(name) {
      if (this.user == null) {
        return false
      } else if (this.user.issuperadmin) {
        return true
      } else {
        let permissions = this.user.permissions
        return name in permissions ? true : false
      }
    },
    TahunSelected() {
      if (this.user == null) {
        return 0
      } else {
        return this.user.tahun_selected
      }
    },
  },
  actions: {
    afterLoginSuccess(data_user) {
      this.access_token = data_user.token.access_token
      this.token_type = data_user.token.token_type
      this.expires_in = data_user.token.expires_in
      this.user = data_user.user
    },
    updateFoto() {

    },
    logout() {    
      this.access_token = null
      this.token_type = null
      this.expires_in = null
      this.user = null
    },
  },
  persist: {
    key: 'evarpjmd',
  },
})