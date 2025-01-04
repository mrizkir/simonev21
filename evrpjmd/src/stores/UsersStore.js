import { defineStore } from 'pinia'
import { getActivePinia } from 'pinia'

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
    DaftarTahunAnggaran() {
      let periode_rpjmd = this.user.periode_rpjmd
      let TA_AWAL = periode_rpjmd.TA_AWAL
      let TA_AKHIR = periode_rpjmd.TA_AKHIR

      var daftar_ta = []
      for(var i = TA_AWAL; i <= TA_AKHIR; i++) {
        daftar_ta.push({
          'value': i,
          'title': i,
        })
      }

      return daftar_ta
    },
    TahunAwalPeriodeRPMJD() {
      let periode_rpjmd = this.user.periode_rpjmd
      let TA_AWAL = periode_rpjmd.TA_AWAL
      return TA_AWAL
    },
    TahunAkhirPeriodeRPMJD() {
      let periode_rpjmd = this.user.periode_rpjmd
      let TA_AKHIR = periode_rpjmd.TA_AKHIR
      return TA_AKHIR
    },
  },
  actions: {
    afterLoginSuccess(data_user) {
      this.access_token = data_user.token.access_token
      this.token_type = data_user.token.token_type
      this.expires_in = data_user.token.expires_in
      this.user = data_user.user
    },
    updateTahunAnggaran(tahun) {
      console.log(tahun)
      this.user.tahun_selected = tahun
    },
    updateFoto() {

    },
    logout() {
      this.access_token = null
      this.token_type = null
      this.expires_in = null
      this.user = null

      getActivePinia()._s.forEach(store => store.$reset());
    },
  },
})