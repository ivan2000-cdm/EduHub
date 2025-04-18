<template>
  <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center bg-white px-3">
    <div class="card shadow p-4" style="max-width: 500px; width: 100%;">
      <h2 class="card-title text-center mb-4">Авторизация</h2>
      <form @submit.prevent="handleLogin">
        <div class="mb-3">
          <label for="username" class="form-label">Имя пользователя</label>
          <input
            v-model="username"
            type="text"
            class="form-control"
            id="username"
            required
          />
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Пароль</label>
          <input
            v-model="password"
            type="password"
            class="form-control"
            id="password"
            required
          />
        </div>
        <button type="submit" class="btn btn-primary w-100">Войти</button>
      </form>
    </div>
  </div>
</template>

<script>
import api from '@/services/api'

export default {
  data() {
    return {
      username: '',
      password: ''
    }
  },
  created() {
    const token = localStorage.getItem('auth_token')
    if (token) {
      this.$router.push('/')
    }
  },
  methods: {
    async handleLogin() {
      try {
        const response = await api.post('/login', {
          username: this.username,
          password: this.password
        })
        localStorage.setItem('auth_token', response.data.token)
        this.$router.push('/')
      } catch (error) {
        console.error('Login failed:', error)
      }
    }
  }
}
</script>

