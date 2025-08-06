
<template>
    <select
      :value="modelValue"
      class="group-select border w-full custom-select mb-2"
      @change="onChange($event)"
      :disabled="isLoading"
      :selectedName="selectedName"
    >
      <option v-for="item in items" :key="item.id" :value="item.id" :name="item.name">
        {{ item.id }} - {{ item.name }}
      </option>
    </select>
    <span v-if="isLoading" class="loading-spinner ms-1 mt-2"></span>
</template>

<script>
export default {
  name: 'GroupList',
  
  props: {
    modelValue: [String, Number],
  },

  emits: ['update:modelValue', 'change'],
  
  methods: {
    onChange(event) {
      this.$emit('update:modelValue', event.target.value);
      this.$emit('change', event.target.value);
      const selectedIndex = event.target.selectedIndex;
      this.selectedName = event.target.options[selectedIndex].getAttribute('name');
    },
    getSelectedName() {
      return this.selectedName;
    }
  },

  data() {
    return {
      isLoading: false,
      items: [],
      selectedName: '',
    };
  },

  async mounted() {
    this.isLoading = true;
    try {
      const response = await axios.get(`/api/clientbase/group/list`);
      this.items = await response.data;
    } catch (error) {
      console.error('Error fetching data:', error);
    } finally {
      this.isLoading = false;
    }
  },
};
</script>

<style>

.loading-spinner {
  position: absolute;
  right: -40px;
  top: -5%;
  transform: translateY(-80%);
  width: 20px;
  height: 20px;
  border: 2px solid rgba(0, 0, 0, 0.2);
  border-top: 2px solid #cfa13f;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
