<template>
<b-link :to="{ path: 'userlist', query: { detail: this.network } }" class="lnk">Return To User List</b-link>
<div class="card mx-auto" style="width: 60rem;">
    <div class="card-header">
        <div class="card-title"><h4>Details</h4></div>
    </div>
    <div class="card-body">
        <table class="table">
            <tr>
                <td>Status:</td>
                <td>
                    <b-form-select v-model="status_selected" id="status">
                        <b-form-select-option value=0>Inactive</b-form-select-option>
                        <b-form-select-option value=1>Active</b-form-select-option>
                        <b-form-select-option value=2>Pending</b-form-select-option>
                    </b-form-select>

                    <!-- <div class="mt-2">Selected: <strong>{{ selected }}</strong></div> -->
                </td>
                <td>Username:</td>
                <td><b-form-input v-model="username" placeholder="Enter username" id="username"></b-form-input></td>
            </tr>
            <tr>
                <td>Full Name:</td>
                <td><b-form-input v-model="fullname" placeholder="Enter Full Name" id="fullname"></b-form-input></td>
                <td>Password:</td>
                <td><b-form-input v-model="password" placeholder="Enter Password" type="password" id="password"></b-form-input></td>
            </tr>
            <tr>
                <td>Branch:</td>
                <td colspan="3">
                    <VueMultiselect
                        v-model="taggingSelected"
                        :options="taggingOptions"
                        :multiple="true"
                        :close-on-select="true"
                        placeholder="Select branches"
                        label="name"
                        track-by="id"
                        tag-position="bottom"
                        style="position: relative; right: 10px;"
                    />
                </td>
            </tr>
            
        </table>
        
    </div>
</div>
    <br>
    <div class="card mx-auto" style="width: 60rem;">
        <div class="card-header">
            <div class="card-title">
                <h4>Setting</h4>
            </div>
        </div>
        <div class="card-body text-center">
            <b-form-checkbox-group
                id="setting-group"
                v-model="settings_selected"
                :options="settings_options"
            ></b-form-checkbox-group>
            <!-- <div>Selected: <strong>{{ settings_selected }}</strong></div> -->
        </div>
    </div>
    <br>
    <div class="card mx-auto" style="width: 60rem;">
        <div class="card-header">
            <div class="card-title">
                <h4>User</h4>
            </div>
        </div>
        <div class="card-body text-center">
            <b-form-checkbox-group
                id="user-group"
                v-model="user_selected"
                :options="user_options"
             ></b-form-checkbox-group>
            <!-- <div>Selected: <strong>{{ user_selected }}</strong></div> -->
        </div>
    </div>
    <br>
    <div class="card mx-auto" style="width: 60rem;">
        <div class="card-header">
            <div class="card-title">
                <h4>Audit Trail</h4>
            </div>
        </div>
        <div class="card-body text-center">
            <b-form-checkbox-group
                id="audit-group"
                v-model="audit_selected"
                 :options="audit_options"
            ></b-form-checkbox-group>
            <!-- <div>Selected: <strong>{{ audit_selected }}</strong></div> -->
        </div>
    </div>
    <br>
    <div class="text-center">
        <b-button class="userdetail-btn" @click="saveUser">Save</b-button>
    </div>
    <br>
</template>

<script>
import VueMultiselect from 'vue-multiselect';

function getApiBaseUrl() {
        const protocol = window.location.protocol;
        const host = window.location.hostname;
        const port = 83;

        return `${protocol}//${host}:${port}/api/v1`;
    }
const apiUrl = getApiBaseUrl(); 

export default {
    components: {
        VueMultiselect
        
    },
    data() {
      return {
        user: '',
        network: '',
        branch: '',
        hidePass: true,
        status_selected: null,
        username: '',
        fullname: '',
        password: '',
        taggingSelected: [
            
        ],
        taggingOptions: [],
        settings_selected: [],
        settings_options: [
          { text: 'View', value: 101 },
          { text: 'Add', value: 102 },
          { text: 'Edit', value: 103 },
          { text: 'Delete', value: 104 }
        ],
        user_selected: [],
        user_options: [
          { text: 'View', value: 201 },
          { text: 'Add', value: 202 },
          { text: 'Edit', value: 203 },
          { text: 'Delete', value: 204 },
          { text: 'Approve', value: 205 }
        ],
        audit_selected: [],
        audit_options: [
          { text: 'View', value: 301 },
          { text: 'Add', value: 302 },
          { text: 'Edit', value: 303 },
          { text: 'Delete', value: 304 }
        ],
        error_username: '',
        error_fullname: '',
        error_password: ''
      }
    },
    props: {
        detail: {
            type: String,
            required: true
        }
    }, 
    mounted(){
        this.displayDetails();
    },
    methods: {
        displayDetails () {
            // console.log(this.detail);
            axios.get('/api/v1/userDetailsApi',{ params: { detail: this.detail } }).then(res=>{
                // console.log(res);
                this.user = res.data.userDetails[0];
                // console.log(this.user.username);
                this.username = this.user.username;
                this.fullname = this.user.full_name;
                this.password = this.user.password;
                this.status_selected = this.user.status;
                this.network = this.user.client_network_id;

                this.LoadBranches();
                this.getSelectedBranches();
            });  
        },
        getPermissions() {
            axios.get('/api/v1/userPermissionsApi',{ params: { detail: this.detail } }).then(res=>{
                this.permissions = res.data.permissions;
                // console.log(this.permissions);
                
                this.permissions.map(item => {
                    let code = item.code.toString();
                    if (code.charAt(0) == '1') {
                        this.settings_selected.push(item.code);
                    } else if (code.charAt(0) == '2') {
                        this.user_selected.push(item.code);
                    } else if (code.charAt(0) == '3') {
                        this.audit_selected.push(item.code);
                    }
                    
                });
                // console.log(this.permissions_selected);
            });
        },
        LoadBranches() {
            const clientNetworkData = {
                clientNetworkId: this.network
            };

            axios.post(apiUrl + '/TerminalList/GetBranchListByNetwork', clientNetworkData, {
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Basic bmVsc29mdDoxMjE1ODY='  
                    }
                })
                .then((res) => { 
                    this.branch = res.data;
                    // console.log(this.branch);
                    this.taggingOptions = this.branch;

                    this.taggingOptions.push({
                        id: 1,
                        name: "ALL"
                    });
                    this.taggingOptions.sort((a,b) =>a.id - b.id);

                    this.getPermissions(this.detail);
            
                });
        },
        getSelectedBranches () {
            axios.get('/api/v1/userBranchApi',{ params: { detail: this.detail } }).then(res=>{
                this.branch = res.data.branches;
                const selectedIds = this.branch.map(item => item.id);
                // this.taggingSelected = this.branch.map(item =>( {
                //     id: item.id
                //     // ,
                //     // name: item.name
                // }));    
                this.taggingSelected = this.taggingOptions.filter(option => selectedIds.includes(option.id));
                // console.log(this.taggingSelected);         
            });   
        },
        saveUser () {
            // alert(this.full_name);
            
            let username = $('#username').val();
            let fullname = $('#fullname').val();
            let status = $('#status').val();
            let password = $('#password').val();
            // console.log(this.taggingSelected);
            let branches = this.taggingSelected.map(item => item.id);
            let permissions_selected = this.settings_selected.concat(this.user_selected, this.audit_selected);
            console.log(permissions_selected);
            // const permissions = this.permissions_selected.map(item => item.code);
            // console.log(branches);
            // console.log(this.permissions_selected);
            const userData = {
                id: this.detail,
                username: username,
                fullname: fullname,
                status: status,
                password: password
            };

            const branchData = {
                id: this.detail,
                branches: branches
            };

            const permissionData = {
                id: this.detail,
                permissions: permissions_selected
            };

            axios.post('/api/v1/userDetailsEdit', userData)
                .then((res) => { 
                    axios.post('/api/v1/userBranchEdit', branchData)
                    .then((res) => {
                        axios.post('/api/v1/userPermissionEdit', permissionData)
                        .then((res) => {
                            console.log('Permissions Saved!');
                        })
                        .catch((error) => {
                            console.error("Error Saving Permissions: ", error);
                        });
                        console.log('Branches Saved!');
                    })
                    .catch((error) => {
                        console.error("Error Saving Branches: ", error);
                    });
                    console.log('Saved!');
                    alert('Saved!');
                    location.reload();
                })
                .catch((error) => {
                    console.error("Error Saving Data: ", error.response.data.errors);
                    this.error_fullname = error.response.data.errors.fullname;
                    // if(this.error_fullname != ''){
                    //     $('#fullname').attr('placeholder', this.error_fullname);
                    // }
                    this.error_username = error.response.data.errors.username;
                    this.error_password = error.response.data.errors.password;

                    // alert(this.error_fullname +'\n'+ this.error_username +'\n'+ this.error_password);
                });
        }
    }

}
</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>

<style>
td{
    text-align: center;
    /* width: 25% */
}
.td-lbl{
    width: 50%
}
.nowrap {
    white-space:nowrap
}

.card {
    margin: 0 auto; /* Added */
    float: none; /* Added */
}

.userdetail-btn {
    background-color: #41b883;
    border: none;
}

.userdetail-btn:hover {
    background-color:#368f66;
}

.card-header {
    background-color: #383838;
    color: white;
}

.lnk {
    position:relative;
    left: 1000px;
}

.error {
    color: red;
}

/*.card-body{
    background-color: #e0e0e0;
} */
</style>