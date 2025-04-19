<template>
  <client-only>
    <file-pond
      ref="filepond"
      :max-files="maxFiles"
      :allow-multiple="true"
      :instant-upload="false"
      :disabled="!allowRemove"
      :allow-remove="allowRemove"
      :accepted-file-types="acceptedTypes"
      :allow-image-preview="isImagePreview"
      :label-file-loading="lableFileLoading"
      :label-idle="!allowRemove ? '' : idleLable"
      :image-preview-min-height="imagePreviewMinHeight"
      :image-preview-max-height="imagePreviewMaxHeight"
      :image-preview-height="imagePreviewHeight"
      @init="initFilePond"
      @input="changeFiles"
    />
  </client-only>
</template>

<script>
import vueFilePond from 'vue-filepond'
import FilePondPluginImagePreview from 'filepond-plugin-image-preview'
import 'filepond/dist/filepond.min.css'
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css'

const FilePond = vueFilePond(FilePondPluginImagePreview)

export default {
  name: 'LDropzone',
  components: {
    FilePond
  },
  props: {
    value: {
      type: Array,
      default: () => [],
    },
    idleLable: {
      type: String,
      default: 'Поместите файлы в зону или нажмите на нее...'
    },
    maxFiles: {
      type: Number,
      default: 5
    },
    lableFileLoading: {
      type: String,
      default: 'Загрузка'
    },
    acceptFileTypes: {
      type: [String, Array],
      default: 'image/jpeg, image/png'
    },
    isImagePreview: {
      type: Boolean,
      default: false
    },
    imagePreviewHeight: {
      type: Number,
      default: 200
    },
    imagePreviewMinHeight: {
      type: Number,
      default: 256
    },
    imagePreviewMaxHeight: {
      type: Number,
      default: 256
    },
    allowRemove: {
      type: Boolean,
      default: true
    }
  },
  data() {
    return {
      files: this.value,
      id: ''
    }
  },
  computed: {
    acceptedTypes() {
      return Array.isArray(this.acceptFileTypes) ? this.acceptFileTypes.join(', ') : this.acceptFileTypes
    }
  },
  mounted() {},
  updated() {},
  unmounted() {},
  methods: {
    initFilePond() {
      this.$refs.filepond.addFiles(this.value)
    },
    changeFiles(e) {
      this.files = e
      this.$emit('update:modelValue', this.files)  // Используйте update:modelValue вместо change
    }
  }
}
</script>

<style lang="scss">
.filepond--action-revert-item-processing,.filepond--action-retry-item-processing,.filepond--action-abort-item-processing,
.filepond--action-process-item{
  display: none!important;
}

.filepond--file-info,.filepond--file-status{
  //transform: none!important;
}

@media screen and (max-width: 768px)
{
  .modal-dialog
  {
    width:calc(100% - 10%);
  }
}

[data-filepond-item-state*='error'] .filepond--item-panel,
[data-filepond-item-state*='invalid'] .filepond--item-panel {
  background-color: red;
}

[data-filepond-item-state='processing-complete'] .filepond--item-panel {
  background-color: green;
}

.filepond--root .filepond--list-scroller {
  margin-top: 1em;
  margin-bottom: 1em;
}

.filepond {
  &--credits {
    display: none!important;
  }
  &--hopper {
    margin: 0;
  }
  &--root {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto,
    Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji',
    'Segoe UI Symbol';
    font-size: 15px!important;
  }
  &--item {
    width: calc(33% - .5em);
    @media screen and (max-width: 768px) {
      width: calc(50% - .5em);
    }
    &-panel {
      border-radius: 0.5em;
      background-color: #555;
    }
  }
  &--panel {
    &-root {
      border-radius: 0.5em;
      background-color: #eee;
    }
    &-bottom {
      padding: 10px!important;
    }
  }
  &--warning {
    position: absolute;
    bottom: 100%;
    left: 50%;
    background: #f7bdbd;
    color: #000;
    border-radius: 999em;
    font-size: .875em;
    margin-top: 1em;
    display: inline-block;
    padding: .25em 1em;
    opacity: 0;
    white-space: nowrap;
    transform: translateY(1em) translateX(-50%);
    transition: opacity .15s ease-out,transform .5s ease-out;
    &[data-state=visible] {
      opacity:1;
      transform:translateY(0) translateX(-50%);
    }
  }
  &--file {
    color: white;
    &-action-button {
      color: white;
      cursor: pointer;
      background-color: rgba(0, 0, 0, 0.5);
      &:hover, &:focus {
        box-shadow: 0 0 0 0.125em rgba(255, 255, 255, 0.9);
      }
    }
  }
  &--drop {
    &-label {
      color: #555;
    }
  }
  &--label {
    &-action {
      text-decoration-color: #aaa;
    }
  }
  &--drip {
    &-blob {
      background-color: #999;
    }
  }
}
</style>
