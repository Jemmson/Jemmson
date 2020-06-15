<template>

    <div class="flex flex-col">
        <v-app-bar
                color="#95ca97"
                fixed
                height="62px"
                class="mb-1rem"
        >
            <img src="img/premiumlogo/onlinelogomaker-031420-0923-4133-2000-transparent.png"
                 style="max-height:50px"
            >
            <v-spacer></v-spacer>

            <div>{{ name }}</div>

            <v-spacer></v-spacer>

            <div class="flex flex-col nav-icon-spacing">
                <v-btn
                        icon
                        @click="openDialog()"
                >
                    <v-icon>mdi-login</v-icon>
                </v-btn>
                <div class="nav-icon-label" style="margin-top: -.5rem; margin-bottom: .5rem">
                    Logout
                </div>
            </div>

            <div class="flex flex-col nav-icon-spacing">
                <v-btn
                        @click="settings()"
                        icon
                >
                    <v-icon>mdi-settings</v-icon>
                </v-btn>
                <div class="nav-icon-label" style="margin-top: -.5rem; margin-bottom: .5rem">
                    Settings
                </div>
            </div>


            <v-dialog
                    v-model="logoutDialog"
                    width="500"
            >
                <v-card>
                    <v-card-title class="w-break uppercase">Do you wish to logout?</v-card-title>
                    <v-card-actions>
                        <v-btn
                                id="cancel"
                                ref="cancel"
                                @click="cancelDialog()"
                                color="red"
                                text="">CANCEL
                        </v-btn>
                        <v-spacer></v-spacer>
                        <v-btn
                                id="logout"
                                ref="logout"
                                @click="logout()"
                                color="primary"
                                text="">LOGOUT
                        </v-btn>
                    </v-card-actions>
                </v-card>

            </v-dialog>


        </v-app-bar>

        <v-tabs class="justify-center" style="margin-top: 3.5rem;">
            <v-tab
                    style="margin-left: auto; margin-right: auto;"
                    class="w-full"
                   @click.prevent="toggleBidsContractor(true)"
            >General Jobs</v-tab>
            <v-tab
                    style="margin-left: auto; margin-right: auto;"
                    class="w-full"
                   @click.prevent="toggleBidsContractor(false)"
            >Sub Jobs</v-tab>
        </v-tabs>

<!--        <div class="container-fluid" style="margin-top: 40px">-->
<!--            <div ref="job_toggle"-->
<!--                 class="row bg-white bids-row spacing">-->
<!--                <div ref="toggle_contractors" @click.prevent="toggleBidsContractor(true)"-->
<!--                     class="col pr-0 pl-0 text-center text-uppercase align-self-end"-->
<!--                     :class="bidsContractorSectionPicked ? 'border-bottom border-primary' : ''">-->
<!--                    <p class="bids-toggle text-primary f-size-13pt text-center">-->
<!--                        General Jobs-->
<!--                    </p>-->
<!--                </div>-->
<!--                <div ref="toggle_subContractors" @click.prevent="toggleBidsContractor(false)"-->
<!--                     class="col pr-0 pl-0 text-center text-uppercase align-self-end"-->
<!--                     :class="!bidsContractorSectionPicked ? 'border-bottom border-primary' : ''">-->
<!--                    <p class="bids-toggle text-primary f-size-13pt text-center">-->
<!--                        Sub Jobs-->
<!--                    </p>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
    </div>

</template>

<script>
    import {mapState, mapMutations} from 'vuex'

    export default {
        name: 'HeaderJobToggle',
        props: {
            name: String
        },
        data() {
            return {
                logoutDialog: false
            }
        },
        computed: {
            ...mapState({
                bidsContractorSectionPicked: state => state.bidsContractorSectionPicked,
            })
        },
        methods: {
            ...mapMutations([
                'toggleBidsContractor'
            ]),
            openDialog() {
                this.logoutDialog = true
            },
            cancelDialog() {
                this.logoutDialog = false
            },
            logout(){
                localStorage.setItem('auth', false);
                window.location.href = '/logout';
            },
            settings(){
                // window.location.href = '/settings';
                this.$router.push('/settings')
            }
        }
    }
</script>

<style scoped>
    .spacing {
        height: 54px;
        padding-bottom: 4px;
    }
</style>