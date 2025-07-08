
<template>
 <div class="col-4 d-flex flex-column select-div">
  <div class="d-flex align-items-start">
    <select
      :value="modelValue"
      class="terminal-select mb-2"
      @change="onChange($event)"
      :disabled="isLoading || hasUnsaved"
    >
      <option v-for="option in options" :key="option.value" :value="option.value">
        {{ option.text }}
      </option>
    </select>
    <span v-if="isLoading" class="loading-spinner ms-1 mt-2"></span>
  </div>

  <p v-if="hasUnsaved" class="text-danger small-label mb-0">
    <em>*Please save or discard changes before switching terminals.</em>
  </p>
</div>

  
</template>

<script>
export default {
  name: 'TerminalSelector',
  
  props: {
    modelValue: [String, Number],
    options: {
      type: Array,
      default: () => []
    },
    isLoading: {
      type: Boolean,
      default: false
    },
    hasUnsaved: {
      type: Boolean,
      default: false
    },
  },
  
  emits: ['update:modelValue', 'change'],
  
  methods: {
    onChange(event) {
      this.$emit('update:modelValue', event.target.value);
      this.$emit('change', event.target.value);
    }
  }
};
</script>

<style>
.select-div {
  padding: 0px;
  position: relative;
  z-index: 10;
}

.terminal-select {
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  cursor: pointer;
  display: block;
  width: 100%;
  max-width: 500px; 
  background-color: white;
  color: black;
  font-size: 0.89rem;
  font-weight: 400;
  line-height: 1.5;
  padding: 4px 30px 4px 8px; 
  border-radius: 5px;
  border: 1px solid #d5d5d5;
  height: 30px;

  background-image: url("../../../images/arrow-down.svg");
  background-repeat: no-repeat;
  background-position: right 10px center;
  background-size: 16px;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.terminal-select:focus {
  border-bottom: 1px solid #b88c03 !important;
  background-color: white;
  color: black;
  outline: none;
}

.terminal-select:disabled {
  background-color: #f2f2f2;
  color: #999;
  cursor: not-allowed;
  background-image: none; 
}

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
