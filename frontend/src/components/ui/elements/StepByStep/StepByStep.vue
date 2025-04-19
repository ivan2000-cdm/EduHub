<template>
  <div>
    <slot name="header">
      <steps
        :value="step"
        :steps="steps"
        @input="changeStep"
      />
    </slot>
    <slot name="body">
      <template v-for="(item) in steps">
        <step
          :key="item.name"
          :name="item.name"
          :label="item.label"
          :value="item.value"
          :validator="item.validator"
          :valid="item.valid"
          :order-num="item.order"
          :selected="item.selected"
          :successes="item.successes"
          :disabled="item.disabled"
          @valid="handleValid(item)"
          @invalid="handleInvalid(item)"
        >
          <slot
            :name="item.name"
            :step="item"
          />
        </step>
      </template>
    </slot>
    <slot name="footer">
      <div class="flex justify-content-end mt-3">
        <div class="flex">
          <l-button
            v-show="isFirstStep"
            color="default"
            class="mr-5"
            @click="cancel"
          >
            Отмена
          </l-button>
          <l-button
            v-show="!isFirstStep"
            color="default"
            class="mr-5"
            @click="prevStep"
          >
            Назад
          </l-button>
        </div>
        <div class="flex">
          <l-button
            v-show="!isLastStep"
            color="primary"
            :disabled="!step.valid"
            @click="nextStep"
          >
            Далее
          </l-button>
          <l-button
            v-show="isLastStep"
            color="success"
            :disabled="!step.valid"
            @click="submit"
          >
            Создать
          </l-button>
        </div>
      </div>
    </slot>
  </div>
</template>

<script>
import Steps from '@/components/ui/elements/StepByStep/Steps'
import Step from '@/components/ui/elements/StepByStep/Step'
export default {
  name: 'StepByStep',
  components: { Step, Steps },
  props: {
    value: {
      type: Array,
      required: true,
      validator: (array) => {
        let selected = false
        return array.every((item) => {
          const keys = Object.keys(item)
          const result = keys.includes('label') &&
            keys.includes('order') &&
            keys.includes('selected') && !(item.selected && selected) &&
            keys.includes('successes') &&
            keys.includes('disabled') && !(item.disabled && item.selected)

          selected = item.selected

          return result
        })
      }
    }
  },
  data () {
    return {
      steps: this.value
    }
  },
  computed: {
    step () {
      return this.steps?.find(item => item.selected) || {}
    },
    isFirstStep () {
      return this.step.order === 1
    },
    isLastStep () {
      return this.step.order === this.amountSteps
    },
    orderNumNext () {
      return this.step.order + 1
    },
    orderNumPrev () {
      return this.step.order - 1
    },
    indexStepCurrent () {
      return this.steps.findIndex(item => item.order === this.step.order)
    },
    indexStepNext () {
      return this.steps.findIndex(item => item.order === this.orderNumNext)
    },
    indexStepPrev () {
      return this.steps.findIndex(item => item.order === this.orderNumPrev)
    },
    amountSteps () {
      return this.steps?.length || 0
    }
  },
  watch: {
    value (val) {
      this.steps = val
    }
  },
  methods: {
    changeStep (step) {
      const index = this.steps.findIndex(item => item.order === step.order)

      this.steps[this.indexStepCurrent].selected = false
      this.steps[index].selected = true

      this.$emit('input', this.steps)
    },
    nextStep () {
      if (this.indexStepNext < this.amountSteps) {
        const step = this.steps[this.indexStepNext]
        const index = this.steps.findIndex(item => item.order === step.order)

        this.steps[this.indexStepCurrent].successes = true
        this.steps[this.indexStepCurrent].selected = false
        this.steps[index].selected = true
        this.steps[index].disabled = false

        this.$emit('input', this.steps)
      } else {
        console.error('Шаг вне диапазона!')
      }
    },
    prevStep () {
      if (this.indexStepPrev >= 0) {
        const step = this.steps[this.indexStepPrev]
        const index = this.steps.findIndex(item => item.order === step.order)

        this.steps[this.indexStepCurrent].selected = false
        this.steps[index].selected = true

        this.$emit('input', this.steps)
      } else {
        console.error('Шаг вне диапазона!')
      }
    },
    submit () {
      this.$emit('submit')
    },
    cancel () {
      this.$emit('cancel')
    },
    handleValid (step) {
      const stepIndex = this.steps.findIndex(item => item.name === step.name)
      this.steps[stepIndex].valid = true

      this.$emit('valid', step.name)
      this.$emit('input', this.steps)
    },
    handleInvalid (step) {
      const stepIndex = this.steps.findIndex(item => item.name === step.name)
      this.steps[stepIndex].valid = false

      this.$emit('invalid', step.name)
      this.$emit('input', this.steps)
    }
  }
}
</script>

<style scoped>

</style>
