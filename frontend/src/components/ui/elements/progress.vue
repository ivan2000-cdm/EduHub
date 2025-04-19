<template functional>
  <div
    :class="['progress', 'position-relative', props.size && `progress-${props.size}`]"
    style="width: 100%"
  >
    <div
      class="position-absolute text-center"
      style="width: 100%"
    >
      {{ props.percent }}%
    </div>
    <div
      :class="['progress-bar', props.color && `progress-bar-${props.color}`]"
      :style="`width: ${props.percent}%`"
    />
  </div>
</template>

<script>
// Прогресс бар
export default {
  name: 'LProgress',
  props: {
    size: {
      type: String,
      default: 'xl'
    },
    color: {
      type: String,
      default: 'primary'
    },
    percent: {
      type: Number,
      required: true,
      default: 10
    }
  }
}
</script>
