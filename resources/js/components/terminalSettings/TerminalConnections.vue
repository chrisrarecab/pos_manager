<template>
	<div class="container component-body text-start">
		<div class="row custom-gutter">
			<div v-for="(setting, index) in settings" :key="setting.id" class="mb-3">
                <div class="d-flex align-items-center">
                    <label class="input-label h5">
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
                                '--text-tooltip-after-left': '3%',
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
                        :icon-height="13"
                        :icon-width="13"
                        :tooltip-style="{
                            '--tooltip-bg': '#000',
                            '--tooltip-text-color': '#fff',
                             '--text-tooltip-left': '100%',
                            '--text-tooltip-after-left': '3%',
                            '--text-tooltip-translateX': '-10%'
                        }"
                        /> 

                    <!-- Issues Tooltip -->
                    <Tooltip
                        v-if="setting.issues.length > 0"
                        :icon="icons.issue"
                        :text="setting.issues.map(issue => issue.value).join(', ')"
                        :icon-height="17"
                        :icon-width="17"
                        :tooltip-style="{
                            '--tooltip-bg': '#ffbf00',
                            '--tooltip-text-color': '#000',
                            '--text-tooltip-after-left': '3%',
                            '--text-tooltip-translateX': '-5%'
                        }"
                        />
    
                </div>

			<!-- Toast Notification -->
			<ToastNotification
				v-if="showToast"
				ref="toastRef"
				:title="toastTitle"
				:message="toastMessage"
				:show="showToast"
				:type="toastType"
				@close="showToast = false"
			/>
			</div>
		</div>

	<Table
		:items="terminalList" 
		:fields="tableColumns" 
		:currKey="currKey"
		:rowKey="'terminalno'"
		:perPage="10"
		:draft="draft"
		@updateDraft="(key, val) => draft[key] = val"
		@edit="startEditing"
		@cancel="cancelEdit"
		@save="saveData"
	/>
	</div>
</template>

<script setup>
import { computed, ref, watchEffect } from 'vue';
import { format } from 'date-fns';
import Table from '../common/Table.vue';
import Tooltip from '../common/TextTooltip.vue';
import ToastNotification from '../common/ToastNotification.vue';
import { updateTerminalSettings } from '../terminalSettings/TerminalSettingsApi.vue';
import { useToast } from '@/composables/common'
import { checkIfAdmin } from '@/composables/common.js'

const { isAdmin } = checkIfAdmin();

const props = defineProps({
	icons: Object,
	tooltipVisibility: Object,
	settings: Object,
	
});
const { showToast, toastMessage, toastTitle, toastType, show } = useToast()

const formatDate = (date) => {
	return format(new Date(date), 'yyyy-MM-dd');
};

const terminalList = ref([]);

watchEffect(() => {
	const raw = props.settings?.[0]?.value;
	if (typeof raw === 'string') {
		try {
			const parsed = JSON.parse(raw);
			if (Array.isArray(parsed)) {
				terminalList.value = parsed
			}
		} catch (e) {
			console.error('Failed to parse terminal JSON:', e);
		}
	}
})

const tableColumns = computed(() => {
	const first = terminalList.value[0];
	if (!first) return [];

	const columns = Object.keys(first).map(key => {
		const label = key
			.replace(/_/g, ' ')
			.replace(/([a-z])([A-Z])/g, '$1 $2')
			.replace(/\b\w/g, l => l.toUpperCase());

		const column = { 
			key, 
			label,
			type: getFieldType(key, first[key]),
			editable: key !== 'terminalno'
		}

		if (['terminalno'].includes(key)) {
			column.thStyle = 'white-space: nowrap; width: 1%;';
			column.thClass = 'text-nowrap';
			column.tdClass = 'text-nowrap disabled';
		}

		const wideFields = ['businessname', 'address', 'acc', 'sn', 'permitno', 'min'];
		const mediumFields = ['owner', 'tin'];

		if (mediumFields.includes(key)) {
			column.thStyle = 'min-width: 150px;';
		}

		if (wideFields.includes(key)) {
			column.thStyle = 'min-width: 250px;';
		}

		if (key.toLowerCase().includes('date')) {
			column.formatter = (value) => value ? formatDate(value) : '—'
		}

		return column;
	});

	if (isAdmin.value == true) {
		columns.push({
			key: 'actions',
			label: '',
			thStyle: 'text-align: center; width: 1%;',
			tdClass: 'text-center',
			thClass: 'text-nowrap'
		});
	}
	return columns;
});


const getFieldType = (key, value) => {
	if (key.toLowerCase().includes('date')) return 'date'
	if (key === 'isvat') return 'boolean'
	if (typeof value === 'boolean') return 'boolean'
	
	return 'text'
}

const currKey = ref(null);
const draft = ref({});

const startEditing = (item) => {
	currKey.value = item.terminalno;

	const normalized = {};
	for (const key in item) {
		if (getFieldType(key, item[key]) === 'boolean') {
			normalized[key] = item[key] == 1 ? 1 : 0;
		} else {
			normalized[key] = item[key];
		}
	}
	draft.value = normalized;
};

const saveData = async () => {
  try {
    const index = terminalList.value.findIndex(t => t.terminalno === draft.value.terminalno);
    if (index !== -1) {
      terminalList.value[index] = { ...draft.value };
    }

    const payload = {
		func: 'save-terminal-table',
		cirms_terminal_id: props.settings[0].terminal_setting.cirms_terminal_id,
		core_terminal_id: props.settings[0].terminal_setting.core_terminal_id,
		settings: [
			{
				setting_id: props.settings[0].terminal_setting.setting_id,
				value: JSON.stringify(terminalList.value) 
			}
		]
    };

    const response = await updateTerminalSettings(payload);
	if (response.isSuccessful == true) {
		show('Settings saved successfully', 'success')
	}
    currKey.value = null;
    draft.value = {};

  } catch (error) {
    console.error('Save failed:', error);
  }
};


const cancelEdit = () => {
  currKey.value = null
  draft.value = {}
}

</script>
<style>
.custom-table table thead th {
	color: var(--text-muted);
	font-weight: 600;	
	background-color: #f8f9fa; 
	text-align: center;         
	border-bottom: none !important;
	box-shadow: none !important;
	padding: 0.75rem 1rem;    
	font-size: .9rem;
}
.custom-table table td {
	font-size: .85rem;
	color: var(--text-dark);
}
td {
	vertical-align: middle;
	text-align: center;
	color: var(--text-dark);
}
</style>