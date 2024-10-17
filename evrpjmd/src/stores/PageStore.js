import { defineStore } from 'pinia'

export const usesPageStore = defineStore('usesPageStore', {
  state: () => ({
    pages: [],
  }),
  getters: {
    
  },
  actions: {
    AtributeValueOfPage(name, key) {
      let page = this.pages.find(halaman => halaman.name == name);
      console.log(page)
      // return page[key]
    }
  },
})