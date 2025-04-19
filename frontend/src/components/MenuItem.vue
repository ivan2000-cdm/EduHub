<template>
  <li>
    <!-- Пункт меню -->
    <router-link
      v-if="!hasChildren"
      :to="item.path"
      class="nav-link d-flex justify-content-between"
      active-class="active"
    >
      {{ item.title }}
    </router-link>

    <!-- Пункт с вложениями -->
    <div v-else>
      <a
        href="#"
        class="nav-link d-flex justify-content-between"
        @click.prevent="toggleSubMenu"
        :class="{ active: isOpen }"
      >
        {{ item.title }}
        <span class="ml-2">
          <i :class="isOpen ? 'bi bi-caret-down' : 'bi bi-caret-left'"></i>
        </span>
      </a>

      <ul
        v-if="hasChildren"
        :class="['collapse submenu', { show: isOpen }]"
        :id="'submenu-' + uniqueId"
      >
        <menu-item
          v-for="(child, idx) in item.children"
          :key="idx"
          :item="child"
        />
      </ul>
    </div>
  </li>
</template>

<script>
export default {
  name: 'MenuItem',
  props: {
    item: Object
  },
  data() {
    return {
      isOpen: false,
      uniqueId: Math.random().toString(36).substring(2)
    }
  },
  computed: {
    hasChildren() {
      return this.item.children && this.item.children.length > 0
    }
  },
  methods: {
    toggleSubMenu() {
      this.isOpen = !this.isOpen
    }
  }
}
</script>

<style scoped>
.submenu {
  list-style-type: none;
}
.submenu li {
  list-style-type: none;
}
.router-link-active {
  font-weight: bold;
  color: #0d6efd;
}
</style>
