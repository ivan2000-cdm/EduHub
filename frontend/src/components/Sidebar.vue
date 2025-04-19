<template>
  <div class="sidebar col-md-3 col-lg-2 p-0 bg-light" :class="{'collapsed': collapsed}">
    <div class="d-flex flex-column vh-100">
      <!-- Логотип -->
      <div class="p-2 bg-primary text-white text-center">
        <h4>EduHub</h4>
      </div>

      <!-- Меню -->
      <ul class="nav flex-column p-3">
        <menu-item v-for="(item, index) in menu" :key="index" :item="item" />
      </ul>
    </div>
  </div>
</template>

<script>
import MenuItem from '@/components/MenuItem.vue';
import { fetchMenu } from '@/services/api';

export default {
  name: 'SideBar',
  components: {
    MenuItem,
  },
  props: {
    collapsed: Boolean,
  },
  data() {
    return {
      menu: [],
    };
  },
  async created() {
    try {
      this.menu = await fetchMenu();
    } catch (error) {
      console.error('Не удалось загрузить меню:', error);
    }
  },
};
</script>

<style scoped>
/* Базовая ширина боковой панели */
.sidebar {
  width: 350px;
  transition: width 0.3s ease;
  overflow: hidden;
}

/* Свёрнутая панель */
.collapsed {
  width: 0 !important;
}

/* Скрываем нав и содержимое при сворачивании */
.collapsed .nav,
.collapsed .p-2 {
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.2s ease;
}

.nav,
.p-2 {
  transition: opacity 0.2s ease;
}
</style>
