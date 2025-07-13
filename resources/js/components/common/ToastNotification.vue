<template>
  <transition name="toast-fade">
    <div v-if="visible" class="toast-notification" :class="typeClass">
      <div class="toast-content">
        <span class="toast-icon" v-if="icon">{{ icon }}</span>
        <div class="toast-message">{{ message }}</div>
      </div>
      <button v-if="dismissable" class="toast-close" @click="close">×</button>
    </div>
  </transition>
</template>

<script setup>
	import { ref, computed, onMounted, onBeforeUnmount } from 'vue'

	const props = defineProps({
	message: {
		type: String,
		required: true
	},
	type: {
		type: String,
		default: 'info',
		validator: (value) => ['info', 'success', 'warning', 'error'].includes(value.toLowerCase())
	},
	duration: {
		type: Number,
		default: 3000
	},
	dismissable: {
		type: Boolean,
		default: true
	}
	})

	const emit = defineEmits(['close'])

	const visible = ref(true)
	let timer = null

	const normalizedType = computed(() => props.type.toLowerCase())

	const typeClass = computed(() => `toast-${normalizedType.value}`)

	const icon = computed(() => {
	const icons = {
		info: 'ℹ️',
		success: '✅',
		warning: '⚠️',
		error: '❌'
	}
	return icons[normalizedType.value]
	})

	function show() {
		visible.value = true
		if (props.duration > 0) {
			timer = setTimeout(close, props.duration)
		}
	}

	function close() {
		visible.value = false
		clearTimeout(timer)
		emit('close')
	}

	onMounted(() => {
		show()
	})

	onBeforeUnmount(() => {
		clearTimeout(timer)
	})
</script>


<style scoped>
.toast-notification {
  position: fixed;
  top: 20px;
  right: 20px;
  min-width: 250px;
  max-width: 350px;
  padding: 12px 15px;
  border-radius: 4px;
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.toast-content {
  display: flex;
  align-items: center;
}

.toast-icon {
  margin-right: 10px;
}

.toast-message {
  font-size: 14px;
}

.toast-close {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 18px;
  margin-left: 10px;
  opacity: 0.7;
}

.toast-close:hover {
  opacity: 1;
}

.toast-info {
  background-color: #f0f7ff;
  color: #1976d2;
  border-left: 4px solid #1976d2;
}

.toast-success {
  background-color: #f0fff4;
  color: #2e7d32;
  border-left: 4px solid #2e7d32;
}

.toast-warning {
  background-color: #fffbf0;
  color: #ff9800;
  border-left: 4px solid #ff9800;
}

.toast-error {
  background-color: #fff0f0;
  color: #d32f2f;
  border-left: 4px solid #d32f2f;
}

.toast-fade-enter-active, .toast-fade-leave-active {
  transition: opacity 0.3s, transform 0.3s;
}

.toast-fade-enter, .toast-fade-leave-to {
  opacity: 0;
  transform: translateY(30px);
}
</style>
