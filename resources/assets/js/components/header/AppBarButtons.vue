<template>
    <div>
        <div class="flex space-between"
             v-if="!auth"
        >
            <v-btn
                    @click="gotoPage('/')"
                    icon
            >
                <v-icon>mdi-home-outline</v-icon>
            </v-btn>

            <v-btn
                    @click="emit('features')"
                    icon
            >
                <v-icon>mdi-earth</v-icon>
            </v-btn>

            <v-btn
                    @click="emit('pricing')"
                    icon
            >
                <v-icon>mdi-account-cash</v-icon>
            </v-btn>

            <v-btn
                    @click="emit('documentation')"
                    icon
            >
                <v-icon>mdi-file-document</v-icon>
            </v-btn>

            <v-btn
                    icon
                    @click="openDialog()"
            >
                <v-icon>mdi-login</v-icon>
            </v-btn>

            <v-dialog
                    v-model="logoutDialog"
                    width="500"
            >
                <v-card>
                    <v-card-title class="uppercase">Do you wish to logout?</v-card-title>
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
        </div>
        <div
                v-if="auth"
        >
            <v-btn
                    @click="gotoPage('/home')"
                    icon
            >
                <v-icon>mdi-home-outline</v-icon>
            </v-btn>

            <v-btn
                    @click="gotoSettings()"
                    icon
            >
                <v-icon>mdi-settings</v-icon>
            </v-btn>
        </div>
    </div>
</template>

<script>

    import {mapState} from 'vuex'

    export default {
        name: 'AppBarButtons',
        data() {
            return {
                logoutDialog: false
            }
        },
        computed: {
            ...mapState({
                auth: state => state.auth
            })
        },
        methods: {
            goHome() {
                if (this.auth) {
                    this.$router.push('/home')
                } else {
                    this.$router.push('/')
                }
            },
            openDialog() {
                this.logoutDialog = true
            },
            cancelDialog() {
                this.logoutDialog = false
            },
            gotoSettings() {
                window.location.href = '/settings'
            },
            gotoPage(page) {
                this.$router.push(page)
            },
            emit(btn) {
                this.$emit('appBtn', btn)
            },
            showContent(btn) {
                this.emit(btn)
            }
        }
    }
</script>

<style scoped>

</style>