<template>
    <div class="container-fluid mb-1rem">
        <div class="row bg-light home-row">
            <div class="col-12" ref="biographical_information">
                <div class="profile-details">
                    <h4 class="text-center mt-half-rem" ref="name">{{ jobName }}</h4>

                    <nav class="flex justify-content-around header-border">
                        <section class="flex-1" @click="settings()">
                            <!--                            <img class="float-left img" src="/img/edit.svg" alt="">-->
                            <label>Edit Profile</label>
                        </section>
                        <section class="flex-1" @click="logout()">
                            <!--                            <img id="logout" class="float-left img" src="/img/Logout.svg" alt="">-->
                            <label for="logout">Log Out</label>
                        </section>

                    </nav>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    export default {
        name: 'HeaderJobNameSettingsLogout',
        props: {
            jobName: String
        },
        methods: {
            logout() {
                localStorage.setItem('auth', false);
                window.location.href = '/logout';
            },
            settings() {
                // window.location.href = '/settings';
                this.$router.push('/settings')
            }
        }
    }
</script>

<style scoped>


</style>