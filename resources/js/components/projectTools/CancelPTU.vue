<template>
    <div>
        <div class="btn-icon" @click="cancelPTUModal=true">
            <img src="/resources/images/no-screen.png" class="btn-img"></img>
            <label>Cancel PTU</label>
        </div>
        <!--<b-button @click="cancelPTUModal=true">Cancel PTU</b-button>-->
        <Modal v-model="cancelPTUModal" title="Cancel PTU" size="m"
        :hideFooter ="true"
        :isLoading="isSubmitting"
        @close="closeModal()">
            <MultiStepForm ref="multiStepRefCancelPTU"
						:stepLabels="['Select Terminal', 'Reference Number', 'Confirmation']"
                        :key="cancelPTUModal ? 'open' : 'closed'"
                        :nextDisabled="true"
                        :endButtonName="'End'"
                        @stepNext="checkStepValidation()"
      					@stepBack="alertMessage=false, checkBIR=false, checkStepValidation()"
						@reset="">

                <template v-slot:step1>
                    <span>Client Group:</span>
                    <GroupList ref="groupSelectOption"
                        v-model="groupSelect"
                        @change="fetchNetworkList()"
                    />
                    <span>Client Network:</span>
                    <select v-model="networkSelect" :disabled="!networkList.length" @change="fetchBranchList(), checkStepValidation()" class="border w-full custom-select mb-2">
                        <option v-for="network in networkList" :value="network.id"> {{network.id}} - {{network.name}}</option>
                    </select>
                    <span>Client Branch:</span>
                    <select v-model="branchSelect" :disabled="!networkList.length" @change="getBranchIdValue(), fetchTerminalList(), checkStepValidation()" class="border w-full custom-select mb-2">
                        <option v-for="(branch, index) in branchList" :value="index">  {{branch.branch_id}} - {{branch.name}}</option>
                    </select>
                    <span>Client Terminal: <label v-show="showRequiredLabel" class="required-label">(required*)</label></span>
                    <select v-model="terminalSelect" :disabled="!branchList.length" @change="getTerminalNoValue(), checkStepValidation()" class="border w-full custom-select mb-2">
                        <option value="-1" :disabled="true">Select Terminal No.</option>
                        <option v-for="(terminal, index) in terminalList" :value="(index)">  {{terminal.terminal_number}} </option>
                    </select>
                </template>
                <template v-slot:step2>
                    <p>Request reference number: <label v-show="showRequiredLabel" class="required-label">(required*)</label></p>
                    <p><input ref="requestReferenceNoInput" v-model="requestReferenceNo" @input="checkStepValidation()" class="reference-no-input" maxlength="7" type="text" autocomplete="off"></p>
                </template>
                <template v-slot:step3>
                    <div>
                        <p>Has the BIR approved of this procedure?</p>
                        <p>Furthermore, this will increment the reset counter,</p>
                        <p>Are you sure you want to continue?&nbsp;&nbsp;
                            <input v-model="checkBIR" type="checkbox"></input>&nbsp;
                            <label v-show="!checkBIR" class="required-label">(required*)</label>
                        </p>
                    </div>
                    <div>
                        <b-alert v-show="alertMessage.length" v-model="alert" variant="danger">
                            <p style="text-align: center">{{ alertMessage }}</p>
                        </b-alert>
                    </div>
                    <div class="submit-form">
                        <button class="btn btn-success" @click="submitCancelPTU()" v-show="checkBIR" :disabled="isSubmitting">
                            <span v-if="isSubmitting" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            {{ isSubmitting ? 'Requesting...' : 'Submit' }}
                        </button>
                    </div>
                </template>
            </MultiStepForm>
        </Modal>
    </div>
    <div>
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
    
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, computed, watch, reactive, nextTick  } from 'vue';
import Modal from '/resources/js/components/common/Modal.vue';
import ToastNotification from '/resources/js/components/common/ToastNotification.vue';
import MultiStepForm from '/resources/js/components/common/MultiStepWithValidation.vue';
import GroupList from '../common/GroupList.vue';

const cancelPTUModal = ref(false),
    multiStepRefCancelPTU = ref(true),
    showRequiredLabel = ref(true),
    alert = ref(true),
    alertMessage = ref(''),
    isSubmitting = ref(false);

const groupSelect = ref(0),
    groupSelectOption = ref(true),
    networkList = ref([]),
    networkSelect = ref(0),
    branchList = ref([]),
    branchSelect = ref(0),
    clientBranchId = ref(0),
    branchId = ref(0),
    terminalList = ref([]),
    terminalSelect = ref(0),
    clientTerminalId = ref(0),
    terminalNo = ref(0),
    terminalShow = ref(false),
    checkBIR = ref(false),
    requestReferenceNo = ref(''),
    requestReferenceNoInput = ref(true);

const showToast = ref(false),
	toastMessage = ref(''),
	toastType = ref(''),
	toastTitle = ref('Notification'),
	toastRef = ref(null);
const showBootstrapToast = (message, type, title = 'Notification', duration = 3000) => {
	toastMessage.value = message;
	toastType.value = type
	toastTitle.value = title;
	showToast.value = true;

const toastElement = toastRef.value;
if (toastElement && !showToast.value) {
	const toastInstance = new Toast(toastElement);
	toastInstance.show();
}

setTimeout(() => {
	showToast.value = false;
	}, duration);
};


const setNextDisabled = (bool = false) => {
    multiStepRefCancelPTU.value.disableNextStep(bool);
}

const checkStepValidation = () => {
    let currentStep = multiStepRefCancelPTU.value.getCurrentStep();
    let condition = true;
    switch (currentStep) {

        case 1:
            condition = clientTerminalId.value == 0;
            setNextDisabled(condition)
            break;

        case 2:
            condition = requestReferenceNo.value == '';
            setNextDisabled(condition);
            break;

        default:
            break;
    }
    showRequiredLabel.value = condition;
}

const closeModal = () => {
    cancelPTUModal.value = false;
	resetValues();
};
const resetValues = () => {
    alertMessage.value = '';
    checkBIR.value = false;
    requestReferenceNo.value = '';
    groupSelect.value = 0;
    networkSelect.value = 0;
    networkList.value = [];
    branchSelect.value = 0;
    branchList.value = [];
    terminalSelect.value = 0;
    terminalList.value = [];
    isSubmitting.value = false;
};

const getBranchIdValue = (index = branchSelect.value) => {
    clientBranchId.value = branchList.value[index].id;
    branchId.value = branchList.value[index].branch_id;
};

const getTerminalNoValue = (index = terminalSelect.value) => {
    clientTerminalId.value = terminalList.value[index].id;
    terminalNo.value = terminalList.value[index].terminal_number;
};

const fetchNetworkList = async () => {
	if (groupSelect.value == '' || groupSelect.value == 0) {
		console.warn('No client group selected');
		return;
	}
    try {
        const response = await axios.get(`/api/clientbase/network/list/` + groupSelect.value);
        if (response.data.length == 0)
            return;
        networkList.value = response.data.map(item => ({
            id: item.id.toString(),
            name: item.name,
        }));
        networkSelect.value = networkList.value[0].id.toString();
        fetchBranchList();
    } catch (error) {
        console.error('Error fetching network list:', error);
    }
};

const fetchBranchList = async () => {
	if (networkSelect.value == 0) {
		return;
	}
    try {
        const response = await axios.get(`/api/clientbase/branch/list/` + networkSelect.value);
        if (response.data.length == 0)
            return;
        branchList.value = response.data.map(item => ({
            id: item.id,
            name: item.name,
            branch_id: item.branch_id,
        }));
        branchSelect.value = 0;
        clientBranchId.value = branchList.value[0].id;
        terminalSelect.value = 0;
        fetchTerminalList();
    } catch (error) {
        console.error('Error fetching branch list:', error);
    }
};

const fetchTerminalList = async () => {
	if (clientBranchId.value == 0) {
		return;
	}
    try {
        const response = await axios.get(`/api/clientbase/terminal/list/` + clientBranchId.value);
        if (response.data.length == 0) {
            terminalSelect.value = -1;
            clientTerminalId.value == 0;
            terminalList.value = [];
            terminalShow.value = false;
            showBootstrapToast("No terminals found in this branch.", "Warning");
            return;
        }
        terminalList.value = response.data.map(item => ({
            id: item.id,
            terminal_number: item.terminal_number,
            pos_type: item.pos_type,
        }));
        terminalShow.value = true;
    } catch (error) {
        console.error('Error fetching terminal list:', error);
    }
};

const submitCancelPTU = async () => {
    if (isSubmitting.value) 
        return;
    alertMessage.value = '';
    if (clientTerminalId.value == 0) {
        console.error('No terminal detail selected.');
        showBootstrapToast('Please select a terminal from client details.', 'Error');
        alertMessage.value = 'No terminal detail selected.';
        return;
    }

    let formdata = {
        clientgroupid: groupSelect.value,
        networkid: networkSelect.value,
        branchid: branchId.value,
        terminalno: terminalNo.value,
        reference_number: requestReferenceNo.value
    };
    isSubmitting.value = true

    const response = await axios.post(`/api/clientbase/terminal/cancel-ptu/` + clientTerminalId.value);
    if (response.data) {
        const result = response.data;
        if (! result.isSuccessful) {
            const error = result.error;
            showBootstrapToast(error, 'Error');
            alertMessage.value = error;
        }
        else {
            axios.post(`/api/v1/cancelPTU`, formdata)
            .then(response => {
                if (response.data.isSuccessful) {
                    showBootstrapToast(response.data.message, 'Success');
                    closeModal();
                }
                isSubmitting.value = false;
            })
            .catch(error => {
                let errors = error.response.data.errors;
                alertMessage.value = '';
                Object.values(errors).forEach(val => {
                    alertMessage.value += val + '\n';
                });
                isSubmitting.value = false;
            });
        }
        isSubmitting.value = false;
    }
};

</script>
<style scoped>
    .required-label {
        color:red;
        font-size: 12px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .reference-no-input {
        margin: auto;
        text-align: left;
        padding: 2px 5px;
        width: 100px;
    }
    .submit-form {
        text-align: center;
    }
    .submit-form button {
        width: 200px;
    }
    .btn-icon {
        border: 2px solid rgb(46, 46, 46);
        width: 100px;
        height: 100px;
        padding: 15px;
        text-align: center;
        cursor: pointer;
    }
    .btn-icon:hover {
        background-color: rgba(165, 126, 0, 0.3);
    }
    .btn-icon:hover label{
        text-decoration: underline;
    }
    .btn-icon label {
        font-size: 11px;
        font-weight: bold;
        padding-top: 5px;
        cursor: pointer;
    }
    .btn-img {
        width: 45px;
    }
    input[type=checkbox] {
        transform: scale(1.5);
        cursor: pointer;
    }
</style>