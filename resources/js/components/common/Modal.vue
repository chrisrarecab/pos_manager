<template>
	<BModal 
		:model-value="modelValue" 
		:title="title" 
		:title-class="'fw-bold'" 
		:size="size"
		centered 
		no-close-on-esc 
		no-close-on-backdrop
		@update:model-value="emit('update:modelValue', $event)" 
		@close="emit('close')"
	>
		
		<!-- Main content slot -->
		<div class="modal-content-wrapper">
				<slot />
		</div>
	
		<!-- Footer slot -->
		<template #footer>
				<slot name="footer">
				<button class="btn btn-light" @click="emit('close')">Cancel</button>
				<button class="btn btn-primary" @click="emit('confirm')">Proceed</button>
				</slot>
		</template>
	</BModal>
</template>

<script setup>
import { watch } from 'vue'

const props = defineProps({
modelValue: Boolean,
title: {
	type: String,
	default: 'Modal Title'
},
size: {
	type: String,
	default: 'sm'
},
showFooter: {
	type: Boolean,
	default: false
}
})

const emit = defineEmits(['update:modelValue', 'close', 'confirm'])

watch(() => props.modelValue, (val) => {
	if (val) {
		const scrollBarWidth = window.innerWidth - document.documentElement.clientWidth
		document.body.style.overflow = 'hidden'
		document.body.style.paddingRight = `${scrollBarWidth}px`
	} else {
		document.body.style.overflow = ''
		document.body.style.paddingRight = ''
	}
})
</script>

<style>
.modal-content-wrapper {
	max-height: 100vh;
	overflow-y: auto;
}
</style>
