<template>
    <div>
        <div class="btn-icon" @click="cancelPTUModal=true">
            <img src="/resources/images/no-screen.png"></img>
            <label>Cancel PTU</label>
        </div>
        <!--<b-button @click="cancelPTUModal=true">Cancel PTU</b-button>-->
        <Modal v-model="cancelPTUModal" title="Cancel PTU" size="m"
        :hideFooter ="!showFooter"
        :isLoading="isSubmitting"
        @close="closeModal()">
            <MultiStepForm ref="multiStepRefCancelPTU"
						:stepLabels="['Select Terminal', 'Reference Number', 'Confirmation']"
                        :key="cancelPTUModal ? 'open' : 'closed'"
      					@stepBack="showFooter=false, alertMessage=false, checkBIR=false"
						@reset="">

                <template v-slot:step1>
                    <span>Client Group:</span>
                    <GroupList 
                        v-model="groupSelect"
                        :isLoading="isLoading"
                        :disabled="isLoading"
                        :selectedName="selectName"
                        @change="fetchNetworkList()"
                    />
                    <span>Client Network:</span>
                    <select v-model="networkSelect" :disabled="!networkList.length" @change="fetchBranchList()" class="border w-full custom-select mb-2">
                        <option v-for="network in networkList" :value="network.id"> {{network.id}} - {{network.name}}</option>
                    </select>
                    <span>Client Branch:</span>
                    <select v-model="branchSelect" :disabled="!networkList.length" @change="fetchTerminalList()" class="border w-full custom-select mb-2">
                        <option v-for="branch in branchList" :value="branch.id">  {{branch.branch_id}} - {{branch.name}}</option>
                    </select>
                    <span>Client Terminal: <label class="required-input">(required*)</label></span>
                    <select v-model="terminalSelect" :disabled="!branchList.length" class="border w-full custom-select mb-2">
                        <option v-show="terminalShow" v-for="terminal in terminalList" :value="terminal.id">  {{terminal.terminal_number}} </option>
                    </select>
                </template>
                <template v-slot:step2>
                    <p>Request reference/ticket number: <input v-model="requestReferenceNo" class="reference-no-input" maxlength="7" type="text"></p>
                </template>
                <template v-slot:step3>
                    <div>
                        <p><input v-model="checkBIR" type="checkbox">{{ textCheckBIR }}</p>
                    </div>
                    <div>
                        <b-alert v-show="alertMessage.length" v-model="alert" variant="danger">
                            <p style="text-align: center">{{ alertMessage }}</p>
                        </b-alert>
                    </div>
                    <div class="submit-form">
                        <button class="btn btn-success" @click="submitCancelPTU()" :disabled="!checkBIR">
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
import MultiStepForm from '/resources/js/components/common/MultiStep.vue';
import GroupList from '../projectTools/GroupList.vue';

const cancelPTUModal = ref(false),
    currentStep = ref(1),
    alert = ref(true),
    alertMessage = ref(''),
    isLoading = ref(false),
    isSubmitting = ref(false),
    showFooter = ref(false),
    groupSelect = ref(''),
    selectName = ref(''),
    networkList = ref([]),
    networkSelect = ref(''),
    branchList = ref([]),
    branchSelect = ref(''),
    terminalList = ref([]),
    terminalSelect = ref(''),
    terminalShow = ref(false),
    checkBIR = ref(false),
    textCheckBIR = " Has the BIR approved of this procedure? Furthermore, this will increment the reset counter, are you sure you want to continue?",
    requestReferenceNo = ref('');

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

const openModal = () => {
	cancelPTUModal.value = true;
    showFooter.value = false;
};

const closeModal = () => {
	cancelPTUModal.value = false;
    showFooter.value = false;
    alertMessage.value = '';
    checkBIR.value = false;
    requestReferenceNo.value = '';
    groupSelect.value = '';
    networkSelect.value = '';
    branchSelect.value = '';
    terminalSelect.value = '';
};

const nextProgres = () => {
    if (requestReferenceNo.length) {
        
    }
    currentStep++;
}

const fetchNetworkList = async () => {
	if (groupSelect.value == '' || groupSelect.value == null) {
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
	if (networkSelect.value == '' || networkSelect.value == null) {
		console.warn('No client network selected');
		return;
	}
    try {
        const response = await axios.get(`/api/clientbase/branch/list/` + networkSelect.value);
        if (response.data.length == 0)
            return;
        branchList.value = response.data.map(item => ({
            id: item.id.toString(),
            name: item.name,
            branch_id: item.branch_id,
        }));
        branchSelect.value = branchList.value[0].id.toString();
        fetchTerminalList();
    } catch (error) {
        console.error('Error fetching branch list:', error);
    }
};

const fetchTerminalList = async () => {
	if (!branchSelect.value) {
		console.warn('No client network selected');
		return;
	}
    terminalShow.value = false;
    terminalSelect.value = '';
    try {
        const response = await axios.get(`/api/clientbase/terminal/list/` + branchSelect.value);
        if (response.data.length == 0) {
            showBootstrapToast("No terminal found in this branch.", "Warning");
            return;
        }
        terminalList.value = response.data.map(item => ({
            id: item.id.toString(),
            terminal_number: item.terminal_number,
            pos_type: item.pos_type,
        }));
        terminalSelect.value = terminalList.value[0].id.toString();
        terminalShow.value = true;
    } catch (error) {
        console.error('Error fetching terminal list:', error);
    }
};

const submitCancelPTU = async () => {
    alertMessage.value = '';
    if (terminalSelect.value == '' || terminalSelect.value == '0') {
        console.error("No terminal detail selected.");
        showBootstrapToast("Please select a terminal from client details.", "Error");
        alertMessage.value = 'No terminal detail selected.';
        return;
    }

    let formdata = {
        clientgroupid: groupSelect.value,
        networkid: networkSelect.value,
        branchid: branchSelect.value,
        terminalno: terminalSelect.value,
        reference_number: requestReferenceNo.value
    };
    isSubmitting.value = true

    const response = await axios.post(`/api/clientbase/terminal/cancel-ptu/` + terminalSelect.value);
    if (response.data) {
        const result = response.data;
        if (! result.isSuccessful) {
            const error = result.error;
            showBootstrapToast(error, "Error");
            alertMessage.value = error;
        }
        else {
            axios.post(`/api/v1/cancelPTU`, formdata)
            .then(response => {
                if (response.data.isSuccessful) {
                    statusMessage.value = response.data.message;
                }
                isSubmitting.value = false;
            })
            .catch(error => {
                let errors = error.response.data.errors;
                Object.values(errors).forEach(val => {
                    alertMessage.value += val + "\n";
                });
                isSubmitting.value = false;
            });
        }
        isSubmitting.value = false;
    }
};

</script>
<style scoped>
    .required-input {
        color:red;
        font-size: 12px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .reference-no-input {
        margin: auto;
        text-align: left;
        padding: 2px;
        width: 100px;
    }
    .submit-form {
        text-align: center;
        padding-top: 15px;
    }
    .submit-form button {
        width: 200px;
    }
    .btn-icon {
        border: 3px solid black;
        width: 120px;
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
        font-size: 14px;
        font-weight: bold;
        padding-top: 5px;
    }
    
</style>