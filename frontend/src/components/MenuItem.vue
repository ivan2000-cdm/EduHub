<template>
  <li>
    <a
      :href="item.path"
      class="nav-link d-flex justify-content-between"
      @click.prevent="toggleSubMenu"
      data-bs-toggle="collapse"
      :data-bs-target="'#submenu-' + uniqueId"
    >
      {{ item.title }}
      <!-- Иконка для обозначения раскрытия вложений -->
      <span v-if="item.children && item.children.length > 0" class="ml-2">
        <i :class="isOpen ? 'bi bi-caret-down' : 'bi bi-caret-left'"></i>
      </span>
    </a>

    <!-- Вложенные пункты -->
    <ul
      v-if="item.children && item.children.length > 0"
      :class="['collapse', { show: isOpen }]"
      :id="'submenu-' + uniqueId"
      class="submenu"
    >
      <menu-item
        v-for="(child, idx) in item.children"
        :key="idx"
        :item="child"
      />
    </ul>
  </li>
</template>

<script>
export default {
  name: 'MenuItem',
  props: {
    item: Object, // Данные для текущего пункта меню
  },
  data() {
    return {
      isOpen: false, // Состояние открытого подменю
      uniqueId: Math.random().toString(36).substring(2), // Генерация уникального идентификатора для каждого меню
    };
  },
  methods: {
    // Переключение состояния подменю
    toggleSubMenu() {
      this.isOpen = !this.isOpen;
    },
  },
};
</script>

<style scoped>
/* Убираем маркеры и отступы для вложенных списков */
.submenu {
  list-style-type: none; /* Убираем маркеры списка */
}

.submenu li {
  list-style-type: none; /* Убираем маркеры списка */
}
</style>
