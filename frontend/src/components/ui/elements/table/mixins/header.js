export default {
  props: {
    needAdvancedSearch: {
      type: Boolean,
      default: true
    }
  },
  data () {
    return {
      dragging: 0,
      advancedSearch: false
    }
  },
  computed: {
    visibleColumns () {
      if (this.headers.length > 0) {
        return this.headers.filter(({ visible }) => visible)
      }
      return []
    },
    searchableColumns () {
      if (this.visibleColumns.length > 0 && !this.visibleColumns.every(item => item.key.includes('header'))) {
        return this.getSearchableColumns(this.visibleColumns)
      }
      return []
    }
  },
  methods: {
    /**
     * @decorate CacheMixin
     * @param columns
     * @returns Array
     */
    getSearchableColumns (columns) {
      return columns.filter(({ searchable }) => searchable)
    },
    addIfSelectedRow () {
      if (this.isSelectedRow) {
        this.headers.unshift({
          key: 'checkbox',
          label: '',
          search: null,
          searchable: false,
          sort: null,
          type: 'checkbox',
          visible: true,
          width: '40px'
        })
      }
    },
    toggleAdvancedSearch (val = null) {
      this.advancedSearch = val !== null ? val : !this.advancedSearch
    },
    getWidth (key, type) {
      return type === 'color' || key === 'id' ? '120px' : undefined
    },
    getAjaxHeaders (data) {
      const { entityStructure } = data
      this.headers = this.transformToAjaxHeaders(entityStructure)
      this.addIfSelectedRow()
    },
    /**
     * @decorate CacheMixin
     * @param entityStructure
     * @returns {{search: string, visible: *, width, label: *, sort: null, type: *, key: *, searchable: boolean}[]}
     */
    transformToAjaxHeaders (entityStructure) {
      return Object.keys(entityStructure)
        .map((objKey) => {
          const { key, label, type, show, width } = entityStructure[objKey]
          return {
            label,
            key,
            type,
            visible: show,
            sort: null,
            width: width ?? this.getWidth(key, type),
            searchable: type !== 'color',
            search: ''
          }
        })
    },
    getHeaders () {
      this.headers = this.transformToHeaders([...this.columns])
      this.addIfSelectedRow()
      return Promise.resolve()
    },
    /**
     * @decorate CacheMixin
     * @param columns
     * @returns []
     */
    transformToHeaders (columns) {
      return columns.map(item => ({
        ...item,
        search: null
      }))
    },
    dragStart (el, event) {
      event.dataTransfer.setData('Text', el)
      event.dataTransfer.dropEffect = 'move'
      this.dragging = el
    },
    dragEnter (event) {
      // console.log('enter', event)
    },
    dragLeave (event) {
      // console.log('leave', event)
    },
    dragEnd (event) {
      // console.log('end', event)
      // console.log(this.columns)
    },
    drop (i, event) {
      this.moveItem(this.dragging, i)
      // console.log('drop')
    },
    moveItem (from, to) {
      this.headers.splice(to, 0, this.columns.splice(from, 1)[0])
    }
  }
}
