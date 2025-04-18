<template>
  <div class="layout-container">
    <!-- Боковая панель -->
    <Sidebar :collapsed="isSidebarCollapsed" />

    <!-- Контент с навбаром и страницей -->
    <div class="content">
      <Navbar :collapsed="isSidebarCollapsed" @toggle-sidebar="toggleSidebar" />

      <!-- Основной контент -->
      <div class="m-2">
        <router-view />
      </div>
    </div>
  </div>
</template>

<script>
import Sidebar from '@/components/Sidebar.vue'
import Navbar from '@/components/Navbar.vue'

export default {
  components: {
    Sidebar,
    Navbar
  },
  data() {
    return {
      isSidebarCollapsed: false
    }
  },
  methods: {
    toggleSidebar() {
      this.isSidebarCollapsed = !this.isSidebarCollapsed
    }
  }
}
</script>

<style scoped>
.layout-container {
  display: flex;
  min-height: 100vh;
  position: relative;
}
.content {
  flex: 1;
}
</style>

