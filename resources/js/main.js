import App from '@/App.vue'
import { registerPlugins } from '@core/utils/plugins'
import { createApp, watch } from 'vue'
import { IMaskDirective } from 'vue-imask'
import './assets/css/app.css' // tailwindcss
import { i18n } from '@/i18n'

// Styles
import '@core-scss/template/index.scss'
import '@layouts/styles/index.scss'
import '@styles/styles.scss'


import decimalTwo from '@/directives/decimalTwo'
import letterOnly from '@/directives/letterOnly'
import numberOnly from '@/directives/numberOnly'

const app = createApp(App)

// Register plugins
registerPlugins(app)

// Register i18n
app.use(i18n)

// Register IMask
app.directive('letter-only', letterOnly)
app.directive('number-only', numberOnly)
app.directive('decimal-two', decimalTwo)
app.directive('imask', IMaskDirective)

watch(() => i18n.global.locale.value, lang => { document.documentElement.lang = lang }, { immediate: true })

// Mount vue app
app.mount('#app')
