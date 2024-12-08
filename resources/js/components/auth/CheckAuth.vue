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
            checkAuth() {
                let self = this;
                axios.get('api/checkUserSession', {}).then((response) => {
                    let currentPageName = self.$router.currentRoute.value.name;
                    if (response.data == false) {
                        if (currentPageName != 'login') {
                            self.$router.push('/login?expired=1').then(()=> {self.$router.go(0)});
                        }
                    }
                    else {
                        if (currentPageName != 'dashboard') {
                            self.$router.push('/dashboard').then(()=> {self.$router.go(0)});
                        }
                    }
                }).catch((error) => {
                    
                });
            },
        }
    }
</script>