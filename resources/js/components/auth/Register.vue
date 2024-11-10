<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
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
                    <div class="text-center">
                        <span v-html="errorMessage" class="page-register text-error"></span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
<script>
    //import { createRouter, createWebHashHistory } from 'vue-router'
    export default {
        mounted(){
            this.focusInput();
        },
        data() {
            return {
                secretkey: '',
                username: '',
                fullname: '',
                password: '',
                errorMessage: '',
            }
        },
        methods: {
            focusInput() {
                this.$refs.register.focus();
            },
            register() {
                let self = this;
                axios.post('api/register', {
                    secretkey: this.secretkey,
                    username: this.username,
                    fullname: this.fullname,
                    password: this.password,
                }).then((response) => {
                    console.log(response);
                    if (response.status == 200) {
                        alert('Registered successfully!');
                        window.location.href = "http://localhost:99/login";
                    }
                }).catch((error) => {
                    console.log(error.response.data.message);
                    this.errorMessage = error.response.data.message;
                })
            }
        }
    }
</script>
