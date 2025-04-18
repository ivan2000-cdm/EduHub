<template>
  <div class="bg-white shadow p-4 rounded">
    <h1 class="mb-3 text-center">Ура! Вы авторизовались!</h1>
    <p v-if="token" class="text-center">Вот ваш токен:</p>
    <div v-if="token" class="bg-light p-3 rounded" style="word-break: break-word; white-space: pre-wrap;">
      {{ token }}
    </div>
    <p v-else class="text-center text-danger">Токен не найден. Пожалуйста, авторизуйтесь снова.</p>
  </div>
</template>

<script>
export default {
  data() {
    return {
      token: ''
    }
  },
  created() {
    this.token = localStorage.getItem('auth_token')
  }
}
</script>
