<template>
	<div class="empty-state" :class="{ 'compact': compact }">
		<div v-if="icon" class="empty-icon">{{ icon }}</div>
		<h3 class="empty-title">{{ title }}</h3>
		<p v-if="description" class="empty-description">{{ description }}</p>
			<slot name="actions">
				<button 
					v-if="buttonText" 
					class="empty-action-button" 
					@click="$emit('action-click')"
				>
					{{ buttonText }}
				</button>
			</slot>
		<slot></slot>
	</div>
</template>
  
<script setup>
import { defineProps, defineEmits } from 'vue'

const emit = defineEmits(['action-click'])

const props = defineProps({
	title: {
		type: String,
		required: true
	},
	description: {
		type: String,
		default: ''
	},
	icon: {
		type: String,
		default: ''
	},
	buttonText: {
		type: String,
		default: ''
	},
	compact: {
		type: Boolean,
		default: false
	}
})
</script>
  
<style scoped>
.empty-state {
	display: flex;
	flex-direction: column;
	align-items: center;
	justify-content: center;
	padding: 40px 20px;
	text-align: center;
	background-color: #f9f9f9;
	border-radius: 8px;
	margin: 20px 0;
}

.empty-state.compact {
	padding: 20px;
	margin: 10px 0;
}

.empty-icon {
	font-size: 48px;
	margin-bottom: 16px;
	color: #9e9e9e;
}

.empty-title {
	margin: 0 0 8px;
	font-size: 18px;
	font-weight: 500;
	color: #333;
}

.empty-description {
	margin: 0 0 20px;
	font-size: 14px;
	color: #666;
	max-width: 400px;
}

.empty-action-button {
	padding: 8px 16px;
	background-color: #1976d2;
	color: white;
	border: none;
	border-radius: 4px;
	font-size: 14px;
	cursor: pointer;
	transition: background-color 0.2s;
}

.empty-action-button:hover {
	background-color: #1565c0;
}
</style>

