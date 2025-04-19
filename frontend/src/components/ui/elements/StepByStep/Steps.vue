<template>
  <div class="row align-items-center h-100 justify-content-center">
    <template
      v-for="(step, i) in steps"
    >
      <div
        :key="i"
        :class="[...defaultClasses, 'rounding', ...getClassState(step)]"
        @click="handleClick(step)"
      >
        {{ step.label }}
      </div>
    </template>
  </div>
</template>

<script>
export default {
  name: 'Steps',
  props: {
    value: {
      type: Object,
      default: null
    },
    steps: {
      type: Array,
      required: true,
      validator: (array) => {
        let selected = false
        return array.every((item) => {
          const keys = Object.keys(item)
          const result = keys.includes('label') &&
            keys.includes('orderNum') &&
            keys.includes('selected') && !(item.selected && selected) &&
            keys.includes('successes') &&
            keys.includes('disabled') && !(item.disabled && item.selected)

          selected = item.selected

          return result
        })
      }
    }
  },
  computed: {
    amountSteps () {
      return this.steps.length
    },
    defaultClasses () {
      return ['h-75', 'm-1', 'mb-4', 'flex', 'justify-content-center', 'align-items-center', 'step']
    }
  },
  methods: {
    getClassRounding (i) {
      return i === 0
        ? 'left-rounding'
        : i === this.amountSteps - 1
          ? 'right-rounding'
          : ''
    },
    getClassState (step) {
      switch (true) {
        case step.selected:
          return 'step-active'
        case step.disabled:
          return 'step-disabled'
        case step.successes:
          return 'step-passed'
        default:
          return 'step-default'
      }
    },
    handleClick (step) {
      if (!step.selected && !step.disabled) {
        this.$emit('input', step)
      }
    }
  }
}
</script>

<style scoped>

.step {
  width: 22%;
  cursor: pointer;
  user-select: none;
  margin-bottom: 1.5rem;
}

.step-default {
  background: rgb(210, 210, 210);
}

.step-default:hover {
  background: rgb(170, 170, 170);
}

.step-disabled {
  background: rgb(150, 150, 150);
  cursor: not-allowed;
}

.step-active {
  background: rgb(147, 199, 250);
  cursor: default;
}

.step-passed {
  background: rgb(2, 212, 9);
}

.step-passed:hover {
  background: rgb(4, 176, 9);
}

.rounding {
  border-radius: 55px;
}

.left-rounding {
  border-radius: 15px 0 0 15px
}

.right-rounding {
  border-radius: 0 15px 15px 0
}

</style>
