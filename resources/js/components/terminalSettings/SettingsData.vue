
<template>
  <div class="container d-flex justify-content-center align-items-center component-body">
    <div class="w-75">
      <div class="row custom-gutter">
        <template v-for="(setting, index) in localSettings" :key="setting.id">
            <div class="col-md-6 mb-3">
                <div class="d-flex align-items-center">
                    <label class="input-label">
                        <!-- Last modified tooltip -->
                        <Tooltip
                            v-if="setting.last_modified_by"
                            :icon="icons.lastModified" 
                            :text="'<strong>Last Modified By: </strong>'+  `${setting.user?.full_name || 'Unknown'} <br> 
                            <strong>Last Modified Date: </strong> ${formatDate(setting.last_modified_date)}`"
                            icon-class="tip-icon"
                            :icon-height="15"
                            :icon-width="15"
                            :tooltip-style="{
                                '--tooltip-bg': '#fff',
                                '--tooltip-text-color': '#000',
                                '--text-tooltip-left': '3%',
                                '--text-tooltip-translateX': '-5%'
                            }"
                        />

                        <strong>{{ setting.name }}</strong>
                    </label>
                    <!-- Tip Tooltip -->
                    <Tooltip
                        v-if="setting.tip.length > 0"
                        :icon="icons.question"
                        :text="setting.tip"
                        icon-class="issue-icon"
                        :icon-height="13"
                        :icon-width="13"
                        :tooltip-style="{
                            '--tooltip-bg': '#000',
                            '--tooltip-text-color': '#fff',
                            '--text-tooltip-left': '3%',
                            '--text-tooltip-translateX': '-10%'
                        }"
                        /> 

                    <!-- Issues Tooltip -->
                    <Tooltip
                        v-if="setting.issues.length > 0"
                        :icon="icons.issue"
                        :text="setting.issues.map(issue => issue.value).join(', ')"
                        icon-class="issue-icon"
                        :icon-height="17"
                        :icon-width="17"
                        :tooltip-style="{
                            '--tooltip-bg': '#ffbf00',
                            '--tooltip-text-color': '#000',
                            '--text-tooltip-left': '3%',
                            '--text-tooltip-translateX': '-5%'
                        }"
                        />
                    <!-- <label class="required-text" v-if="setting.is_required == 1"><em>*Required</em></label> -->
                </div>

                <!-- Text Input -->
                <div v-if="setting.form_element === 'text'" class="mb-3 mt-2 ps-1">
                    <input class="custom-input" type="text" v-model="setting.value" disabled>
                </div>

                <!-- Select Dropdown -->
                 <div v-else-if="setting.form_element === 'dropdown'" class="mb-3 mt-2 ps-1">
                    <select class="custom-select" v-model="setting.value" disabled>
                        <option v-for="(option, i) in setting.options" :key="i" :value="option.id">
                            {{ option.name }}
                        </option>
                    </select>
                 </div>

                <!--Multi Select Dropdown -->
                <div  v-else-if="setting.form_element === 'multi_select_dropdown'" class="mb-3 mt-2 ps-1"  >
                    <Multiselect
                        v-model="setting.value"
                        :options="setting.options"
                        :multiple="true"
                        :taggable="true"
                        :close-on-select="false"
                        :clear-on-select="false"
                        :preserve-search="true"
                        :disabled="true"
                        placeholder="Type or select options"
                        label="value"
                        track-by="id"
                        @tag="tag => handleTag(setting, tag)"
                        class="custom-multiselect"
                    />
                 </div>

                <!-- Radio Buttons -->
                <div v-else-if="setting.form_element === 'radio_button'" class="d-flex gap-3 mb-3 mt-2 ps-2">
                   <label  v-for="(option, i) in [...setting.options].sort((a, b) => b.value - a.value)" :key="i" >
                        <input type="radio" :value="option.id" v-model="setting.value" disabled>
                        {{ option.value == 1 ? "True" : "False" }}
                    </label>
                </div>

            </div>
        </template>
      </div>
    </div>
  </div>
</template>
<script setup>
import { reactive, computed, watch } from 'vue';
import { format } from 'date-fns';
import Tooltip from '../common/TextTooltip.vue';
import Multiselect from 'vue-multiselect';

const props = defineProps({
    icons: Object,
    tooltipVisibility: Object,
    settings: Array
});

const formatDate = (date) => {
    return format(new Date(date), 'yyyy-MM-dd hh:mm a');
};


const localSettings = reactive(
  props.settings.map(setting => {
    let value = setting.value;

    if (setting.form_element === 'multi_select_dropdown') {
      const selectedIds = Array.isArray(value) ? value  : typeof value === 'string' ? value.split(',').map(Number) : [];
      value = setting.options.filter(opt => selectedIds.includes(opt.id));
    }

    return reactive({
      ...setting,
      value
    });
  })
);

const handleTag = (setting, newTag) => {
    const newId = Date.now();
    const newOption = { id: newId, value: newTag };

    setting.options.push(newOption);
    setting.value.push(newOption);
};

const emit = defineEmits(['update:settings']);

watch(localSettings, (newSettings) => {
  newSettings.forEach(setting => {
    emit('update-setting', {
      id: setting.id,
      value: setting.form_element === 'multi_select_dropdown'
        ? setting.value.map(v => v.id).join(',')
        : setting.value
    });
  });
}, { deep: true });

</script>


<style scoped>
.custom-gutter {
    --bs-gutter-x: 100px;
}

.col-md-6 {
    padding: 0 25px 0 25px;
}

.tip-icon {
    position: relative;
    top: -1px;
}

.issue-icon {
    position: relative;
    top: 2.5px;
}
</style>

