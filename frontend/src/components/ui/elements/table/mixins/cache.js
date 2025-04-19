import { SET_TABLE_STATE } from '@/assets/data/storeTypes'

export default {
  props: {
    cacheable: {
      type: Boolean,
      default: () => true
    },
    cacheHash: {
      type: [String, null],
      default: () => ''
    }
  },

  computed: {
    hash () {
      return `${this.$router.history.current.fullPath}_${this.cacheHash}`
    },
    existCache () {
      return !!this.$store.state.tableState && this.$store.state.tableState[this.hash]
    }
  },

  mounted () {
    if (this.cacheable) {
      if (typeof this.changeSort === 'function') {
        this.changeSort = this.decorateCacheSort(this.changeSort)
      }
      if (typeof this.handleGlobalSearch === 'function') {
        this.handleGlobalSearch = this.decorateHandleGlobalSearch(this.handleGlobalSearch)
      }
      if (typeof this.getSearchableColumns === 'function') {
        this.getSearchableColumns = this.decorateGetSearchableColumns(this.getSearchableColumns)
      }
      if (typeof this.changePage === 'function') {
        this.changePage = this.decorateChangePage(this.changePage)
      }
      if (typeof this.generateDataForAjax === 'function') {
        this.generateDataForAjax = this.decorateGenerateDataForAjax(this.generateDataForAjax)
      }
      if (typeof this.transformToHeaders === 'function') {
        this.transformToHeaders = this.decorateTransformToHeaders(this.transformToHeaders)
      }
      if (typeof this.transformToAjaxHeaders === 'function') {
        this.transformToAjaxHeaders = this.decorateTransformToHeaders(this.transformToAjaxHeaders)
      }
    }
    this.getAllFromCache()
  },

  methods: {
    getAllFromCache () {
      const sort = this.getFromCache('sort')
      const globalSearch = this.getFromCache('globalSearch')
      const numPage = this.getFromCache('numPage')
      if (sort) {
        this.sort = sort
      }
      if (globalSearch) {
        this.searchText = globalSearch
      }
      if (numPage) {
        this.currentPage = numPage
      }
      // Колонки достаютя из кэша в декораторе decorateTransformToHeaders
    },
    getFromCache (key) {
      return this.existCache
        ? this.$store.state.tableState[this.hash][key]
        : undefined
    },
    decorateCacheSort (func) {
      return (...args) => {
        const sort = func(...args)
        this.setCache({ sort })
        return sort
      }
    },
    decorateHandleGlobalSearch (func) {
      return (globalSearch, ...args) => {
        func(globalSearch, ...args)
        this.setCache({ globalSearch })
      }
    },
    decorateGetSearchableColumns (func) {
      return (...args) => {
        const searchableColumns = func(...args)
        const columns = {}
        searchableColumns
          .filter(column => column.search && column.searchable)
          .forEach(({ key, search }) => {
            columns[key] = { search }
          })
        this.setCache({ columns })
        return searchableColumns
      }
    },
    decorateChangePage (func) {
      return (numPage, ...args) => {
        func(numPage, ...args)
        this.setCache({ numPage })
      }
    },
    decorateTransformToHeaders (func) {
      return (...args) => {
        const headers = func(...args)
        const columns = this.getFromCache('columns')
        if (this.cacheable && columns && Object.keys(columns).length > 0) {
          if (!Object.keys(columns).every(key => key.includes('header'))) {
            this.toggleAdvancedSearch(true)
            return headers.map(item => ({
              ...item,
              search: columns[item.key]?.search || null
            }))
          }
        }

        return headers
      }
    },
    decorateGenerateDataForAjax (func) {
      return (...args) => {
        const data = func(...args)
        const columns = this.getFromCache('columns')
        if (this.cacheable && columns && Object.keys(columns).length > 0) {
          if (!columns.every(item => item.key.includes('header'))) {
            data.columns = columns
          }
        }

        return data
      }
    },
    setCache (data) {
      this.$store.commit(SET_TABLE_STATE, { hash: this.hash, data })
    }
  }
}
