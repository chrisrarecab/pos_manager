<template>
    <div
        :class="{ 'custom-btn': true, 'custom-btn-disabled': isDisabled }"
        :model-value="modelValue" 
        :style="{ 
            width: props.width, 
            height: props.height, 
            padding: props.padding,
            border: props.border,
            fontSize: props.fontSize,
        }"
        :disabled="props.disabled"
        >
        <img :src="props.sourceImage"></img>
        <label> {{ props.label }}</label>
    </div>
</template>

<script setup>
import { ref } from 'vue'
const props = defineProps({
    modelValue: Boolean,
    sourceImage: {
        type: String,
        default: '',
    },
    label: {
        type: String,
        default: '',
    },
    width: {
        type: String,
        default: '100px',
    },
    height: {
        type: String,
        default: '100px',
    },
    padding: {
        type: String,
        default: '15px',
    },
    border: {
        type: String,
        default: '2px solid black',
    },
    fontSize: {
        type: String,
        default: '11px'
    },
    disabled: {
        type: Boolean,
        default: false,
    },
})
const isDisabled = ref(props.disabled);

</script>
<style>
    .custom-btn {
        border: 3px solid black;
        text-align: center;
        display: inline-block;
        cursor: pointer;
    }
    .custom-btn-disabled {
        pointer-events: none;
        border: 3px solid rgba(100, 100, 100, 0.3) !important;
        background-color: rgba(200, 200, 200, 0.3);
        color: gray;
    }
    .custom-btn:hover {
        background-color: rgba(165, 126, 0, 0.3);
    }
    .custom-btn:hover label{
        text-decoration: underline;
    }
    .custom-btn label {
        font-weight: bold;
        padding-top: 5px;
        cursor: pointer;
        white-space: nowrap;
    }
    .custom-btn img {
        width: 45px;
    }
</style>