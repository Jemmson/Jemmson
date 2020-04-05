<template>
    <v-container>

<!--        <pre>{{ user }}</pre>-->

        <div class="h1 text-center">Settings</div>
        <v-card>
            <v-card-actions
                    class="flex flex-col"
            >
                <div class="flex justify-content-around w-full">
                    <v-btn
                            class="nav-btn-position"
                            @click="showSection('profile')"
                    >Profile
                    </v-btn>
                    <v-btn
                            class="nav-btn-position"
                            @click="showSection('changePassword')"
                    >Change Password
                    </v-btn>
                </div>
                <div class="flex justify-content-around w-full">
                    <v-btn
                            class="nav-btn-position"
                            @click="showSection('subscription')"
                    >Subscription
                    </v-btn>
                    <v-btn
                            class="nav-btn-position"
                            @click="showSection('manageCreditCard')"
                    >Credit Card
                    </v-btn>
                </div>
            </v-card-actions>
        </v-card>

        <section
                v-if="show.profile"
        >
            <profile
                    :user="user"
            >

            </profile>
        </section>

        <section
                v-if="show.changePassword"
        >
            <change-password></change-password>
        </section>

        <section
                v-if="show.subscription"
        >
            <subscription></subscription>
        </section>

        <section
                v-if="show.manageCreditCard"
        >
            <manage-credit-card></manage-credit-card>
        </section>

    </v-container>
</template>

<script>

    import Profile from "./Profile";
    import ChangePassword from "./ChangePassword";
    import Subscription from "./Subscription";
    import ManageCreditCard from "./ManageCreditCard";

    export default {
        name: 'Settings',
        components: {
            Profile,
            ChangePassword,
            ManageCreditCard,
            Subscription
        },
        data() {
            return {
                show: {
                    profile: false,
                    changePassword: false,
                    manageCreditCard: false,
                    subscription: false,
                },
                user: this.$attrs.user
            }
        },
        methods: {
            showSection(section) {
                this.hideAllSections();
                if (section === 'profile') {
                    this.show.profile = true;
                } else if (section === 'changePassword') {
                    this.show.changePassword = true;
                } else if (section === 'subscription') {
                    this.show.subscription = true;
                } else if (section === 'manageCreditCard') {
                    this.show.manageCreditCard = true;
                }
            },

            hideAllSections() {
                this.show.profile = false;
                this.show.changePassword = false;
                this.show.subscription = false;
                this.show.manageCreditCard = false;
            },
        },
        mounted() {
            this.$store.commit('setCurrentPage', '/settings');
        }
    }
</script>

<style scoped>

</style>