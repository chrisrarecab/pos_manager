<template>
<div class="card" style="width:60rem; border:none;">
    <b-link :to="{ path: 'userlist', query: { detail: this.network } }" class="d-flex justify-content-end">Return To User List</b-link>
</div>

<div class="card mx-auto" style="width: 60rem;">
    <div class="card-header">
        <!-- <div class="card-title"> -->
            <h7 class="my-0">Details</h7>
        <!-- </div> -->
    </div>
    <div class="card-body">
        <table class="table">
            <tbody>
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
            </tbody>
        </table>
        
    </div>
</div>
    <br>
    <div class="card mx-auto" style="width: 60rem;">
        <div class="card-header my-0">
            <!-- <div class="card-title"> -->
            <b-form-checkbox
            v-model="allSelected"
            :indeterminate="indeterminate"
            @change="toggleAll"
            >
                <h7 class="my-0">Setting</h7>
            </b-form-checkbox>
            
            <!-- </div> -->
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
                <h7 class="my-0">User</h7>
            <!-- <div class="card-title"> -->
                <!-- <h7 class="my-0">User</h7> -->
            <!-- </div> -->
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
            <!-- <div class="card-title"> -->
                <h7 class="my-0">Audit Trail</h7>
            <!-- </div> -->
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
        <b-button class="userdetail-btn" @click="validateUser">Save</b-button>
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
        taggingSelected: [],
        taggingOptions: [],
        permissions_selected: [],
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
        allSelected: false,
        indeterminate: false
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
    watch: {
      settings_selected(newValue, oldValue) {
        if (newValue.length === 0) {
          this.indeterminate = false
          this.allSelected = false
        } else if (newValue.length === this.settings_options.length) {
          this.indeterminate = false
          this.allSelected = true
        } else {
          this.indeterminate = true
          this.allSelected = false
        }
      },
      user_selected(newValue, oldValue) {
        if (newValue.length === 0) {
          this.indeterminate = false
          this.allSelected = false
        } else if (newValue.length === this.user_options.length) {
          this.indeterminate = false
          this.allSelected = true
        } else {
          this.indeterminate = true
          this.allSelected = false
        }
      },
      audit_selected(newValue, oldValue) {
        if (newValue.length === 0) {
          this.indeterminate = false
          this.allSelected = false
        } else if (newValue.length === this.audit_options.length) {
          this.indeterminate = false
          this.allSelected = true
        } else {
          this.indeterminate = true
          this.allSelected = false
        }
      },
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
                // alert(this.password);

                this.LoadBranches();
                this.getSelectedBranches();
            });  
        },
        toggleAll(checked) {
            // Store only the `value` of the flavours in the selected array
            this.settings_selected = checked ? this.settings_options.map(f => f.value) : [];
            this.user_selected = checked ? this.user_options.map(f => f.value) : [];
            this.audit_selected =  checked ? this.audit_options.map(f => f.value) : [];
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
                    // this.permissions_selected.push(item.code);
                });
                if(this.settings_selected.length == 4 && this.user_selected == 5 && this.audit_selected == 4){
                    this.allSelected = true;
                    // this.indeterminate = true;
                }
                // console.log(this.permissions_selected);
            });
        },
        LoadBranches() {
            const clientNetworkData = {
                clientNetworkId: this.network
            };

            axios.post(apiUrl + '/UserList/GetBranchListByNetwork', clientNetworkData, {
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
        async validateUser() {
            let username = $('#username').val();
            let fullname = $('#fullname').val();
            let errors = [];
            let error_message = "";
            console.log(this.taggingSelected);
            // let password = $('#password').val();

            if (fullname == '') {
                errors.push('Full Name should not be empty!');
            }
            if (username == '') {
                errors.push('Username should not be empty!');
            }
            await axios.get('/api/v1/checkUsernameApi',{ params: { id: this.detail,  username: username} }).then(res=>{
                // console.log(typeof(res.data));
                // alert(res.data);
                if(res.data == 1){
                    errors.push('Username already exists!');
                }
                // alert('errors: '+errors.length);
            });
            if (this.taggingSelected.length == 0){
                errors.push('Select at least 1 branch!');
            }
            if (this.user_selected.length == 0 && this.settings_selected == 0 && this.audit_selected == 0) {
                errors.push('Select at least 1 permission!');
            }

            
            // alert('errors outside: '+ errors.length);
            // if (password == '') {
            //     this.error_password = 'Password should not be empty!';
            // }
            if(errors.length > 0) {
                let i = 0;

                while (i < errors.length) {
                    error_message += errors[i] + "\n";
                    i++;
                }
                alert(error_message);
                // location.reload();
            } else {
                this.saveUser();
            }

        },
        saveUser () {
            
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
                // .catch((error) => {
                //     console.error("Error Saving Data: ", error.response.data.errors);
                //     this.error_fullname = error.response.data.errors.fullname;
                //     // if(this.error_fullname != ''){
                //     //     $('#fullname').attr('placeholder', this.error_fullname);
                //     // }
                //     this.error_username = error.response.data.errors.username;
                //     this.error_password = error.response.data.errors.password;

                //     // alert(this.error_fullname +'\n'+ this.error_username +'\n'+ this.error_password);
                // });
        }
    }

}
</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>

<style>
td{
    text-align: center;
}
.td-lbl{
    width: 50%
}
.nowrap {
    white-space:nowrap
}

.card {
    margin: 0 auto;
    float: none;
}

.userdetail-btn {
    background-color: #41b883;
    border: none;
}

.userdetail-btn:hover {
    background-color:#368f66;
}

.userdetail-btn:focus {
    background-color: #41b883;
    /* border: none; */
}

.card-header {
    background-color: #383838;
    color: white;
}

.error {
    color: red;
}

/*.card-body{
    background-color: #e0e0e0;
} */
</style>