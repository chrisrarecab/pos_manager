
<template>
	<div class="custom-text-tooltip-container position-relative">
		<p class="d-flex align-items-center mb-0"
			v-if="text"
			@mouseenter="visible = true"
			@mouseleave="visible = false"
		>
			<img :src="icon" :height="iconHeight" :width="iconWidth" :alt="altText" :class="iconClass" />
		</p>
		<div v-show="visible" class="custom-text-tooltip" :style="tooltipStyle">
			<div class="custom-text-tooltip-content">
					<label class="custom-text-tooltip-label" v-html="text"></label>
			</div>
		</div>
	</div>
</template>

<script setup>
import { ref } from 'vue';

defineProps({
	icon: String,
	text: String,
	altText: { type: String, default: 'Tooltip' },
	iconClass: { type: String, default: '' },
	iconHeight: { type: [String, Number], default: 14 },
	iconWidth: { type: [String, Number], default: 14 },
	tooltipStyle: { type: Object, default: () => ({}) }
});

const visible = ref(false);
</script>

