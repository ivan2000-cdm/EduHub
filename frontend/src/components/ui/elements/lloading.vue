<template>
  <div class="loading" :style="styleLoading">
    <div class="loading__body" :style="styleLoadingBody">
      <i class="fad fa-spinner" :style="styleIcon" />
    </div>
  </div>
</template>

<script>
export default {
  name: 'LLoading',
  props: {
    styleLoading: {
      type: Object,
      default: () => ({})
    },
    styleLoadingBody: {
      type: Object,
      default: () => ({})
    },
    styleIcon: {
      type: Object,
      default: () => ({})
    }
  }
}
</script>

<style lang="scss" scoped>
.loading {
  position: relative;
  width: 100%;
  height: 100%;
  &__body {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    width: 100%;
    height: 100%;
    > * {
      color: #234969;
    }
    > i {
      background-color: white;
      padding: 10px;
      border-radius: 50%;
      display: inline-block;
      font-size: 4em;
      animation: rotate 1.3s infinite;
    }
  }
}

@keyframes rotate {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
</style>
