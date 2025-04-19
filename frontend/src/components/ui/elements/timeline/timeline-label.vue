<template functional>
  <div class="time-label">
    <span :class="props.bg">
      <slot />
    </span>
  </div>
</template>

<script>
export default {
  name: 'LTimelineLabel',
  props: {
    bg: {
      type: String,
      default: 'bg-yellow'
    }
  }
}
</script>
