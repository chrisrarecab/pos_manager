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
            :bordered="bordered"
            :items="paginatedItems"
            :fields="fields"
            class="tblStyle">
            <template #cell(version)="data">
                <div class="version-badge">
                    <span v-html="data.item.version"></span>&nbsp;
                    <div class="version-image-container">
                        <div v-if="data.item.isVersionOutdated.isOutdated == true" class="tooltip-container" @mouseenter="showTooltip(data.item.id)" @mouseleave="hideTooltip">
                        <div class="version-container">
                            <img :src="updateImg" height="15" width="15" alt="">
                            <!-- <b-badge href="#" variant="secondary">{{ data.item.isVersionOutdated.difference }}</b-badge> -->
                        </div>
                        <div class="tooltip" v-if="hoveredSettingId === data.item.id">
                            <div class="tooltip-items">
                            <p><strong>Available Update: &nbsp;</strong> {{ data.item.latest_update }}</p>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </template>
            <template #cell(setting)="data">
                <div class="tooltip-container" @mouseenter="showTooltip(data.item.id)" @mouseleave="hideTooltip">
                    <div class="status">
                        <span v-if="data.item.setting === 'Updated'">
                            <b-badge variant="success"><span v-html="data.item.setting"></span></b-badge>
                        </span>
                        <span v-else-if="data.item.setting === 'Outdated'">
                            <b-badge variant="danger"><span v-html="data.item.setting"></span></b-badge>
                        </span>
                        <span v-else>
                                <span>-</span>
                        </span>
                    </div>
                    <div class="tooltip" v-if="hoveredSettingId === data.item.id">
                        <div class="tooltip-items">
                            <p><strong>Last Update:  &nbsp;</strong> {{ data.item.settings_date }}</p>
                        </div>
                    </div>
                </div>
            </template>
            <template #cell(maintenance)="data">
                <div class="tooltip-container" @mouseenter="showTooltip(data.item.id)" @mouseleave="hideTooltip">
                    <div class="status-maintenance">
                        <span v-if="data.item.maintenance === 'Updated'">
                            <b-badge variant="success" class="badge-content"> <img :src="checkImg"  height="10" width="10" alt="">Active</b-badge>
                        </span>
                        <span v-else-if="data.item.maintenance === 'Expired'">
                            <b-badge variant="danger" class="badge-content"><img :src="crossImg"  height="10" width="10" alt=""> Expired</b-badge>
                        </span>
                        <span v-else>
                                <span>-</span>
                        </span>
                    </div>
                    <div class="tooltip" v-if="hoveredSettingId === data.item.id">
                        <div class="tooltip-items">
                            <p> <strong>Expired On:  &nbsp;</strong> {{ data.item.expirationDate }}</p>
                        </div>
                    </div>
                </div>
            </template>
            <template #cell(hardware)="data">
                <div class="tooltip-container" @mouseenter="showTooltip(data.item.id)" @mouseleave="hideTooltip">
                    <span :data-hardware-id="data.item.id" v-html="data.item.hardware"></span>
                    <div class="tooltip" v-if="hoveredSettingId === data.item.id">
                        <div class="tooltip-items">
                            <p>
                            <strong>CPU Usage:  &nbsp;</strong>
                            <span :style="{
                                display: 'inline-block',
                                width: '13px',
                                height: '13px',
                                backgroundColor: getCpuUsageColor(data.item.cpuUsage),
                                borderRadius: '20%',
                                margin: '0 0 0 1px'
                                }"></span>&nbsp; {{ data.item.cpuUsage }}%
                            </p>
                            <p>
                                <strong> Ram Usage: &nbsp;</strong>
                                <span :style="{
                                display: 'inline-block',
                                width: '13px',
                                height: '13px',
                                backgroundColor: getRamUsageColor(data.item.ramUsage),
                                borderRadius: '20%',
                                margin:'0'
                                }"></span>&nbsp;{{ data.item.ramUsage }}%
                            </p>
                            <p>
                                <strong> Disk Usage: &nbsp;</strong>
                                <span :style="{
                                display: 'inline-block',
                                width: '13px',
                                height: '13px',
                                backgroundColor: getDiskUsageColor(data.item.spaceUsage),
                                borderRadius: '20%',
                                margin: '0 0 0 1px'
                                }"></span>&nbsp;{{  data.item.spaceUsage }}%
                            </p>
                            <p><strong> Last Update: &nbsp;</strong>{{ data.item.serverDate }}</p>
                        </div>
                    </div>
                </div>
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
    const headers = {
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Basic bmVsc29mdDoxMjE1ODY='  
        }
    };

    function getApiBaseUrl() {
        const protocol = window.location.protocol;
        const host = window.location.hostname;
        const port = 92;

        return `${protocol}//${host}:${port}/api/v1`;
    }
    const apiUrl = getApiBaseUrl(); 
    import serverImage from '../../images/server.svg'; 
    import updateImg from '../../images/update.svg'; 
    import check from '../../images/check.svg';  
    import cross from '../../images/cross.svg';    

    import '../../css/terminalList.css';

    export default {
        data() {
            return {
                hoveredSettingId: null,
                clients:[],
                items: [],
                selected: null,
                bordered: true,
                loading: false,
                currentPage: 1,  
                itemsPerPage: 5,
                updateImg: updateImg,
                checkImg: check,
                crossImg: cross,
                fields: [ 
                    { key: 'id', label: 'ID', thStyle: { width: '5%' }, thClass: 'th-style', tdClass: 'td-center td-style' },   
                    { key: 'branch', label: 'Branch', thStyle: { width: '15%' }, thClass: 'th-style ', tdClass: ' td-style' },      
                    { key: 'terminal', label: 'Terminal', thStyle: { width: '5%' }, thClass: 'th-style', tdClass: 'td-center td-style' },      
                    { key: 'version',  label: 'Version', thStyle: { width: '8%' }, thClass: 'th-style', tdClass: 'td-center td-style' },      
                    // { key: 'latest_update', label: 'Latest Update', thStyle: { width: '8%' }, thClass: 'th-style',  tdClass: 'td-center td-style' },   
                    { key: 'setting', label: 'Setting', thStyle: { width: '8%' }, thClass: 'th-style', tdClass: 'td-center td-style' },      
                    { key: 'backup', label: 'Backup', thStyle: { width: '8%' }, thClass: 'th-style', tdClass: 'td-center td-style' },      
                    { key: 'maintenance', label: 'License/Maintenance', thStyle: { width: '8%' }, thClass: 'th-style', tdClass: 'td-center td-style'},       
                    { key: 'hardware', label: 'Hardware', thStyle: { width: '5%' }, thClass: 'th-style', tdClass: 'td-center td-style'},   
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
            compareVersions(id, v1, v2) {
                const v1Set = v1.split('.').map(Number); // [4, 4, 42, 03]
                const v2Set = v2.split('.').map(Number); // [4, 4, 38, 3]
                
                for (let i = 0; i < Math.max(v1Set.length, v2Set.length); i++) {
                    const v1Part = v1Set[i] || 0;
                    const v2Part = v2Set[i] || 0;

                    if (v1Part < v2Part) {
                        return {
                            id: id,
                            isOutdated: true,
                            // difference: v2Set[i] - v1Part,
                            message: `${v2}`
                        };
                    } else if (v1Part > v2Part) {
                        return {
                            id: id,
                            isOutdated: false,
                            message: 'Updated'
                        };
                    }
                }
                return {
                    id: id,
                    isOutdated: false,
                    message: 'Updated'
                };
            },
            fetchClient() {
                //client network dropdown 
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

                return axios.post(apiUrl + '/TerminalList/TerminalStatus', terminalData, headers)
                .then((res) => { 

                    this.items = res.data.map(entry => {
                        const uuid = entry.uuid;
                        const id = entry.id;
                        const branch = entry.branch;
                        const terminal = entry.terminal;
                        const backup = entry.backup;
                        const maintenance = entry.maintenance;
                        const expirationDate = entry.expire_at;
                        const cpuUsage = parseFloat(entry.cpu_usage) || 0; 
                        const ramUsage = parseFloat(entry.ram_usage) || 0; 
                        const spaceUsage = parseFloat(entry.space_usage) || 0;
                        const serverDate = entry.server_status_date;

                        const version = entry.current_version || '-';
                        const latest_update = entry.update_version || '-';

                        const shouldDisplayImage = cpuUsage || ramUsage || spaceUsage;
                        const imageWithTooltip = shouldDisplayImage 
                            ? `<span data-id="${id}" class="tooltip-image">
                                <img src="${serverImage}" height="20" width="20" alt=""></span>` 
                            : '-';

                        let isVersionOutdated = this.compareVersions(id, version, latest_update);
  
                        const hardware = imageWithTooltip;
                               
                        return {uuid, id, branch, terminal, version, isVersionOutdated, latest_update,
                                backup, maintenance, expirationDate, hardware, cpuUsage, ramUsage, spaceUsage, serverDate};
                    });
   
                    this.checkPosSettings(); 
                    this.loading = false;   
                })
                .catch((error) => {
                    console.log(error);
                    this.loading = false;
                });

            },
            checkPosSettings() {
               
                const clientTerminalUuids = this.items.map(item => item.uuid);
                const clientterminalUuidApi = {
                    uuid: clientTerminalUuids
                };
                const clientbaseData = axios.post(apiUrl + '/TerminalList/PosSettingsList', clientterminalUuidApi, headers);
                const posmanagerData = axios.post('/api/v1/settings', clientterminalUuidApi, headers);
                    
                Promise.all([clientbaseData, posmanagerData])
                    .then(([res1, res2]) => {
                        const clientbaseData = res1.data;
                        let posmanagerData = res2.data;
                        const state = [];
                        
                        posmanagerData = posmanagerData.map(posmanagerEntry => {
                            const findClientterminalId = this.items.find(item => item.uuid === posmanagerEntry.uuid);
                            return {
                                ...posmanagerEntry,
                                clientterminalId: findClientterminalId ? findClientterminalId.id : null 
                            };
                        });

                        clientbaseData.forEach(clientbaseEntry => {
                            const matchedEntry = posmanagerData.find(posmanagerEntry => {
                                return Number(clientbaseEntry.clientterminalid) === Number(posmanagerEntry.clientterminalId) && 
                                    clientbaseEntry.key.trim().toLowerCase() === posmanagerEntry.key.trim().toLowerCase();
                            });
                            const matchedEntryValue = matchedEntry?.value || '';
                            const settings_date = clientbaseEntry.settings_date || matchedEntry?.settings_date || 'N/A';
                            if (matchedEntry) {
                                this.loading = true;
                                if (clientbaseEntry.value !== matchedEntryValue) {
                                    if (clientbaseEntry.clientterminalid) {
                                        state.push({
                                            clientterminalid: clientbaseEntry.clientterminalid,
                                            key: clientbaseEntry.key,
                                            cbPossettingsValue: clientbaseEntry.value,
                                            pmPossettingsValue: matchedEntryValue,
                                            status: 'Outdated',  
                                            settings_date
                                        }); 
                                    }
                                }else{
                                    if (clientbaseEntry.value === matchedEntryValue) {
                                        if (clientbaseEntry.clientterminalid) {
                                            state.push({    
                                                clientterminalid: clientbaseEntry.clientterminalid,
                                                key: clientbaseEntry.key,
                                                cbPossettingsValue: clientbaseEntry.value,
                                                pmPossettingsValue: matchedEntryValue,
                                                status: 'Updated',
                                                settings_date  
                                            });
                                        }
                                    }
                                }
                            } else {
                                if (clientbaseEntry.clientterminalid) {
                                    state.push({
                                        clientterminalid: clientbaseEntry.clientterminalid,
                                        key: clientbaseEntry.key,
                                        cbPossettingsValue: clientbaseEntry.value,
                                        pmPossettingsValue: null,
                                        status: 'Outdated',
                                        settings_date
                                    });
                                }
                            }
                        });
                        
                        const statusMap = new Map();

                        state.forEach(entry => {
                            // change entry when there is another entry with the same id and the status = outdated 
                            // but the id exist and the status = updated 
                            const existingEntry = statusMap.get(entry.clientterminalid);
                            if (!existingEntry || entry.status === 'Outdated') {
                                statusMap.set(entry.clientterminalid, entry);
                            }
                        });

                        const filteredState = Array.from(statusMap.values());
                        const statusArr = filteredState.map(diff => ({
                            clientterminalid: diff.clientterminalid,
                            status: diff.status,
                            settings_date: diff.settings_date 
                        }));
      
                        // feed the results to this.items 
                        this.items.forEach(item => {
                            const statusEntry = statusArr.find(status => status.clientterminalid === item.id);
                            if (statusEntry) {
                                item.setting = statusEntry.status;
                                item.settings_date = statusEntry.settings_date;
                            } else {
                                item.setting = '-';
                            }
                        });
                        this.loading = false;
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
                        this.fetchTerminals(clientGroupId, clientNetworkId)
                    }
                }
            },
            showTooltip(id) { this.hoveredSettingId = id;  },
            hideTooltip() { this.hoveredSettingId = null; },
            getCpuUsageColor(usage) {
                if(usage >= 90){
                    return 'red'; 
                }else if(usage >= 80 && usage <= 89.99 ){
                    return 'yellow'; 
                }else if(usage > 0 && usage < 80){
                    return 'green'; 
                }else{
                    return 'black';
                }
            },
            getRamUsageColor(usage) {
                if(usage >= 80){
                    return 'red'; 
                }else if(usage >= 70 && usage <= 79.99){
                    return 'yellow'; 
                }else if(usage >= 0 && usage <= 69 ){
                    return 'green';
                }else{
                    return 'black'; 
                }
            },
            getDiskUsageColor(usage) {
                if(usage >= 80){
                    return 'red'; 
                }else if(usage >= 50 && usage <= 79.99){
                    return 'yellow'; 
                }else if(usage >= 0 && usage <= 49 ){
                    return 'green';
                }else{
                    return 'black'; 
                }
            }
        },
        mounted() {
            this.fetchClient(); 
        },
    }
</script>
