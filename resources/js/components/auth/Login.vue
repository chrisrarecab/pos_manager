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
                                                <input class="form-control" v-model="domain" ref="domain" type="text" placeholder="domain">
                                            </div>
                                            <div class="form-group">
                                                <input class="form-control" v-model="username" ref="usernameCirms" type="text" placeholder="username">
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
                domain: '',
                errorMessage: '',
                sourceProject: 1,
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
            checkDomainFormat(domain) {
                return (/^[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,6}$/i.test(domain));
            },
            loginCore() {
                this.errors = [];
                this.sourceProject = 1;
                this.loginValidation(this);
            },
            loginCirms() {
                this.errors = [];
                if (this.domain === '') {
                    this.errors.push("Domain is required");
                }
                else if (!this.checkDomainFormat(this.domain)) {
                    this.errors.push("Invalid domain format");
                }
                this.sourceProject = 2;
                this.loginValidation(this);
            },
            loginValidation(self) {
                if (self.isLoading) {
                    return;
                }
                self.isLoading = true;
                self.alertMessage = false;

                if (self.username === '') {
                    self.errors.push("Username is required");
                }
                if (self.password === '') {
                    self.errors.push("Password is required");
                }
                if (self.errors.length > 0) {
                    self.alertMessage = true;
                    self.isLoading = false;
                    return;
                }
                self.loadingSpinner = true;

                axios.post('api/login', {
                    username: self.username,
                    password: self.password,
                    domain: self.domain,
                    project: self.sourceProject,
                }).then((response) => {
                    console.log(response);
                    self.loadingSpinner = false;
                    if (response.status == 200) {
                        alert('Login successfully!');
                        self.$router.push('/dashboard').then(()=> {self.$router.go(0)});
                    }
                    self.isLoading = false;
                }).catch((error) => {
                    self.loadingSpinner = false;
                    console.log(error);
                    if (typeof error.response.data.message !== 'undefined') {
                        self.errors.push(error.response.data.message);
                        self.alertMessage = true;
                    }
                    if (typeof error.response.data.error !== 'undefined') {
                        self.errors.push(error.response.data.error);
                        self.alertMessage = true;
                    }
                    self.isLoading = false;
                })
            }
        }
    }
</script>
