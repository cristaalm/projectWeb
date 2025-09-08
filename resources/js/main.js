import App from '@/App.vue'
import { registerPlugins } from '@core/utils/plugins'
import { createApp } from 'vue'
import './assets/css/app.css' // tailwindcss

// Styles
import '@core-scss/template/index.scss'
import '@layouts/styles/index.scss'
import '@styles/styles.scss'

const app = createApp(App)

// Register plugins
registerPlugins(app)

// Mount vue app
app.mount('#app')
