import { createApp } from 'vue'

// Vuetify
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

//css
import '@/style.css'

//js
import router from '@/routes'
import store from '@/stores'

//components
import App from './App.vue'
const vuetify = createVuetify({
  components,
  directives,
})

createApp(App)
  .use(store)
  .use(router)
  .use(vuetify)
  .mount('#app')
