<template>
  <div class="lgallery">
    <cool-light-box
      :items="images"
      :index="index"
      @close="index = null"
    />
    <perfect-scrollbar :options="scrollBar">
      <div class="lgallery__wrapper">
        <img
          v-for="(img, idx) in images"
          :key="`img-${idx}`"
          class="lgallery__image"
          :src="getUrl(img)"
          @click="index = idx"
        >
      </div>
    </perfect-scrollbar>
  </div>
</template>

<script>
import CoolLightBox from 'vue-cool-lightbox'
import 'vue-cool-lightbox/dist/vue-cool-lightbox.min.css'

export default {
  name: 'LGallery',
  components: {
    CoolLightBox
  },
  props: {
    images: {
      type: Array,
      default: () => ([])
    }
  },
  data: () => ({
    index: null,
    scrollBar: {
      suppressScrollX: false
    }
  }),
  methods: {
    getUrl (img) {
      return typeof img === 'object' ? img.src : img
    }
  }
}
</script>

<style lang="scss" scoped>
.lgallery {
  &__wrapper {
    display: flex;
    width: 100%;
  }
  &__image {
    width: 256px;
    height: auto;
    &:not(:last-child) {
      margin-right: 10px;
    }
  }
}
</style>
