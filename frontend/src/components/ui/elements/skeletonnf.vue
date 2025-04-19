<template>
  <div
    class="lskeleton"
    :style="{
      width,
      height
    }"
  />
</template>

<script>
export default {
  name: 'LSkeletonnf',
  props: {
    width: {
      type: String,
      default: '100%'
    },
    height: {
      type: String,
      default: '20px'
    }
  }
}
</script>

<style lang="scss" scoped>
@keyframes loading {
  0% {
    background-position: -1000px 0;
  }
  100% {
    background-position: 1000px 0;
  }
}

.lskeleton {
  background: linear-gradient(to right, #eeeeee 8%, #dfdfdf 40%, #eeeeee 33%);
  animation: loading 20s linear infinite;
}
</style>
