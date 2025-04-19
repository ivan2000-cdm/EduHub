<template>
  <ul :key="pages.length" class="pagination pagination-xs">
    <li class="page-item">
      <span
        :class="{
          'page-link': true,
          'page-link-disable': previousPage <= 1 || total === 0
        }"
        href="#"
        @click="changePage(1, 'back')"
      >
        <i class="fas fa-angle-double-left" style="font-size: 11px;" />
      </span>
    </li>
    <li class="page-item">
      <span
        :class="{
          'page-link': true,
          'page-link-disable': previousPage <= 1 || total === 0
        }"
        href="#"
        @click="changePage(currentPage - 1, 'back')"
      >
        <i class="fas fa-chevron-left" style="font-size: 11px;" />
      </span>
    </li>
    <li
      v-if="previousPage > 0"
      class="page-item"
    >
      <span
        href="#"
        class="page-link"
        @click="changePage(previousPage, 'default')"
      >
        {{ previousPage }}
      </span>
    </li>
    <li
      class="page-item"
    >
      <span
        class="page-link"
        style="background-color: #007bff26;"
        @click="changePage(currentPage, 'default')"
      >
        {{ currentPage }}
      </span>
    </li>
    <li
      v-if="nextPage <= pages[pages.length-1]"
      class="page-item"
    >
      <span
        class="page-link"
        @click="changePage(nextPage, 'default')"
      >
        {{ nextPage }}
      </span>
    </li>
    <li class="page-item">
      <span
        :class="{
          'page-link': true,
          'page-link-disable': nextPage >= lastPage || total === 0
        }"
        @click="changePage(currentPage + 1, 'next')"
      >
        <i class="fas fa-chevron-right" style="font-size: 11px;" />
      </span>
    </li>
    <li class="page-item">
      <span
        :class="{
          'page-link': true,
          'page-link-disable': nextPage >= lastPage || total == 0
        }"
        @click="changePage(pages[pages.length-1], 'next')"
      >
        <i class="fas fa-angle-double-right" style="font-size: 11px;" />
      </span>
    </li>
  </ul>
</template>

<script>
export default {
  name: 'LPagination',
  props: {
    perPage: {
      type: Number,
      required: true,
      default: 15
    },
    total: {
      type: Number,
      required: true,
      default: 0
    },
    currentPage: {
      type: Number,
      default: 1
    }
  },
  data () {
    return {
      pages: [],
      page: this.currentPage,
      lastTotalPages: 0
    }
  },
  computed: {
    nextPage () {
      return this.currentPage + 1
    },
    previousPage () {
      return this.currentPage - 1
    },
    lastPage () {
      return this.pages.slice(-1)[0]
    }
  },
  watch: {
    total () {
      this.setPages()
    },
    perPage () {
      this.setPages()
    }
  },
  mounted () {
    // this.$emit('changePage', this.currentPage)
    this.setPages()
  },
  methods: {
    changePage (page, type) {
      this.page = page
      if (
        (this.previousPage > 1 && type === 'back') ||
        (this.nextPage < this.lastPage && type === 'next') ||
        type === 'default'
      ) {
        this.$emit('changePage', page)
      }
    },
    setPages () {
      this.lastTotalPages = this.pages.length
      const numberOfPages = Math.ceil(this.total / this.perPage)
      this.pages = []
      for (let index = 1; index <= numberOfPages; index++) {
        this.pages.push(index)
      }
      // if (this.page === 1 || this.total === 0) {
      //   this.$emit('changePage', 1)
      // }
      // if (this.page > this.pages.length) {
      //   console.log('this.pages.length', this.pages.length)
      //   this.$emit('changePage', this.pages.length)
      // }
      // if (this.page > 1 && this.page <= this.pages.length) {
      //   if (this.lastTotalPages !== 0) {
      //     this.$emit('changePage', Math.ceil(this.pages.length * (this.page / this.lastTotalPages)))
      //   }
      // }
    }
  }
}
</script>

<style lang="scss" scoped>
.pagination {
  margin-bottom: 0;
  .page-link {
    padding: .4rem .60rem;
    cursor: pointer;
  }
}

.page-link-disable {
  cursor: not-allowed !important;
  color: var(--gray) !important
}
</style>
