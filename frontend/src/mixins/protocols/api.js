const mixin = {
  methods: {
    async updateComment (id, value) {
      try {
        await this.$axios.post(`/api/protocols-application/${id}/update-comment`, { comment: value })
        this.$toastr.success('Примечание обновлен')
      } catch (e) {
        console.error(e)
        this.$toastr.error('Что-то пошло не так')
      }
    },
    async updateRoute (id, value) {
      try {
        if (value.length > 0) {
          await this.$axios.post(`/api/protocols-application/${id}/update-route`, { route: value })
          this.$toastr.success('Маршрут обновлен')
        } else {
          this.$toastr.warning('Маршрут должен быть заполнен')
        }
      } catch (e) {
        this.$toastr.error('Что-то пошло не так')
        console.error(e)
      }
    }
  }
}

export default mixin
