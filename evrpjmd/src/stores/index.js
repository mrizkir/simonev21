import { createPinia } from 'pinia'
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'
import { piniaEncryptionPlugin } from '@/plugins/piniaEncryption';

const pinia = createPinia()
pinia.use(piniaPluginPersistedstate)
pinia.use(piniaEncryptionPlugin)

export default pinia;