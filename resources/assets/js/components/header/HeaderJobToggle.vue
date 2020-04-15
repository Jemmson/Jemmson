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

            <v-btn
                    icon
                    @click="logout()"
            >
                <v-icon>mdi-login</v-icon>
            </v-btn>

            <v-btn
                    @click="settings()"
                    icon
            >
                <v-icon>mdi-settings</v-icon>
            </v-btn>

        </v-app-bar>

        <v-tabs class="justify-content-between" style="margin-top: 3.5rem;">
            <v-tab class="w-full"
                   @click.prevent="toggleBidsContractor(true)"
            >General Jobs</v-tab>
            <v-tab class="w-full"
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
        computed: {
            ...mapState({
                bidsContractorSectionPicked: state => state.bidsContractorSectionPicked,
            })
        },
        methods: {
            ...mapMutations([
                'toggleBidsContractor'
            ]),
            logout(){
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