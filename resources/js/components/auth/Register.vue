<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="loading-progress">
                    <div v-show="loadingSpinner" class="text-center">
                        <b-spinner variant="secondary" type="grow" small label="Spinning"></b-spinner>&nbsp;
                        <b-spinner variant="secondary" type="grow" small label="Spinning"></b-spinner>&nbsp;
                        <b-spinner variant="secondary" type="grow" small label="Spinning"></b-spinner>&nbsp;
                    </div>
                </div>
                <form @submit.prevent="register">
                    <div class="section-body">
                        <table class="center">
                            <tbody>
                                <tr>
                                    <td colspan="2"><label>Secret Key:</label></td>
                                    <td colspan="2"><input v-model="secretkey" ref="register" type="text" class="form-control"></td>
                                </tr>
                                <tr>
                                    <td colspan="2"><label>Username:</label></td>
                                    <td colspan="2"><input v-model="username" type="text" class="form-control"></td>
                                </tr>
                                <tr>
                                    <td colspan="2"><label>Fullname:</label></td>
                                    <td colspan="2"><input v-model="fullname" type="text" class="form-control"></td>
                                </tr>
                                <tr>
                                    <td colspan="2"><label>Password:</label></td>
                                    <td colspan="2"><input v-model="password" type="password" class="form-control"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="section-footer text-center">
                        <b-button class="btn-default" type="submit">Register</b-button>
                    </div>
                    <div class="response-message">
                        <div>
                            <b-alert v-model="alertMessage" variant="danger" dismissible>
                                <div v-if="errors.length" class="alert-message">
                                    <p class="text-no-margin text-center" v-for="error in errors">{{ error }}</p>
                                </div>
                            </b-alert>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        mounted(){
            this.focusInput();
            this.checkSession();
        },
        data() {
            return {
                secretkey: '',
                username: '',
                fullname: '',
                password: '',
                alertMessage: false,
                loadingSpinner: false,
                errors: [],
            }
        },
        methods: {
            focusInput() {
                this.$refs.register.focus();
            },
            checkSession() {
                axios.get('api/checkUserSession', {

                }).then((response) => {
                    if (response.data == true) {
                        window.location.href = "sample";
                    }
                }).catch((error) => {
                    console.log(error);
                });
            },
            register() {
                let self = this;
                this.errors = [];
                this.alertMessage = false;
                this.loadingSpinner = true;
                
                axios.post('api/register', {
                    secretkey: this.secretkey,
                    username: this.username,
                    fullname: this.fullname,
                    password: this.password,
                }).then((response) => {
                    console.log(response);
                    if (response.status == 200) {
                        alert('Registered successfully!');
                        //self.$router.push('/login').then(()=> {this.$router.go(0)});
                        window.location.href = "http://localhost:99/login";
                    }
                }).catch((error) => {
                    console.log(error);
                    if (typeof error.response.data.message !== 'undefined') {
                        this.errors.push(error.response.data.message);
                        this.alertMessage = true;
                        this.loadingSpinner = false;
                    }
                })
            }
        }
    }
</script>
