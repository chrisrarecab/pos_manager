<template></template>
<script>
    export default {
        mounted() {
            this.checkAuth();
        },
        data() {
            return {}
        },
        methods: {
            redirectToLogin() {
                //alert('Session expired. Please refresh the page!');
                window.location.href = "login";
            },
            checkAuth() {
                axios.get('api/checkUserSession', {

                }).then((response) => {
                    if (response.data == false) {
                        this.redirectToLogin();
                    }
                }).catch((error) => {
                    this.redirectToLogin();
                });
            },
        }
    }
</script>