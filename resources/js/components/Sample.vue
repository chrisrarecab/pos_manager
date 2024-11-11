<template>
    <p class="text-center">User has logged in.</p>
</template>
<script>
    export default {
        mounted() {
            //this.checkAuth();
        },
        data() {
            return {
            }
        },
        created () {
            
        },
        methods: {
            checkAuth() {
                axios.get('api/checkUserSession', {

                }).then((response) => {
                    if (response.status == 200) {
                        console.log(response);
                    }
                }).catch((error) => {
                    console.log(error);
                    if (typeof error.response.data.isSuccessful !== 'undefined') {
                        if (! error.response.data.isSuccessful ) {
                            //window.location.href = "http://localhost:99/login";
                        }
                    }
                });
            },
        }
    }
</script>