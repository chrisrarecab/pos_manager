<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="login-main-panel">
                    <div class="loading-progress">
                        <div v-show="loadingSpinner" class="text-center">
                            <b-spinner variant="secondary" type="grow" small label="Spinning"></b-spinner>&nbsp;
                            <b-spinner variant="secondary" type="grow" small label="Spinning"></b-spinner>&nbsp;
                            <b-spinner variant="secondary" type="grow" small label="Spinning"></b-spinner>&nbsp;
                        </div>
                    </div>
                    <div>
                        <b-card no-body>
                            <b-tabs content-class="mt-3" class="auth-tabs">
                                <b-tab title="Core" active>
                                    <form @submit.prevent="loginCore">
                                        <div class="login-form">
                                            <div class="form-group">
                                                <input class="form-control" v-model="username" ref="username" type="text" placeholder="username">
                                            </div>
                                            <div class="form-group">
                                                <input class="form-control" v-model="password" ref="password" type="password" placeholder="password">
                                            </div>
                                            <div class="form-group text-center">
                                                <b-button class="btn-default" type="submit">Login</b-button>
                                            </div>
                                        </div>
                                    </form>
                                </b-tab>
                                <b-tab title="CIRMS">
                                    <form @submit.prevent="loginCirms">
                                        <div class="login-form">
                                            <div class="form-group">
                                                <input class="form-control" ref="domain" type="text" placeholder="domain" disabled>
                                            </div>
                                            <div class="form-group">
                                                <input class="form-control" ref="usernameCirms" type="text" placeholder="username" disabled>
                                            </div>
                                            <div class="form-group">
                                                <input class="form-control" ref="password" type="password" placeholder="password" disabled>
                                            </div>
                                            <div class="form-group text-center">
                                                <b-button class="btn-default" disabled>Login</b-button>
                                            </div>
                                        </div>
                                    </form>
                                </b-tab>
                            </b-tabs>
                        </b-card>
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
                </div>
            </div>
        </div>
    </div>
</template>
<script>

    export default {
        mounted(){
            this.initialized();
        },
        data() {
            return {
                errors: [],
                username: '',
                password: '',
                errorMessage: '',
                alertMessage: false,
                loadingSpinner: false,
                isLoading: false,
                params: '',
            }
        },
        methods: {
            initialized() {
                this.$refs.username.focus();
            },
            loginCore() {
                if (this.isLoading) {
                    return;
                }
                this.isLoading = true;
                this.errors = [];
                this.alertMessage = false;
                if (!this.username) {
                    this.errors.push("Username is required");
                }
                if (!this.password) {
                    this.errors.push("Password is required");
                }
                if (this.errors.length > 0) {
                    this.alertMessage = true;
                    return;
                }
                this.loadingSpinner = true;
                let self = this;
                axios.post('api/login', {
                    username: this.username,
                    password: this.password,
                }).then((response) => {
                    console.log(response);
                    this.loadingSpinner = false;
                    if (response.status == 200) {
                        alert('Login successfully!');
                        self.$router.push('/dashboard').then(()=> {this.$router.go(0)});
                    }
                    isLoading = false;
                }).catch((error) => {
                    this.loadingSpinner = false;
                    console.log(error);
                    if (typeof error.response.data.message !== 'undefined') {
                        this.errors.push(error.response.data.message);
                        this.alertMessage = true;
                    }
                    if (typeof error.response.data.error !== 'undefined') {
                        this.errors.push(error.response.data.error);
                        this.alertMessage = true;
                    }
                    this.isLoading = false;
                })
            }
        }
    }
</script>
