<template>
  <div
    :class="{
      'form-generator': true,
      'two-columns': twoColumns
    }"
  >
    dsadsa
  </div>
</template>

<script>
export default {
  name: 'LFormGenerator',
  props: {
    twoColumns: {
      type: Boolean,
      default: false
    },
    entityStructure: {
      type: Object,
      default: () => ({})
    },
    value: {
      type: Object,
      default: () => ({})
    },
    allReadonly: {
      type: Boolean,
      default: false
    }
  },
  data () {
    return {
      object: {},
      inputs: []
    }
  },
  mounted () {
    const entityStructureKeys = Object.keys(this.entityStructure)
    if (entityStructureKeys.length !== Object.keys(this.value).length) {
      entityStructureKeys.forEach((key) => {
        if (this.value[key]) {
          this.object[key] = this.value[key]
        } else {
          const { type, show } = this.entityStructure[key]
          if (show) {
            switch (type) {
              case 'entity':
                this.object[key] = {}
                break
              case 'collection':
                this.object[key] = []
                break
              case 'color':
                this.object[key] = '#ffffff'
                break
              case 'boolean':
                this.object[key] = false
                break
              default:
                this.object[key] = null
                break
            }
          }
        }
      })
      this.getInputs()
      this.$emit('input', this.object)
    }
  },
  methods: {
    getInputs () {
      this.inputs = Object.keys(this.entityStructure).map((objKey) => {
        const { key, label, type, show, readonly, required } = this.entityStructure[objKey]
        let jsType = 'text'
        switch (type.toLowerCase()) {
          case 'int':
          case 'float':
            jsType = 'number'
            break
          case 'string':
            jsType = 'text'
            break
          default:
            jsType = type.toLowerCase()
            break
        }
        if (show && key !== 'id') {
          return {
            label,
            prop: key,
            type: jsType,
            readonly,
            required,
            validity: ''
          }
        }
      }).filter(item => typeof item !== 'undefined')
    }
  }
}
</script>

<style lang="scss" scoped>
.form-generator {
  display: grid;
  grid-template-columns: 1fr;
  &.two-columns {
    grid-template-columns: 1fr 1fr;
    column-gap: 4rem;
  }
}
</style>
