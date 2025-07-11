<template>
   <div class="multi-step-form">
      <!-- Progress Tracker -->
      <div class="progress-container">
			<div class="progress-tracker">
				<div v-for="(label, index) in stepLabels" class="step-container" :key="index" :class="{'active': step === index + 1, 'completed': step > index + 1}">
					<div class="step-circle" :aria-current="step === index + 1 ? 'step' : null" :aria-label="`Step ${index + 1}: ${label}`" >
						{{ index + 1 }}
					</div>
					<div class="step-label" :class="{'active-label': step === index + 1}">
						{{ label }}
					</div>
				</div>
			</div>
			<div class="progress-bar" :style="progressBarStyle">
				<div class="progress" :style="progressStyle"></div>
			</div>
      </div>
	  
      <!-- Step Content -->
      <div class="step-content">
        	<slot :name="'step' + step"></slot>
      </div>

	  <hr class="mt-5">

      <!-- Navigation buttons -->
	  <div class="step-controls">
		<button @click="prevStep" :disabled="isFirstStep" class="p-btn p-btn-prev">
			<i class="fas fa-arrow-left"></i> Previous
		</button>
		
		<button @click="nextStep" :disabled="isLastStep" class="p-btn p-btn-next"> 
			{{ isLastStep ? 'Finish' : 'Next' }}
			<i :class="isLastStep ? 'fas fa-check' : 'fas fa-arrow-right'"></i>
		</button>
	</div>
   </div>
</template>
  
<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
	stepLabels: {
		type: Array,
		required: true,
		validator: (value) => value.length > 0 && value.length <= 5,
	},
	progressBarOffset: {
		type: String,
		default: '15%',
	},
});

const emit = defineEmits(['finished', 'stepBack', 'reset']);
const step = ref(1);
const nextStep = () => {
	if (step.value < props.stepLabels.length) {
		step.value++;

		if (step.value === props.stepLabels.length) {
			emit('finished');
		}
	}
};

const prevStep = () => {
	if (step.value > 1) {
		step.value--;
		emit('stepBack');
	}
};

const resetSteps = () => {
	step.value = 1;
	emit('reset');
};
defineExpose({ resetSteps });

const progressStyle = computed(() => {
	const totalSteps = props.stepLabels.length;
	if (totalSteps === 1) return { width: '0%' };

	const progressPercentage = ((step.value - 1) / (totalSteps - 1)) * 100;
	return {
		width: `${progressPercentage}%`,
	};
});

const progressBarStyle = computed(() => {
	return {
		left: props.progressBarOffset,
		right: props.progressBarOffset,
	};
});

const isFirstStep = computed(() => step.value === 1);
const isLastStep = computed(() => step.value === props.stepLabels.length);
</script>

  
<style scoped>
.multi-step-form {
	max-width: 1500px;
	margin: 0 auto;
	padding: 2rem;
	font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.progress-container {
	position: relative;
	margin-bottom: 2.5rem;
}

.progress-tracker {
	display: flex;
	justify-content: space-between;
	position: relative;
	z-index: 1;
}

.step-container {
	display: flex;
	flex-direction: column;
	align-items: center;
	flex: 1;
	position: relative;
	z-index: 2;
}

.step-circle {
	width: 2.5rem;
	height: 2.5rem;
	display: flex;
	align-items: center;
	justify-content: center;
	border-radius: 50%;
	background-color: #e9ecef;
	color: #6c757d;
	font-weight: bold;
	transition: all 0.3s ease;
	border: 3px solid #e9ecef;
	box-sizing: border-box;
}

.step-container.active .step-circle {
	background-color: white;
	color: var( --progress-tracker-current);
	border-color: var( --progress-tracker-current);
	transform: scale(1.1);
}

.step-container.completed .step-circle {
	background-color: var( --progress-tracker-complete);
	color: white;
	border-color: var( --progress-tracker-complete);
}

.step-label {
	margin-top: 0.5rem;
	text-align: center;
	color: #6c757d;
	font-size: 0.9rem;
	transition: all 0.3s ease;
}

.step-container.active .step-label {
	color: var( --progress-tracker-current);
	font-weight: bold;
}

.step-container.completed .step-label {
	color: var( --progress-tracker-complete);
}

.progress-bar {
	position: absolute;
	top: 1.25rem;
	height: 4px;
	background-color: #e9ecef;
	z-index: 0;
}

.progress {
	position: absolute;
	height: 100%;
	background-color: var( --progress-tracker-complete);
	transition: width 0.3s ease;
	left: 0;
}

.step-content {
	margin: 2rem 0;
	min-height: 150px;
}

.step-controls {
	display: flex;
	justify-content: space-between;
	margin-top: 2rem;
}

.p-btn {
	padding: 0.5rem 1.5rem;
	border-radius: 0.25rem;
	font-weight: 500;
	cursor: pointer;
	transition: all 0.2s ease;
	border: none;
}

.p-btn-next:hover:not(:disabled) {
	background-color: var(--success);
	color:  var(--text-light);
}

.p-btn-next:hover.disabled {
	background-color: transparent !important;
}

.p-btn-next:disabled {
	opacity: 0.65;
	cursor: not-allowed;
}

.p-btn-prev {
	color:  var(--text-muted);
}

.p-btn-prev:hover:not(:disabled) {
	background-color: var(--text-muted-1);
	color:  var(--text-dark);
}

.p-btn-prev.disabled {
	opacity: 0.65;
	cursor: not-allowed;
}
</style>