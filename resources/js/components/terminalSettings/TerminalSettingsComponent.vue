<template>
   <div>
		<div v-if="clientsTerminalDetails.length">
			<h1 class="text-center">{{ clientsTerminalDetails[0]?.clientNetworkName || 'N/A' }}</h1>
		</div>
   </div>

	<div class="container mt-5">
		<span class="fs-6"><strong>Terminal:</strong></span>
		<div class="row">
			<TerminalSelector 
			:options="clientTerminalDetailOptions"   
			v-model="selectedTerminal"
			:isLoading="isLoading"
			:hasUnsaved="hasUnsaved"
  			:disabled="isLoading || hasUnsaved"
			/>

			<ToastNotification 
  				v-if="showToast"
				ref="toastRef"
				:title="toastTitle"
				:message="toastMessage"
				:show="showToast"
				:type="toastType"
				@close="showToast = false"
			/>
			<div class="col-4"></div>
			<div class="col-4 d-flex flex-column align-items-end p-bottom">
				<div class="d-flex justify-content-end">
					<b-button ref="saveBtn" :class="['save-button', { 'saved': isSaved }]"  size="sm"  @click="submitTerminalSettings('saveSettings')"> Save Changes 
						<b-badge variant="light">{{ changedCount }}</b-badge></b-button>
					
					<div class="custom-tooltip-container ms-2">
						<p class="h5 mb-0 controls" @mouseenter="showTooltip" @mouseleave="hideTooltip">
							<img :src="controlImg" height="20" width="20" alt="Control Icon">
						</p>
						<div v-show="isTooltipVisible" class="custom-tooltip" @mouseenter="keepTooltipOpen" @mouseleave="hideTooltip">
							<div class="custom-tooltip-content">
								<div class="custom-tooltip-item" v-for="(icon, index) in tooltipIcons" :key="index"
									:style="{ '--icon-size': icon.size + 'px' }" @click="handleIconClick(icon.label)">
									<label class="custom-tooltip-label">{{ icon.label }}</label>
									<img :src="icon.src" alt="Icon">
								</div>
							</div>
						</div>
					</div>
				</div>
				<p class="mt-1 mb-0 text-muted small-label"><em>Last Synced Time: 2025-03-01 09:10:23</em></p>
        	</div>
		</div>
		<div class="d-flex justify-content-between align-items-center mt-5">
			<div>    
				<!-- MODAL FOR COPY SETTINGS -->
				<Modal v-model="showCopyModal" title="Copy Setting" size="lg"
					:hideFooter ="!showFooter"
      				:isLoading="isLoadingCopy"
					@close="closeModal('copy')"
				>
					<MultiStepForm ref="multiStepRefCopy"
						:stepLabels="['Choose source and target terminals', 'Review new POS settings', 'Save new POS settings']"
						:progressBarOffset="'15%'"
						:key="showCopyModal ? 'open' : 'closed'"
						@finished="showFooter = true"
      					@stepBack="showFooter = false"
						@reset="showFooter = false" 
					>
						<template v-slot:step1>
							<div class="d-flex justify-content-center align-items-center mt-5">
								<div class="row">
									<p class="small-label mb-0 ps-1"><strong>Copy From:</strong></p>
									<select v-model="selectedSourceTerminal" class="border w-full custom-select mb-3" 
										@change="fetchModalTabsAndSettings">
										<option v-for="(terminal, index) in clientsTerminalDetails" :key="index" :value="terminal.clientTerminalId">
										B{{ terminal.branchId }}: {{ terminal.branchName }} - Terminal #{{ terminal.terminalNo }}
										</option>
									</select>

									<p class="small-label mb-0 ps-1"><strong>Copy To:</strong></p>
									<select v-model="selectedTargetTerminal" class="border w-full custom-select">
										<option	v-for="(terminal, index) in clientsTerminalDetails.filter(t => t.clientTerminalId !== selectedSourceTerminal)"
											 :key="index" :value="terminal.clientTerminalId">
										B{{ terminal.branchId }}: {{ terminal.branchName }} - Terminal #{{ terminal.terminalNo }}
										</option>
									</select>
								</div>
							</div>
						</template>
						<template v-slot:step2>
							<div class="mt-5">
								<p class="mb-3" v-if="selectedTo">
									<em>These are the updated settings for 
										<strong> Branch {{ selectedTo.branchId }}: {{ selectedTo.branchName }} - Terminal #{{ selectedTo.terminalNo }}</strong>. 
										Please review them carefully.</em>
								</p>
								<div class="accordion" id="settingsAccordion">
									<div v-for="(tab, tabIndex) in modalSettingTabs" :key="tabIndex" >
										<div  class="accordion-item" v-if="modalTabData[tab.id]?.some(setting => setting.type === 'admin_only|shared')">
											<h2 class="accordion-header">
											<button class="accordion-button" type="button" data-bs-toggle="collapse" 
													:data-bs-target="'#collapse' + tabIndex" :aria-expanded="tabIndex === 0 ? 'true' : 'false'"
													:aria-controls="'collapse' + tabIndex">
												{{ tab.name }}
											</button>
											</h2>
											<div :id="'collapse' + tabIndex" class="accordion-collapse collapse" :class="{ 'show': tabIndex === 0 }" data-bs-parent="#settingsAccordion">
											<div class="accordion-body">
												<table class="table table-sm">
													<thead>
														<tr>
															<th>Setting</th>
															<th>Value</th>
														</tr>
													</thead>
													<tbody>
														<tr v-for="(setting, settingIndex) in modalTabData[tab.id]" :key="settingIndex">
														<td>{{ setting.name }}</td>
														<td>
															<span v-if="setting.form_element === 'dropdown' && setting.options">
																{{ setting.options.find(opt => opt.id == setting.value)?.value }}
															</span>
															<span v-else-if="setting.form_element === 'radio_button' && setting.options">
																{{ setting.options.find(opt => opt.id == setting.value)?.value === "1" ? "True" : "False" }}
															</span>
															<span v-else> {{ setting.value || 'Not set' }} </span>
														</td>
														</tr>
													</tbody>
												</table>
											</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</template>
						<template v-slot:step3>
							<div class="d-flex justify-content-center align-items-center mt-5">
								<p class="mt-3 text-center " v-if="selectedTo">
									You're about to apply new settings to 
									<span class="highlight-text">
										 Branch {{ selectedTo.branchId }}: {{ selectedTo.branchName }}- Terminal#{{ selectedTo.terminalNo }}
									</span> , copied from
									 <span class="highlight-text">
										 Branch {{ selectedFrom.branchId }}: {{ selectedFrom.branchName }}- Terminal#{{ selectedFrom.terminalNo }}
									</span>.
									Click <strong>Proceed</strong> to continue..
								</p>

							</div>
						</template>
						<template v-slot:step4></template>
					</MultiStepForm>
					<template #footer>
						<button class="btn btn-light" @click="closeModal('copy')">Cancel</button>
						<button class="btn btn-success" @click="submitTerminalSettings('copySettings')" :disabled="isLoadingCopy">
							<span v-if="isLoadingCopy" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
							{{ isLoadingCopy ? 'Copying...' : 'Proceed' }}
						</button>
					</template>
				</Modal>
				<!-- NODAL FOR APPLY TO ALL SETTINGS -->
				<Modal v-model="showApplyToAllModal" title="Apply-to-All Setting" size="lg"
					:hideFooter="!showFooter"
      				:isLoading="isLoadingCopy"
					@error="console.error('Image failed to load', iconSrc)"
					@close="closeModal('applytoall')"
				>
					<MultiStepForm ref="multiStepRefApplyToAll"
						:stepLabels="['Choose source and target terminals', 'Review new POS settings', 'Save new POS settings']"
						:progressBarOffset="'15%'"
						:key="showApplyToAllModal ? 'open' : 'closed'"
						@finished="showFooter = true"
      					@stepBack="showFooter = false"
						@reset="showFooter = false" 
					>
						<template v-slot:step1>
							<p class="ps-4 mt-5">Copy terminal settings by: </p>
							<div class="d-flex flex-column align-items-center">
								<div class="d-flex justify-content-center flex-wrap mb-4">
									<button v-for="option in whereToCopyOptions" :key="option" @click="selectedOption = option" type="button" class="radioGroup px-4 py-2 rounded-full border"
										:style="selectedOption === option
											? {
												color: '#fff',
												backgroundColor: 'var(--primary)',
												borderColor: 'var(--primary)'
											}
											: {
												color:'var(--text-dark)',
												backgroundColor: 'var(--surface)',
												borderColor: 'var(--surface)'
											}"
									>
									{{ option }}
									</button>
								</div>
								<div v-if="selectedOption === 'Network'" class="w-100 d-flex justify-content-center">
									<div class="w-100" style="max-width: 480px;">
										<p class="small-label mb-1 ps-1"><strong>Copy From:</strong></p>
										<select v-model="selectedSourceTerminal" class="custom-select w-100 mb-3" @change="fetchModalTabsAndSettings">
											<option v-for="(terminal, index) in clientsTerminalDetails" :key="index" :value="terminal.clientTerminalId">
												B{{ terminal.branchId }}: {{ terminal.branchName }} - Terminal #{{ terminal.terminalNo }}
											</option>
										</select>
									</div>
								</div>
								<div v-if="selectedOption === 'Branch'" class="w-100 d-flex justify-content-center">
									<div class="w-100" style="max-width: 480px;">
									<p class="small-label mb-1 ps-1"><strong>Copy From:</strong></p>
									<select v-model="selectedSourceTerminal" class="custom-select w-100 mb-3" @change="fetchModalTabsAndSettings">
										<option v-for="(terminal, index) in clientsTerminalDetails" :key="index" :value="terminal.clientTerminalId">
											B{{ terminal.branchId }}: {{ terminal.branchName }} - Terminal #{{ terminal.terminalNo }}
										</option>
									</select>
									<p class="small-label mb-1 ps-1"><strong>Copy To:</strong></p>
									<select v-model="selectedTargetBranch" class="custom-select w-100 mb-3" @change="fetchModalTabsAndSettings">
										<option disabled value="">Select a branch</option>
										<option v-for="(branch, index) in filteredBranches" :key="index" :value="branch.branchId" >
											B{{ branch.branchId }}: {{ branch.branchName }}
										</option>
									</select>
									</div>
								</div>
							</div>
						</template>
						<template v-slot:step2>
							<div class="mt-5">
								<p class="mt-5 mb-3">
									<em> These are the updated settings for </em>
									<span v-if="selectedOption === 'Branch' && selectedBranchDetails">
										<span class="highlight-text">
											Branch {{ selectedBranchDetails.branchId }} - {{ selectedBranchDetails.branchName }}
										</span>.
										<em> Please review them carefully.</em>
										<ul>
											<li v-for="terminal in terminalsUnderSelectedBranch.filter (t => !(t.branchId === selectedFrom?.branchId && t.terminalNo === selectedFrom?.terminalNo))"
												:key="terminal.clientTerminalId" >
												<em>Terminal# {{ terminal.terminalNo }}</em>
											</li>
										</ul>
									</span>
									<span v-else-if="selectedOption === 'Network' && selectedTo">
										<strong>{{ selectedTo.clientNetworkName }}</strong>
									</span>
								</p>
								<div class="accordion" id="settingsAccordion">
									<div v-for="(tab, tabIndex) in modalSettingTabs" :key="tabIndex" >
										<div  class="accordion-item" v-if="modalTabData[tab.id]?.some(setting => setting.type === 'admin_only|shared')">
											<h2 class="accordion-header">
											<button class="accordion-button" type="button" data-bs-toggle="collapse" 
												:data-bs-target="'#collapse' + tabIndex" :aria-expanded="tabIndex === 0 ? 'true' : 'false'" :aria-controls="'collapse' + tabIndex">
												{{ tab.name }}
											</button>
											</h2>
											<div :id="'collapse' + tabIndex" class="accordion-collapse collapse" :class="{ 'show': tabIndex === 0 }" data-bs-parent="#settingsAccordion">
												<div class="accordion-body">
													<table class="table table-sm">
														<thead>
															<tr>
															<th>Setting</th>
															<th>Value</th>
															</tr>
														</thead>
														<tbody>
															<tr v-for="(setting, settingIndex) in modalTabData[tab.id]" :key="settingIndex">
																<td>{{ setting.name }}</td>
																<td>
																	<span v-if="setting.form_element === 'dropdown' && setting.options">
																		{{ setting.options.find(opt => opt.id == setting.value)?.value }}
																	</span>
																	<span v-else-if="setting.form_element === 'radio_button' && setting.options">
																		{{ setting.options.find(opt => opt.id == setting.value)?.value === "1" ? "True" : "False" }}
																	</span>
																	<span v-else> {{ setting.value || 'Not set' }} </span>
																</td>
															</tr>
														</tbody>
													</table>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</template>
						<template v-slot:step3>
							<div class="d-flex justify-content-center align-items-center mt-5">
								<p class="mt-4 text-center">
									You're about to apply new settings to all terminals of
									<template v-if="selectedOption === 'Branch' && selectedBranchDetails">
										<span class="highlight-text">
											Branch {{ selectedBranchDetails.branchId }} - {{ selectedBranchDetails.branchName }}
										</span>
										
									</template>

									<template v-else-if="selectedOption === 'Network' && selectedTo">
										<strong>{{ selectedTo.clientNetworkName }}</strong>
									</template>

									copied from
									<span class="highlight-text">
										Branch {{ selectedFrom.branchId }}: {{ selectedFrom.branchName }} - Terminal #{{ selectedFrom.terminalNo }}
									</span>.
									Click <strong>Proceed</strong> to continue..
								</p>
							</div>
						</template>
					</MultiStepForm>
					<template #footer>
						<button class="btn btn-light" @click="closeModal('applytoall')">Cancel</button>
						<button class="btn btn-success" @click="submitTerminalSettings('applyToAllSettings')" :disabled="isLoadingCopy"> 
							<span v-if="isLoadingCopy" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
							{{ isLoadingCopy ? 'Copying...' : 'Proceed' }}
						</button>
					</template>
				</Modal>
			</div>
			<div class="col-4 d-flex">
				<form class="mb-2 d-flex w-100">
					<input type="search" class="custom-input flex-grow-1" placeholder="Search..." aria-label="Search" />
					<b-button class=" ms-2 btn btn-warning" size="sm"><img :src="searchIcon" height="20" width="20" alt="Control Icon"></b-button>
				</form>
			</div>
		</div>
	</div>
	<b-card class="d-flex">
      <b-tabs v-model="currentTab" class="custom-tabs">
        <b-tab v-for="tab in settingTabs" :key="tab.id">
			<template #title>
				{{ tab.name }}
				<span v-if="hasUnsavedChanges(tab.id)" class="change-icon"></span>
			</template>

          <EmptyState title="No Data Available" 
		  	v-if="!tabData[tab.id] || tabData[tab.id].length === 0" 
            :tabName="tab.name" 
          />
          
          <SettingsData 
            v-else
			:icons="ICON_PATHS"
            :settings="tabData[tab.id]"
            :tooltipVisibility="tooltipVisibility"
			  v-model:settings="settings"
          />
        </b-tab>
      </b-tabs>
    </b-card>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, computed, watch, reactive, nextTick  } from 'vue';
import { BCard, BTabs, BTab } from "bootstrap-vue-3";
import ToastNotification from '../common/ToastNotification.vue';
import { ICON_PATHS } from '../common/Icons.vue';
import Modal from '../common/Modal.vue';
import EmptyState from '../common/EmptyState.vue';
import MultiStepForm from '../common/MultiStep.vue';
import{ fetchClientDetails, fetchTerminalSettingsTabs, fetchTerminalSettings, 
	updateTerminalSettings, copyTerminalSettings, applySettingsToMultipleTerminals }
	from '../terminalSettings/TerminalSettingsApi.vue';
import TerminalSelector from '../TerminalSettings/TerminalSelector.vue';
import SettingsData from '../terminalSettings/SettingsData.vue';

const props = defineProps({
	detail: {
		type: String,
		required: true
	}
})

const isLoading = ref(false);
const showFooter = ref(false);

const isLoadingCopy = ref(false);
const whereToCopyOptions = (['Network', 'Branch']),
	selectedOption = ref('Network');

const controlImg = ICON_PATHS.control,
	searchIcon = ICON_PATHS.search,
	copyAllIcon = ICON_PATHS.copyAll;

const tooltipIcons = reactive([
	{ src: ICON_PATHS.copy, label: "Copy to..", size: 21 },
	{ src: ICON_PATHS.copyAll, label: "Apply to all", size: 22 },
	{ src: ICON_PATHS.revert, label: "Revert", size: 22 },
	{ src: ICON_PATHS.save, label: "Set as default", size: 22 },
	{ src: ICON_PATHS.resync, label: "Resync", size: 22 }
]);

const showTooltip = () => { isTooltipVisible.value = true; };
const hideTooltip = () => { isTooltipVisible.value = false; };
const keepTooltipOpen = () => { isTooltipVisible.value = true; };

const showToast = ref(false),
	toastMessage = ref(""),
	toastType = ref(""),
	toastTitle = ref("Notification"),
	toastRef = ref(null);
const showBootstrapToast = (message, type, title = "Notification", duration = 3000) => {
	toastMessage.value = message;
	toastType.value = type
	toastTitle.value = title;
	showToast.value = true;

const toastElement = toastRef.value;
if (toastElement) {
	const toastInstance = new Toast(toastElement);
	toastInstance.show();
}

setTimeout(() => {
	showToast.value = false;
	}, duration);
};

const clientBranchDetails = ref([]);
const clientsTerminalDetails = ref([]);
const fetchClientTerminalDetail = async () => {
	try {
		const data = await fetchClientDetails(props.detail);
		clientsTerminalDetails.value = data.map(item => ({
			branchId: item.branchid,
			branchName: item.branchname,
			clientGroupId: item.client_group_id,
			clientNetworkId: item.client_network_id,
			terminalNo: item.client_terminal_no ?? "NA",
			clientTerminalId: item.client_terminal_id ?? null,
			clientNetworkName: item.name
		})).sort((a, b) => a.branchId - b.branchId);

		if (clientsTerminalDetails.value.length > 0) {
			selectedTerminal.value = clientsTerminalDetails.value[0].clientTerminalId;
			selectedSourceTerminal.value = clientsTerminalDetails.value[0].clientTerminalId;
		}
		console.log(clientsTerminalDetails);
		const uniqueBranchTerminals = Object.values(
		clientsTerminalDetails.value
			.filter(item => item.clientTerminalId !== null)
			.reduce((acc, curr) => {
				if (!acc[curr.branchId]) {
					acc[curr.branchId] = {
						branchId: curr.branchId,
						branchName: curr.branchName
					};
				}
				return acc;
			}, {})
		); 

		clientBranchDetails.value = uniqueBranchTerminals;

	} catch (error) {
		console.error("Error fetching client details:", error);
		showBootstrapToast("Failed to load terminals", "Error");
	}
};
onMounted(fetchClientTerminalDetail);
const clientTerminalDetailOptions = computed(() => {
	return clientsTerminalDetails.value.map(terminal => ({
		value: terminal.clientTerminalId,
		text: `Branch ${terminal.branchId}: ${terminal.branchName} - Terminal# ${terminal.terminalNo}`
	}));
});

const tooltipVisibility = reactive({}),
	isTooltipVisible = ref(false),
	selectedTerminal = ref(null),
	settingTabs = ref([]),
	modalSettingTabs = ref([]),
	currentTab = ref(0),
	tabData = reactive({}),
	originalTabData = ref({});
const fetchTabsAndSettings = async () => {
	if (!selectedTerminal.value) return;
	isLoading.value = true;

	try {
		settingTabs.value = await fetchTerminalSettingsTabs();
		const response = await fetchTerminalSettings(selectedTerminal.value);
		const settings = response.data || response;
		Object.keys(tabData).forEach(key => {
			tabData[key] = [];
		});

		const originalValues = {};
		settings.forEach(setting => {
		if (!tabData[setting.setting_tab_id]) {
			tabData[setting.setting_tab_id] = [];
		}
		tooltipVisibility[setting.id] = false;
		let terminalSettingValue = setting.terminal_setting?.value ?? "";
		
		tabData[setting.setting_tab_id].push({
			...setting,
			value: terminalSettingValue,
			options: setting.options || []
		});

		originalValues[setting.id] = terminalSettingValue;
		});

		originalTabData.value = originalValues;
	} catch (error) {
		console.error("Error fetching settings:", error);
		showBootstrapToast("Failed to load settings", "Error");
	} finally {
		isLoading.value = false;
	}
};
watch(selectedTerminal, fetchTabsAndSettings);

const selectedSourceTerminal = ref(null);
const selectedTargetTerminal = ref("");
const modalTabData = ref({});
const fetchModalTabsAndSettings = async () => {
	if (!selectedSourceTerminal.value) {
		console.warn('No modal terminal selected');
		return;
	}
	
	isLoading.value = true;

	try {
		modalSettingTabs.value = await fetchTerminalSettingsTabs();
		const modalSettings = await fetchTerminalSettings(selectedSourceTerminal.value);
		
		modalTabData.value = {};
		
		modalSettings.forEach(setting => {
			if (setting.type !== 'admin_only|shared') return;
			if (!modalTabData.value[setting.setting_tab_id]) {
				modalTabData.value[setting.setting_tab_id] = [];
			}
			
			tooltipVisibility[setting.id] = false;
			let terminalSettingValue = setting.terminal_setting?.value ?? "";
			
			modalTabData.value[setting.setting_tab_id].push({
				...setting,
				value: terminalSettingValue,
				options: setting.options || []
			});
		});
		
	} catch (error) {
		console.error("Error details:", {
			terminal: selectedSourceTerminal.value,
			error: error.message,
			stack: error.stack
		});
		showBootstrapToast("Failed to load settings", "Error");
	} finally {
		isLoading.value = false;
	}
};
// watch(selectedSourceTerminal);

const selectedFrom = computed(() => {
	return clientsTerminalDetails.value.find(
		terminal => terminal.clientTerminalId === selectedSourceTerminal.value
	);
});
const selectedTo = computed(() => {
	return clientsTerminalDetails.value.find(
		terminal => terminal.clientTerminalId === selectedTargetTerminal.value
	);
});	
const selectedBranchDetails = computed(() => {
	return clientBranchDetails.value.find(
		branch => branch.branchId === selectedTargetBranch.value
	) || null;
});
const terminalsUnderSelectedBranch = computed(() => {
	if (!selectedTargetBranch.value) return [];

	return clientsTerminalDetails.value.filter(
		terminal => terminal.branchId === selectedTargetBranch.value
	);
});
const selectedTargetBranch = ref("");
const filteredBranches = computed(() => {
	if (!selectedFrom.value) return clientBranchDetails.value || [];

	return (clientBranchDetails.value || []);
});

watch(filteredBranches, (newBranches) => {
	if (newBranches.length > 0) {
		selectedTargetBranch.value = newBranches[0].branchId;
	} else {
		selectedTargetBranch.value = '';
	}
}, { immediate: true });

const isSaved = ref(false),
	saveBtn = ref(null);

const getSettingsArray = (modalTabData) => {
	const rawSettings = Object.values(modalTabData ?? {}).flat();
	const settingsArray = (rawSettings || []).map(setting => ({
		setting_id: setting.id,
		value: setting.value ?? "",
	}));

	if (settingsArray.length === 0) {
		showBootstrapToast("No settings available to copy.", "Warning");
		return null; 
	}

	return settingsArray;
}

const submitTerminalSettings = async (saveType) => {
	try {
		let payload;
		switch(saveType) {
			case 'saveSettings':
				showToast.value = false;

				const changedSettings = Object.values(tabData).flat().filter(setting => 
					setting.value !== (originalTabData?.value?.[setting.id] ?? null)
				);

				changedCount.value = changedSettings.length;

				if (changedSettings.length === 0) {
					showBootstrapToast("No changes detected.", "Warning");
					blurSaveBtn();
					return;
				}

					payload = {
					client_terminal_id: selectedTerminal.value,
					settings: changedSettings.map(setting => ({
						setting_id: setting.id,
						value: setting.value,
					})),
				};

				await updateTerminalSettings(payload);
				showBootstrapToast("Settings saved successfully", "Success");

				changedSettings.forEach(setting => {
					originalTabData.value[setting.id] = setting.value;
				});
				
				await fetchTabsAndSettings();
				isSaved.value = true;
				blurSaveBtn();
			break;
			case 'copySettings':
				isLoadingCopy.value = true;
				showToast.value = false;

				if (!selectedTargetTerminal.value) {
					showBootstrapToast("Please select a target terminal.", "Warning");
					isLoadingCopy.value = false;	
					return;
				}

				const settingsArray = getSettingsArray(modalTabData.value);
				payload = {
					client_terminal_id: selectedTargetTerminal.value,
					settings: settingsArray
				};

				await copyTerminalSettings(payload);
				showBootstrapToast("Settings copied successfully", "Success");
				
				await fetchTabsAndSettings();
				isLoadingCopy.value = false;	
				closeModal('copy');
			break;
			case 'applyToAllSettings':
				isLoadingCopy.value = true;
				let targetTerminalIds = [];

				if(selectedOption.value == 'Network') {
					targetTerminalIds = clientsTerminalDetails.value
						.filter(item => item.clientTerminalId !== selectedSourceTerminal.value)
						.filter(id => id !== null)
						.map(item => item.clientTerminalId);
					
				}
				else if(selectedOption.value == 'Branch'){
					targetTerminalIds = clientsTerminalDetails.value
						.filter(item => item.branchId === selectedTargetBranch.value)
						.map(item => item.clientTerminalId)
						.filter(id => id !== null); 
				}
				else {
					showBootstrapToast("Please select a target terminal.", "Warning");
					isLoadingCopy.value = false;	
					return;
				};

				const applyToAllSettingsArr = getSettingsArray(modalTabData.value);
				payload = {
					client_terminal_ids: targetTerminalIds,
					settings: applyToAllSettingsArr
				};

				await applySettingsToMultipleTerminals(payload);
				showBootstrapToast("Settings copied successfully", "Success");
				
				await fetchTabsAndSettings();
				isLoadingCopy.value = false;	
				closeModal('applytoall');
				
			break;
		}
		
	} catch (error) {
		console.error("Error saving settings:", error);
		showBootstrapToast("Failed to save settings", "Error");
		isLoadingCopy.value = false;	
	}
};
// Disable terminal dropdown when there's changes
const hasUnsaved = computed(() => changedCount.value > 0);
// For Save Changes button indicator
const changedCount = computed(() =>
	Object.values(tabData).flat().filter(setting =>
		setting.value !== (originalTabData?.value?.[setting.id] ?? null)
	).length
);
// For tab indicator
const hasUnsavedChanges = (tabId) => {
	const currentSettings = tabData[tabId] || [];
	return currentSettings.some(setting =>
		setting.value !== (originalTabData?.value?.[setting.id] ?? "")
	);
};
// Prevent reloading of page when there's changes
const handleBeforeUnload =(event) => {
	const hasUnsaved = settingTabs.value.some(tab => hasUnsavedChanges(tab.id));
	if (hasUnsaved) {
		event.preventDefault();
		event.returnValue = "";
	}
}
onMounted(() => { window.addEventListener("beforeunload", handleBeforeUnload); });
onBeforeUnmount(() => { window.removeEventListener("beforeunload", handleBeforeUnload); });

const blurSaveBtn = () => {
	nextTick(() => {
		const btnEl = saveBtn.value?.$el || saveBtn.value?.$refs?.button;
		btnEl?.blur?.();
	});
};
const handleIconClick = (label) => {
	if (label === "Copy to..") {
		openModal("copy");
	}
	if (label === "Apply to all") {
		openModal("applytoall");
	}
};

const showCopyModal = ref(false),
	showApplyToAllModal = ref(false),
	multiStepRefCopy = ref(null),
	multiStepRefApplyToAll = ref(null);
const openModal = async (modalType) => {
	const filteredTargets = clientTerminalDetailOptions.value.filter(
			t => t.value !== selectedSourceTerminal.value
		);

	selectedTargetTerminal.value = filteredTargets[0]?.value || null;

	// await fetchModalTabsAndSettings();
	const stop = watch(
		selectedSourceTerminal,
		fetchModalTabsAndSettings,
		{ immediate: true } 
	);

	if (modalType === "copy") {
		showCopyModal.value = true;

		await nextTick();
		multiStepRefCopy.value?.resetSteps();
	} 
	else if (modalType === "applytoall") {
		showApplyToAllModal.value = true;

		await nextTick();
		multiStepRefApplyToAll.value?.resetSteps();
	}
	watch(showCopyModal, (val) => {
		if (!val) stop();
	});
	watch(showApplyToAllModal, (val) => {
		if (!val) stop();
	});

};

const closeModal = (modalType) => {
	if (modalType === "copy") {
		showCopyModal.value = false;
	} else if (modalType === "applytoall") {
		showApplyToAllModal.value = false;
	}
};

</script>
<style>
	.fs-6{
		position: relative;
		left: -9px;
	}
	.tip-icon{
		position:relative;
		top: -1px;
	}
	.issue-icon{
		position:relative;
		top: 2.5px;
	}
	.col-md-6{
		padding: 0 25px 0 25px;
	}
	.custom-gutter {
		--bs-gutter-x: 100px;
	}
	.loading-spinner {
		position: absolute;
		right: -40px; 
		top:-5%;
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