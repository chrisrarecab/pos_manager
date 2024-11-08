<template>
    <div style="width:97%;">
    <div class="d-flex justify-content-center align-items-center">
        <p class="text-center network-name">{{ clients.length ? clients[0].clientGroupName.toUpperCase() : 'No clients available' }}</p>
    </div>
    <span class="spn-lnk"><b-link @click="addNewUser">Add New User</b-link></span>
    <div class="header"><h2>Users</h2></div>
    <div class="d-flex justify-content-center mt-5 align-items-center">
        <!-- <div v-if="loading" class="overlay">
            <b-spinner label="Loading..." variant="secondary" type="grow"></b-spinner>
        </div> -->
        <div v-if="users.length == 0" class="overlay">
            <h5 class="no-data-text">NO DATA AVAILABLE</h5>
        </div> 
        
        <b-table striped hover
            :bordered="bordered"
            :items="paginatedItems"
            :fields="fields"
            @row-clicked="editItem"
            class="tbl">
            <template #cell(id)="data">
                {{ data.item.id }}
            </template>
            <template #cell(full_name)="data">
                {{ data.item.full_name }}
            </template>
            <template #cell(username)="data">
                {{ data.item.username }}
            </template>
            <template #cell(status)="data">
                <div v-if="data.item.status == 1">
                    ACTIVE
                </div>
                <div v-else-if="data.item.status == 0">
                    INACTIVE
                </div>
                <div v-else>
                    PENDING
                </div>
                
            </template>
            <template #cell(action)="data">
                <!-- <b-link :to="{ path: 'userdetails', query: { detail: data.item.id } }"><img src="../../images/icons8-edit.svg" height="25px" width="25px" fluid alt="Responsive image" ></b-link> -->
                <b-link @click="deleteItem(data.item)"><img src="../../images/icons8-delete.svg" height="25px" width="25px" fluid alt="Responsive image" ></b-link>
                <!-- <b-button variant="danger" @click="editItem(data.item.id)">DELETE</b-button> -->
            </template>
            
            
        </b-table>
    </div>
    <div class="d-flex justify-content-center mt-5 align-items-center">
        <b-pagination
            v-model="currentPage"
            :total-rows="users.length"
            :per-page="itemsPerPage"
            aria-controls="my-table">
        </b-pagination>
    </div>
    </div>
</template>
<style scoped>
.tbl {
    width:1000px;
}

.header {
    position:relative;
    left:130px;
    top: 30px;
}
.overlay {
    position: absolute;
    background: rgb(255, 255, 255); 
    display: flex;
    justify-content: center;
    align-items: center;
    top: 540px; 
}
.no-data-text {
    top: 850px; 
}

.spn-lnk {
    position: relative;
    top:80px;
    left: 1020px;
}



</style>

<script>
    import '../../css/style.css';

    function getApiBaseUrl() {
        const protocol = window.location.protocol;
        const host = window.location.hostname;
        const port = 83;

        return `${protocol}//${host}:${port}/api/v1`;
    }
    const apiUrl = getApiBaseUrl(); 

    export default {
        data() {
            return {
                users: [],
                clients:[],
                newId: '',
                selected: null,
                bordered: true,
                loading: false,
                currentPage: 1,  
                itemsPerPage: 5,
                fields: [ 
                    // { key: 'id', label: 'ID', thStyle: { width: '5%' }, thClass: 'th-style', tdClass: 'td-center td-style' },   
                    { key: 'full_name', label: 'Full Name', thStyle: { width: '40%' }, thClass: 'th-style ', tdClass: ' td-style' },      
                    { key: 'username', label: 'Username', thStyle: { width: '20%' }, thClass: 'th-style', tdClass: 'td-style' },      
                    { key: 'status',  label: 'Status', thStyle: { width: '20%' }, thClass: 'th-style', tdClass: 'td-style' },
                    { key: 'action',  label: '', thStyle: { width: '20%' }, thClass: 'th-style', tdClass: 'td-center td-style' }      
                ]
            }
        },
        props: {
            detail: {
                type: String,
                required: true
            }
        }, 
        mounted(){
            this.fetchClient();
        },
        computed: {
            paginatedItems() {
                const start = (this.currentPage - 1) * this.itemsPerPage;
                const end = start + this.itemsPerPage;
                return this.users.slice(start, end); 
            }
        },
        methods: {
            getUsers(){
                // console.log(this.selected);
                axios.get('/api/v1/userlistApi',{ params: { detail: this.selected } }).then(res=>{
                    this.users = res.data.users;
                    // console.log(this.users);
                    // this.users.forEach(item => {
                    //     item.id = id;
                    //     item.full_name = full_name;
                    //     item.username = username;
                    //     item.status =status;
                    //     // console.log(item);
                        
                    // });
                    this.users.map(item =>( {
                        id: item.id,
                        full_name: item.full_name,
                        username: item.username,
                        status: item.status
                    }));
                });
            },
            fetchClient() {
                const networkData = {
                    clientNetworkId: this.detail,
                };
                axios.post(apiUrl + '/TerminalList/GetClientByClientNetwork', networkData, {
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

                    // console.log(this.clients);

                    if (this.clients.length > 0) {
                        this.selected = this.clients[0].clientNetworkId; 
                        // console.log(this.selected) ;
                        this.onClientSelect(); 
                    }
                })
                .catch((error) => console.log(error));
            },
            onClientSelect() {
                if (this.selected) {
                    const selectedClient = this.clients.find(client => client.clientNetworkId === this.selected);
                    if (selectedClient) {
                        const { clientGroupId, clientNetworkId } = selectedClient;
                        this.currentPage = 1;
                        this.getUsers();
                    }
                }
            },
            deleteItem(item) {
                // console.log('Delete user '+ item.id);
                let deleteUser = confirm("Are you sure you want to delete this user?");
                if (deleteUser) {
                    const userData = {
                        userId: item.id
                    };
                    axios.post('/api/v1/deleteUserApi', userData).then(res=>{
                        location.reload();
                    })
                    .catch((error) => console.log(error));
                }
            },
            editItem(item){
                // console.log(item.id);
                let url = "/userdetails?detail="+item.id;
                window.location.href = url;
            },
            addNewUser(){
                const networkData = {
                    clientNetworkId: this.detail,
                };
                axios.post('/api/v1/userAddApi', networkData).then(res=>{
                    // console.log(res.data);
                    //res.data is the id of the inserted new user
                    let url = "/userdetails?detail="+res.data;
                    window.location.href = url;
                })
                .catch((error) => console.log(error));
            }
          
      }

    }
</script>
