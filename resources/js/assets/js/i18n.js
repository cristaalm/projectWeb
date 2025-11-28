import { createI18n } from 'vue-i18n'
import dictionary from './dictionary'

// i18n.js

// Create a new instance of the i18n plugin
const i18n = createI18n({
  locale: 'en', // Set the default locale to English
  messages: dictionary // Set the messages for translation using the imported dictionary
})

export default i18n // Export the i18n instance for use in other parts of the application
