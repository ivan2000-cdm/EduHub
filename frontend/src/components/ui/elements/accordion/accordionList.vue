<template>
  <div class="accordion">
    <slot />
  </div>
</template>

<script>
// todo запилить catcher эвента выбора активной панели аккордиона

export default {
  name: 'LAccordion'
}
</script>

<style scoped>
  .accordion .card {
    margin-bottom : 0;
  }
</style>
