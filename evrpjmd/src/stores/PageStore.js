import { defineStore } from 'pinia'

export const usesPageStore = defineStore('usesPageStore', {
  state: () => ({
    pages: [],
  }),
  getters: {
    
  },
  actions: {
    addToPages(page) {      
      let found = this.pages.find(halaman => halaman.name == page.name)
      if (!found) {
        this.pages.push(page)
      }
    },
    getPage(name) {
      let page = this.pages.find(halaman => halaman.name == name);
      return page;
    },
    replacePage(page, index) {
      this.pages[index] = page
    },
    AtributeValueOfPage(name, key) {
      let page = this.pages.find(halaman => halaman.name == name)      
      return page[key]
    },
    updatePage(page) {
      var i
      for (i = 0; i < this.pages.length; i++) {
        if (this.pages[i].name == page.name) {
          break
        }
      }
      this.replacePage(page, i)
    },
  },
})