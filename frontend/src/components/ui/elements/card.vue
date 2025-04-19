<template>
  <div
    :class="{
      'card': true,
      'hover': hover
    }"
    @click="$emit('click')"
  >
    <div v-if="$slots.header" class="card-header">
      <slot name="header" />
    </div>
    <div v-if="$slots.body" class="card-body">
      <slot name="body" />
    </div>
    <div v-if="$slots.footer" class="card-footer">
      <slot name="footer" />
    </div>
  </div>
</template>

<script>
export default {
  name: 'LCard',
  props: {
    hover: {
      type: Boolean,
      default: false
    }
  }
}
</script>

<style lang="scss" scoped>
.card.hover:hover {
  background-color: #f0f0f0;
  cursor: pointer;
}
.card.card-outline-tabs .card-header a {
  border-top: none;
  &:hover {
    border-top: none;
  }
}
</style>
