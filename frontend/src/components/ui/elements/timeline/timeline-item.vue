<template functional>
  <div>
    <i :class="[props.icon, props.iconBg]" />
    <div class="timeline-item">
      <span class="time">
        <i v-if="props.time !== ''" class="fas fa-clock" />
        {{ props.time | timeFilter }}
      </span>
      <h3
        v-if="$slots.header"
        style="display: inherit !important"
        :class="{
          'timeline-header': true,
          'no-border': props.noBorderHeader
        }"
      >
        <slot name="header" />
      </h3>
      <div
        v-if="$slots.body"
        class="timeline-body"
      >
        <slot name="body" />
      </div>
      <div
        v-if="$slots.footer"
        class="timeline-footer"
      >
        <slot name="footer" />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'LTimelineItem',
  filters: {
    timeFilter (value) {
      const splitting = value.split(' ')
      return splitting.length === 2
        ? splitting[1].includes('00:00:00') ? splitting[0] : value
        : value
    }
  },
  props: {
    icon: {
      type: String,
      default: 'fad fa-pencil-alt'
    },
    iconBg: {
      type: String,
      default: 'bg-blue'
    },
    time: {
      type: String,
      default: ''
    },
    noBorderHeader: {
      type: Boolean,
      default: false
    }
  }
}
</script>

<style lang="scss" scoped>
.timeline {
  &-body {
    white-space: pre-line;
  }
}
</style>
