<template>
  <div class="app-wrapper">
    <RouterView />
  </div>
</template>

<script setup>
import { RouterView } from 'vue-router'

</script>

<style>
/* Глобальные стили для всей страницы */
html, body, #app {
  margin: 0;
  padding: 0;
  height: 100%;
  width: 100%;
  background-color: #f0f0f0; /* светло-серый фон */
  box-sizing: border-box;
  overflow-x: hidden;
}

/* Контейнер для всего контента */
.app-wrapper {
  display: flex;
  justify-content: center; /* Центрируем по горизонтали */
  align-items: center; /* Центрируем по вертикали */
  height: 100vh; /* Высота на весь экран */
  width: 100%; /* Ширина на весь экран */
  //background-color: #3b82f6; /* синий фон для всей рабочей области */
}
</style>
