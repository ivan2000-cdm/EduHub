export default {
  props: {
    ajax: {
      type: String,
      default: ''
    }
  },
  data: () => ({
    ajaxMeta: {
      loading: false,
      // filteredCount: 0,
      totalCount: 0
    }
  }),
  computed: {
    isAjax () {
      return this.ajax !== ''
    },
    ajaxData () {
      const columns = {}
      this.headers.forEach(({ key, visible, search, searchable }) => {
        columns[key] = { visible, search, searchable }
      })
      const offset = (this.currentPage * this.perOnePage.value) - this.perOnePage.value
      return {
        globalSearch: this.searchText,
        limit: this.perOnePage.value,
        sort: this.sort,
        offset,
        columns
      }
    }
  },
  watch: {
    ajaxData (val) {
      this.$emit('generateAjaxData', val)
    }
  },
  methods: {
    /**
     * @decorate CacheMixin
     * @param item
     * @returns {*}
     */
    generateDataForAjax (item) {
      return item
    },
    async getAjaxData (changeHeader = false, cancelToken = null) {
      this.ajaxMeta.loading = true
      const data = this.generateDataForAjax(this.ajaxData)
      const options = {}
      if (cancelToken) {
        options.cancelToken = cancelToken
      }

      try {
        const { data: items } = await this.$axios.post(this.ajax, data, options)
        const { metadata } = items
        if (changeHeader) {
          this.getAjaxHeaders(items)
        }
        this.ajaxMeta.totalCount = metadata.filteredCount
        // this.ajaxMeta.filteredCount = metadata.filteredCount
        this.sort = metadata.sort
        this.localData = items.data
      } catch (e) {
        console.log(JSON.stringify(e))
        this.error = e
      } finally {
        this.ajaxMeta.loading = false
      }
      return Promise.resolve()
    }
  }
}
