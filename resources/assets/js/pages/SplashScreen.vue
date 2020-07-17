<template>

    <div style="background-color: #007bffc7; height: 100vh">
        <h1 class="mt-10 text-center">
            Welcome To Jemmson</h1>

        <div
             class="flex flex-col justify-center align-center"
        >

            <v-btn
                    @click="goToHome()"
                    style="margin-top: 10rem"
            >
                <img src="img/premiumlogo/onlinelogomaker-031420-0923-4133-2000-transparent.png"
                     style="max-height:50px"
                >
            </v-btn>

        </div>
    </div>

</template>

<script>
    export default {
        name: "SplashScreen",
        methods:{
            goToHome(){
                this.$router.push('/home')
            }
        },
        mounted(){
            this.$store.commit('setCurrentPage', '/splash-screen');
        }
    }
</script>

<style scoped>

</style>