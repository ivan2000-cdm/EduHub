<template>
  <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom px-4 py-2 d-flex justify-content-between align-items-center">
    <!-- Кнопка сворачивания -->
    <button class="toggle-button btn btn-outline-primary" @click="toggleSidebar">
      <i :class="collapsed ? 'bi bi-chevron-double-right' : 'bi bi-chevron-double-left'"></i>
    </button>

    <!-- Breadcrumbs -->
    <div class="breadcrumb mb-0">
      <span v-for="(segment, index) in breadcrumbSegments" :key="index">
        <span v-if="index > 0"> / </span>
        {{ segment }}
      </span>
    </div>

    <!-- Кнопка Выйти -->
    <button class="btn btn-sm btn-outline-danger" @click="logout">
      <i class="bi bi-box-arrow-right me-1"></i> Выйти
    </button>
  </nav>
</template>

<script>
export default {
  name: 'Navbar',
  props: {
    collapsed: Boolean
  },
  computed: {
    breadcrumbSegments() {
      return this.$route.path
        .split('/')
        .filter(Boolean)
        .map(s => this.mapRouteName(s));
    }
  },
  methods: {
    logout() {
      localStorage.removeItem('auth_token');
      this.$router.push('/login');
    },
    mapRouteName(segment) {
      const map = {
        dictionary: 'Справочники',
        applicants: 'Заявители',
        // добавь другие ключи при необходимости
      };
      return map[segment] || segment.charAt(0).toUpperCase() + segment.slice(1);
    },
    toggleSidebar() {
      this.$emit('toggle-sidebar');
    }
  }
}
</script>

<style scoped>
.breadcrumb {
  font-size: 0.9rem;
  font-weight: 500;
}
.toggle-button {
  margin-left: 1rem;
}
</style>
