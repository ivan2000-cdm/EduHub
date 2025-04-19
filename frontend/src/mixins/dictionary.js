import { SET_TABLEPAGE } from '../assets/data/storeTypes/index'

const mixin = {
  computed: {
    createRoute () {
      const { current } = this.$router.history
      return `${current.fullPath}/create`
    },
    editRoute () {
      const { current } = this.$router.history
      return `${current.fullPath}/edit`
    },
    isPage () {
      const { fullPath } = this.$route
      return this.tablePage.path === fullPath
    }
  },
  methods: {
    rowClick ({ row, page }) {
      const { current } = this.$router.history
      const { id } = row
      this.$store.commit(SET_TABLEPAGE, {
        path: current.fullPath,
        page
      })
      this.$router.push(`${current.fullPath}/${id}`)
    },
    getWidth (key, type) {
      return type === 'color' ||
      key === 'id'
        ? '120px'
        : undefined
    },
    getHeaders (data) {
      if (data !== null && data !== undefined) {
        const { entityStructure } = data
        return Object.keys(entityStructure).map((objKey) => {
          const { key, label, type, show, width, selectFromOptions } = entityStructure[objKey]
          if (show) {
            return {
              label,
              key,
              type,
              visible: show,
              sort: key === 'id' ? 'desc' : null,
              width: width ?? this.getWidth(key, type),
              searchable: type !== 'color',
              selectFromOptions: selectFromOptions ?? false
            }
          }
          return undefined
        }).filter(item => typeof item !== 'undefined')
      }
      return []
    },
    // TODO переписать
    getInputs (data) {
      const { entityStructure } = data
      return Object.keys(entityStructure).map((objKey) => {
        const { key, label, type, show, readonly, required } = entityStructure[objKey]
        let jsType = 'text'
        switch (type.toLowerCase()) {
          case 'int':
          case 'float':
            jsType = 'number'
            break
          case 'string':
            jsType = 'text'
            break
          case 'color':
            jsType = 'color'
            break
          case 'date':
            jsType = 'date'
            break
          case 'collection':
            jsType = 'collection'
            break
          case 'array':
            jsType = 'array'
            break
          case 'entity':
            jsType = 'entity'
            break
          case 'password':
            jsType = 'password'
            break
          case 'boolean':
            jsType = 'boolean'
            break
          default:
            jsType = 'text'
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
        return undefined
      }).filter(item => typeof item === 'object')
    },
    async apiRemove (removeUrl, redirectTo = null) {
      try {
        await this.$axios.post(removeUrl, null)
        this.$toastr.success('Удалено')
        if (redirectTo) {
          this.$router.push(redirectTo)
        } else {
          this.$router.go(-1)
        }
      } catch (e) {
        console.error(e)
        this.$toastr.error(e)
      }
    },
    remove (redirectTo = null) {
      this.removeModal()
        .then((value) => {
          if (value.value) {
            this.apiRemove(this.removeUrl, redirectTo)
          }
        })
    },
    removeModal () {
      return this.$swal.fire({
        icon: 'warning',
        text: 'Вы уверены, что хотите удалить?',
        reverseButtons: true,
        confirmButtonText: 'Удалить',
        cancelButtonText: 'Закрыть предупреждение',
        showCancelButton: true,
        customClass: {
          confirmButton: 'btn btn-danger',
          cancelButton: 'btn btn-warning'
        }
      })
    },
    goBack () {
      if (this.isEdit) {
        this.$swal.fire({
          icon: 'warning',
          text: 'Вы уверены, что хотите отменить Ваши изменения?',
          reverseButtons: true,
          confirmButtonText: 'Отменить изменения',
          cancelButtonText: 'Закрыть предупреждение',
          showCancelButton: true,
          customClass: {
            confirmButton: 'btn btn-danger btn-sm',
            cancelButton: 'btn btn-warning btn-sm'
          }
        })
          .then((value) => {
            if (value.value) {
              this.$router.go(-1)
            }
          })
      } else {
        this.$router.go(-1)
      }
    },
    getFioFromObject (entity) {
      return `${entity.surname} ${entity.name ? entity.name.substr(0, 1) + '.' : ''}${entity.patronymic ? entity.patronymic.substr(0, 1) + '.' : ''}`
    },
    getFormattedJsDate (val) {
      if (val === null) {
        return val
      }
      const date = new Date(val)
      if (isNaN(date.getDate())) {
        return val // Возвращаем исходное значение, если дата некорректна
      }
      const month = (date.getMonth() + 1).toString().padStart(2, '0')
      const day = date.getDate().toString().padStart(2, '0')
      return day + '.' + month + '.' + date.getFullYear()
    },
    getFormattedDate (val) {
      if (val === null) {
        return val
      }
      const date = new Date(val.timestamp * 1000)
      const month = date.getMonth() ? '0' + (date.getMonth() + 1) : date.getMonth() + 1
      const day = date.getDate() > 9 ? date.getDate() : '0' + date.getDate()
      return date.getFullYear() + '-' + month + '-' + day
    },
    calculateRows (text, charactersPerLine = null) {
      // Если передоваемое поле для расчёта пустое
      if (!text) {
        return false
      }
      const screenWidth = window.innerWidth
      const screenHeight = window.innerHeight
      const averageCharacterWidth = 9 // Приблизительная средняя ширина символа
      const lineHeight = 20 // Приблизительная высота строки

      // Используйте charactersPerLine, если он указан, иначе используйте вычисленное значение
      const maxCharsPerLine = charactersPerLine || Math.floor(screenWidth / averageCharacterWidth)
      const maxLines = Math.floor(screenHeight / lineHeight)

      const totalChars = text.length
      const totalLines = Math.ceil(totalChars / maxCharsPerLine)
      const calculatedRows = Math.min(totalLines, maxLines) + text.split('\n').length - 1

      return Math.max(calculatedRows, 4) // Возвращает максимум из расчетного или 4 строки
    }
  }
}

export default mixin
