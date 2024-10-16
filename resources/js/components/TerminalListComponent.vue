<template>
    <div class="d-flex justify-content-center align-items-center">
        <p class="text-center network-name">{{ clients.length ? clients[0].clientGroupName : 'No clients available' }}</p>
    </div>
    <div class="d-flex justify-content-center mb-5 align-items-center">
        <b-col class="col-sm-2">
            <b-form-select v-model="selected" size="sm" class="dropdown" @input="onClientSelect">
                <template #option="{ option }">
                    <b-form-select-option :value="option.value" class="option-style">
                        {{ option.text }}
                    </b-form-select-option>
                </template>
                <template #default>
                    <b-form-select-option 
                        v-for="option in clientOptions" 
                        :key="option.value" 
                        :value="option.value" 
                        class="option-style">
                        {{ option.text }}
                    </b-form-select-option>
                </template>
            </b-form-select>
        </b-col>
    </div>
    <div class="d-flex justify-content-center mt-5 align-items-center">
        <div v-if="loading" class="overlay">
            <b-spinner label="Loading..." variant="secondary" type="grow"></b-spinner>
        </div>
        <div v-if="!loading && items.length == 0" class="overlay">
            <h5 class="no-data-text">NO DATA AVAILABLE</h5>
        </div>
        <b-table hover
            :outlined="outlined"
        
            :items="paginatedItems"
            :fields="fields"
            class="tblStyle">
            <template #cell(hardware)="data">
                   <span>-</span>
            </template>
        </b-table>

    </div>
    <div class="d-flex justify-content-center mt-5 align-items-center">
        <b-pagination
            v-model="currentPage"
            :total-rows="items.length"
            :per-page="itemsPerPage"
            aria-controls="my-table">
        </b-pagination>
    </div>
</template>
  
<script>
    function getApiBaseUrl() {
        const protocol = window.location.protocol;
        const host = window.location.hostname;
        const port = 92;

        return `${protocol}//${host}:${port}/api/v1`;
    }
    const apiUrl = getApiBaseUrl(); 

    export default {
        data() {
            return {
                clients:[],
                items: [],
                selected: null,
                outlined: true,
                loading: false,
                currentPage: 1,  
                itemsPerPage: 5,
                fields: [
                    {
                        key: 'id', 
                        label: 'ID',
                        thStyle: { width: '5%' },
                        thClass: 'th-style',
                        tdClass: 'td-center td-style'
                    },   
                    {
                        key: 'branch', 
                        label: 'Branch',
                        thStyle: { width: '15%' },
                        thClass: 'th-style',
                        tdClass: 'td-left td-style'
                    },      
                    {
                        key: 'terminal', 
                        label: 'T#',
                        thStyle: { width: '5%' },
                        thClass: 'th-style',
                        tdClass: 'td-center td-style'
                    },      
                    {
                        key: 'version', 
                        label: 'Version',
                        thStyle: { width: '8%' },
                        thClass: 'th-style',
                        tdClass: 'td-center td-style'
                    },      
                    {
                        key: 'latest_update', 
                        label: 'Latest Update',
                        thStyle: { width: '8%' },
                        thClass: 'th-style',
                        tdClass: 'td-center td-style'
                    },   
                    {
                        key: 'setting', 
                        label: 'Setting',
                        thStyle: { width: '8%' },
                        thClass: 'th-style',
                        tdClass: 'td-center td-style'
                    },      
                    {
                        key: 'backup', 
                        label: 'Backup',
                        thStyle: { width: '8%' },
                        thClass: 'th-style',
                        tdClass: 'td-center td-style'
                    },      
                    {
                        key: 'maintenance', 
                        label: 'License/Maintenance',
                        thStyle: { width: '9%' },
                        thClass: 'th-style',
                        tdClass: 'td-center td-style'
                    },       
                    {
                        key: 'hardware', 
                        label: 'Hardware',
                        thStyle: { width: '5%' },
                        thClass: 'th-style',
                        tdClass: 'td-center td-style'
                    },   
                ]
            }
        },
        props: {
            detail: {
            type: String,
            required: true
            }
        },
        computed: {
            paginatedItems() {
                const start = (this.currentPage - 1) * this.itemsPerPage;
                const end = start + this.itemsPerPage;
                return this.items.slice(start, end); 
            },
            clientOptions() {
                return this.clients.map(client => ({
                    value: client.clientNetworkId, 
                    text: client.clientNetworkName,
                    class: 'option-style' 
                }));
            }
        },
        methods: {
            formatDate(dateString) {
                const dateOnly = dateString.split(' ')[0]; 
                const date = new Date(dateOnly); 
                const options = {
                    year: 'numeric',
                    month: 'long', 
                    day: 'numeric', 
                };

                if(date == 'Invalid Date'){
                    return '-';
                }
                return date.toLocaleDateString('en-US', options);
            },
            fetchClient() {
                const terminalData = {
                    clientGroupId: this.detail,
                };
                axios.post(apiUrl + '/TerminalList/GetClient', terminalData, {
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Basic bmVsc29mdDoxMjE1ODY='  
                    }
                })
                .then((res) => { 
                    this.clients = res.data.map(item => ({
                        clientGroupId: item[0],
                        clientGroupName: item[1], 
                        clientNetworkId: item[2], 
                        clientNetworkName: item[3],
                    }));

                    if (this.clients.length > 0) {
                        this.selected = this.clients[0].clientNetworkId;  
                        this.onClientSelect(); 
                    }
                })
                .catch((error) => console.log(error));
            },
            fetchTerminals(clientGroupId, clientNetworkId) {
                this.loading = true;
                const terminalData = {
                    clientGroupId: clientGroupId,
                    clientNetworkId: clientNetworkId,
                };

                return axios.post(apiUrl + '/TerminalList/TerminalStatus', terminalData, {
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Basic bmVsc29mdDoxMjE1ODY='  
                    }
                })
                .then((res) => { 
                    this.items = res.data.map(entry => {
                        const [
                            idArray, branchArray, terminalArray, versionArray,
                            updateArray, backupArray, maintenanceArray
                        ] = entry;

                        const id = idArray[0];
                        const branch = branchArray[0];
                        const terminal  = terminalArray[0];
                        const version = versionArray[0];
                        const latest_update = updateArray[0];
                        const backup = backupArray[0];
                        const maintenance = maintenanceArray[0];
                        const backupFormatted = this.formatDate(backup);
                        
                        const setting = null;
                        return { id, branch, terminal, version, latest_update, setting, backup: backupFormatted, maintenance };
                    });
                    this.loading = false;
                })
                .catch((error) => {
                    console.log(error);
                    this.loading = false;
                });
            },
            checkPosSettings() {
                const clientTerminalIds = this.items.map(item => item.id);
                const clientterminalidApi = {
                    clientTerminalId: clientTerminalIds
                };

                const api1Promise = axios.post(apiUrl + '/TerminalList/PosSettingsList', clientterminalidApi, {
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Basic bmVsc29mdDoxMjE1ODY='  
                    }
                });

                const api2Promise = axios.post('/api/v1/settings', clientterminalidApi, {
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Basic bmVsc29mdDoxMjE1ODY='  
                    }
                });

                Promise.all([api1Promise, api2Promise])
                    .then(([res1, res2]) => {
                        const apiData = res1.data;
                        const posData = res2.data;
                        const state = [];

                        // Compare data 
                        apiData.forEach(apiEntry => {
                            const matchedEntry = posData.find(posEntry => {
                                return Number(apiEntry.clientterminalid) === posEntry.clientterminalid && 
                                    apiEntry.key.trim().toLowerCase() === posEntry.key.trim().toLowerCase();
                            });
                            // check if id is existing in array to avoid duplication
                            const entryExistsInstate = (clientterminalid) => {
                                return state.some(diff => 
                                    diff.clientterminalid === clientterminalid
                                );
                            };

                            if (matchedEntry) {
                                if (apiEntry.value !== matchedEntry.value) {
                                    if (!entryExistsInstate(apiEntry.clientterminalid)) {
                                        state.push({
                                            clientterminalid: apiEntry.clientterminalid,
                                            key: apiEntry.key,
                                            apiValue: apiEntry.value,
                                            posValue: matchedEntry.value,
                                            status: 'Outdated'  
                                        });
                                    }
                                }else{
                                    if (apiEntry.value === matchedEntry.value) {
                                        if (!entryExistsInstate(apiEntry.clientterminalid)) {
                                            state.push({
                                                clientterminalid: apiEntry.clientterminalid,
                                                key: apiEntry.key,
                                                apiValue: apiEntry.value,
                                                posValue: matchedEntry.value,
                                                status: 'Updated'  
                                            });
                                        }
                                    }
                                }
                            } else {
                                if (!entryExistsInstate(apiEntry.clientterminalid)) {
                                    state.push({
                                        clientterminalid: apiEntry.clientterminalid,
                                        key: apiEntry.key,
                                        apiValue: apiEntry.value,
                                        posValue: null,
                                        status: 'Outdated' 
                                    });
                                }
                            }
                        });

                        // get all status then feed it to col setting
                        const statusArr = state.map(diff => ({
                            clientterminalid: diff.clientterminalid,
                            status: diff.status  
                        }));
                        this.items.forEach(item => {
                            const statusEntry = statusArr.find(status => status.clientterminalid === item.id);
                            if (statusEntry) {
                                item.setting = statusEntry.status;
                            } else {
                                item.setting = 'Unknown';
                            }
                        });

                    })
                    .catch((error) => {
                        console.error("Error fetching terminal data:", error);
                    });
            },
            onClientSelect() {
                if (this.selected) {
                    const selectedClient = this.clients.find(client => client.clientNetworkId === this.selected);
                    if (selectedClient) {
                        const { clientGroupId, clientNetworkId } = selectedClient;
                        this.currentPage = 1;
                        // this.fetchTerminals(clientGroupId, clientNetworkId); 
                        this.fetchTerminals(clientGroupId, clientNetworkId).then(() => {
                            this.checkPosSettings();  // Ensure this runs after terminals are fetched
                        });
                    }
                    this.checkPosSettings();
                }
            }
        },
        mounted() {
            this.fetchClient(); 
        }
    }
</script>

<style>
.overlay {
    position: absolute;
    background: rgb(255, 255, 255); /* Light overlay */
    display: flex;
    justify-content: center;
    align-items: center;
}

.b-spinner {
    width: 3rem;
    height: 3rem;
}
.th-style   {
    text-align: center;
    background-color: #e7e7e7 !important;
    border-bottom: none;
}
.tblStyle   {
    height: 250px;
}
.td-style{
    font-size: 0.9rem;
}
.td-center{
    text-align: center;
}
.td-left{
    text-align: left;
}
.dropdown{
    border-radius:36px;
    margin-bottom: 20px;
}
.option-style{
    text-align: left;;
    font-size: 0.8rem;
    background-color: white; /* Default background color */
    color: black; /* Default text color */
}
select .option-style:hover{
    background-color: rgb(179, 178, 178) !important; 
    color: black; 
    cursor: pointer; 
}

.network-name{
    font-size: 2.8rem;
    font-weight:500;
    margin-bottom: 0;
}
.form-select:focus{
    border-color: #e7e7e7;
    box-shadow: 0 0 0 0.25rem rgb(231 231 231);
}
.page-item.active .page-link {
    border-color: #e7e7e7;
    background-color: #e7e7e7;
    color:#424242;
    box-shadow: none;
}
.page-item .page-link{
    color:#424242
}
</style>