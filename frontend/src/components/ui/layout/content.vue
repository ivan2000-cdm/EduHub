<template>
  <div class="content-wrapper">
    <!-- <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-4">
              <h1 class="content-h1">
                {{ props.title }}
              </h1>
            </div>
            <div class="col-sm-6 ml-auto">
              <div class="breadcrumbs">
                <l-breadcrumbs :data="props.breadcrumbs" />
              </div>
            </div>
          </div>
        </div>
      </section> -->
    <section v-show="!props.loading" class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <slot />
          </div>
        </div>
      </div>
    </section>
    <div v-show="props.loading" class="loading">
      <div class="loading-wrapper">
        <i class="fad fa-spinner" />
        <h5>Загрузка...</h5>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'LContent',
  props: {
    title: {
      type: String,
      default: ''
    },
    breadcrumbs: {
      type: Array,
      default: () => []
    },
    loading: {
      type: Boolean,
      default: false
    }
  },
  methods: {
  }
}
</script>

<style lang="scss" scoped>
.content {
  &-wrapper {
    padding-top: 7px;
    > .content {
      padding: 0;
    }
  }
  &-h1 {
    width: 100%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: normal;
  }
}
.breadcrumbs {
  display: flex;
  align-items: center;
  justify-content: flex-end;
}
.loading {
  width: 100%;
  height: calc(100vh - 120px);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #234969;
  &-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    > i {
      margin-bottom: 1rem;
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
