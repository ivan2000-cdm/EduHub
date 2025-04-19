export default {
  props: {
    data: {
      type: Array,
      default: () => ([])
    },
    colorProp: {
      type: Object,
      default: () => ({
        background: null,
        text: null
      })
    }
  },
  data: () => ({
    searchText: '',
    sort: {}
  }),
  computed: {
    localOrAjaxData () {
      return this.loading
        ? this.generatedData.localData
        : !this.isAjax ? this.paginated : this.localData
    },
    sorted () {
      if (!this.isAjax) {
        const sort = array => array.sort((prev, next) => {
          const modifier = this.sort.order === 'desc' ? -1 : 1

          /*
            Для текста делаем регистронезависимую сортировку
            Для дат делаем преобразование в дату
          */
          const newValue = (value) => {
            const dateRegexp = /((0[1-9]|[12]\d|3[01]).(0[1-9]|1[0-2]).[12]\d{3})/
            switch (typeof value) {
              case 'string':
                return typeof value === 'string' && value !== '' && dateRegexp.test(value)
                  ? new Date(`${value.split('.').reverse().join('-')} 00:00:00`)
                  : value.toLowerCase()
              case 'undefined':
                return ''
              default:
                return value
            }
          }
          const newPrev = newValue(prev[this.sort.key])
          const newNext = newValue(next[this.sort.key])

          // Сравнение предыдущего элемента со следующим по ключу
          return newPrev === newNext
            ? 0
            : newPrev < newNext
              ? -1 * modifier
              : 1 * modifier
        })
        return (Object.keys(this.sort).length > 0 ? sort(this.localData) : this.localData)
          .filter((obj) => {
            return this.searchableColumns.filter(({ search }) => search)
              .every(({ key, search }) => {
                return obj[key] !== null && obj[key] && search
                  ? obj[key].toString().toLowerCase().includes(search.toString().toLowerCase())
                  : false
              })
          })
      }
      return []
    },
    paginated () {
      // Элементы на страницу
      if (!this.isAjax) {
        const from = (this.currentPage * this.perOnePage.value) - this.perOnePage.value
        const to = (this.currentPage * this.perOnePage.value)
        return [...(this.searchText !== '' ? this.searching : this.sorted)].slice(from, to)
      }
      return []
    },
    totalCount () {
      // Общее число элементов
      return this.isAjax ? this.ajaxMeta.totalCount : this.sorted.length
    },
    pageRange () {
      let end = this.perOnePage.value * this.currentPage
      end = end > this.totalCount ? this.totalCount : end
      let start = this.currentPage === 1 ? 1 : end + 1 - this.perOnePage.value
      start = this.totalCount === 0 ? 0 : start
      return {
        start,
        end
      }
    },
    searching () {
      if (!this.isAjax && this.searchText !== '') {
        return this.sorted
          .filter(obj => this.searchableColumns
            .some(({ key }) => obj[key] !== null && obj[key]
              ? obj[key].toString().toLowerCase().includes(this.searchText.toLowerCase())
              : false
            ))
      }
      return []
    }
  },
  methods: {
    /**
     * @decorate CacheMixin
     * @param event
     */
    changePage (event) {
      this.$nextTick(() => {
        if (process.client) {
          window.scrollTo(0, 0)
        }
      })
      this.currentPage = event
      if (this.isAjax) {
        this.getAjaxData()
      }
    },
    changePerPage (e) {
      this.perOnePage = e
      if (this.isAjax) {
        this.getAjaxData()
      }
    },
    /**
     * @decorate CacheMixin
     */
    handleGlobalSearch () {
      if (this.isAjax) {
        this.getAjaxData()
      }
    },
    /**
     * @decorate CacheMixin
     * @param sort
     * @param column
     * @returns {{key: string, order: (string)}|{key: string, order: string}}
     */
    changeSort (sort, column) {
      return sort.key === column
        ? { key: column, order: sort.order === 'asc' ? 'desc' : 'asc' }
        : { key: column, order: 'desc' }
    },
    handleChangeSort (column) {
      this.sort = this.changeSort({ ...this.sort }, column)
      if (this.isAjax) {
        this.getAjaxData()
      }
    }
  }
}
