<template>
  <div class="form-group">
    <label v-if="label" :for="name">{{ label }}</label>
    <div class="input-group">
      <div class="custom-file">
        <input
          :id="name"
          type="file"
          class="custom-file-input"
          :multiple="multiple"
          @change="handleChange"
        >
        <label
          :for="name"
          class="custom-file-label"
        >
          {{ labelFiles || placeholder }}
        </label>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'LInputFile',
  props: {
    name: {
      type: String,
      default: '',
      required: false
    },
    label: {
      type: String,
      default: '',
      required: false
    },
    placeholder: {
      type: String,
      default: 'Выберите файл'
    },
    multiple: {
      type: Boolean,
      default: false
    },
    accept: {
      type: String,
      default: null
    },
    value: {
      type: [File, Array],
      default: () => []
    }
  },
  data: () => ({
    labelFiles: null
  }),
  methods: {
    handleChange (e) {
      const files = e.target.files || e.dataTransfer.files
      this.labelFiles = `Выбрано ${files.length} файлов`
      this.$emit('update:value', files)  // Обновляем значение через 'update:value'
    }
  }
}
</script>

<style lang="scss" scoped>
.custom-file {
  &-input {
    &:hover {
      cursor: pointer;
    }
  }
}
.custom-file-input:lang(ru)~.custom-file-label::after {
  content: "Обзор" !important;
}
</style>


<style lang="scss" scoped>
  .custom-file {
    &-input {
      &:hover {
        cursor: pointer;
      }
    }
  }
  .custom-file-input:lang(ru)~.custom-file-label::after {
    content: "Обзор" !important;
  }
</style>
