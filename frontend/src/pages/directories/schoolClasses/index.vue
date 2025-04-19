<template>
  <l-card>
    <l-table
      :columns="headers"
      :data="array"
      overflow-y="67vh"
      @onRowClick="rowClick"
    >
      <template v-if="isGranted(roles.SCHOOL_MANAGEMENT_SET)" #functions>
        <l-button
          link color="primary"
          icon="far fa-plus"
          style="width: 120px"
          :to="createRoute"
        >
          Добавить
        </l-button>
      </template>
    </l-table>
  </l-card>
</template>

<script>
import roles from '@/assets/auth/Roles' // правильный импорт
import DictionaryMixin from '~/mixins/dictionary'
import LButton from '@/components/ui/elements/button.vue'
import LCard from '@/components/ui/elements/card.vue'
import LTable from '@/components/ui/elements/table/table.vue'

export default {
  name: 'SchoolClassesIndex',
  components: { LCard, LButton, LTable },
  mixins: [DictionaryMixin],
  access: { roles: [...roles.SCHOOL_MANAGEMENT_SET] },
  data() {
    return {
      object: {} // Инициализируем объект для данных
    };
  },
  mounted() {
    this.loadData(); // Загружаем данные при монтировании компонента
  },
  methods: {
    async loadData() {
      try {
        const { data: object } = await this.$axios.get('api/school-classes');
        console.log(object); // Логируем полученные данные для отладки
        this.object = object;
      } catch (error) {
        console.error('Error loading data:', error);
      }
    },
  },
  computed: {
    roles() {
      return roles;
    },
    // Заголовки колонок
    headers() {
      return this.object.entityStructure ? Object.keys(this.object.entityStructure) : [];
    },
    // Данные для таблицы
    array() {
      if (this.object.data && this.object.data.length > 0) {
        return this.object.data.map(item => {
          const formattedItem = {};
          Object.keys(this.object.entityStructure).forEach(key => {
            formattedItem[key] = item[key] !== null ? item[key] : '';
          });
          return formattedItem;
        });
      }
      return [];
    },
  },
}
</script>

<style scoped></style>
