import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import axios from 'axios'

import App from './App.vue'
import router from './router'
import Vue2Transitions from 'vue2-transitions'
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap-icons/font/bootstrap-icons.css'

const app = createApp(App)

// Делаем $axios глобальным
app.config.globalProperties.$axios = axios.create({
  baseURL: 'http://localhost:3000/', // или твой backend URL
})

app.use(createPinia())
app.use(router)
app.use(Vue2Transitions)

app.mount('#app')
