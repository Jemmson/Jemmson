 <template>
    <v-container>

<!--        <pre>{{ user }}</pre>-->

        <h2 class="text-center uppercase black--text" style="margin-bottom: 2rem;">Settings</h2>
        <v-card>
            <v-card-actions
                    class="flex flex-col"
            >
                <div class="flex justify-content-around w-full">
                    <div class="flex flex-col">
                        <v-icon
                                class="nav-btn-position"
                                :color="show.profile ? 'success': ''"
                                @click="showSection('profile')"
                        >mdi-face-profile
                        </v-icon>
                        <div class="nav-icon-label" :class="show.profile ? 'nav-icon-label-selected': ''">
                            Profile
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <v-icon
                                :color="show.changePassword ? 'success': ''"
                                class="nav-btn-position"
                                @click="showSection('changePassword')"
                        >mdi-onepassword
                        </v-icon>
                        <div class="nav-icon-label" :class="show.changePassword ? 'nav-icon-label-selected': ''">
                            Password
                        </div>
                    </div>
                    <div class="flex flex-col" v-if="isContractor()">
                        <v-icon
                                :color="show.subscription ? 'success': ''"
                                class="nav-btn-position"
                                @click="showSection('subscription')"
                        >mdi-youtube-subscription
                        </v-icon>
                        <div v-if="isContractor()" class="nav-icon-label" :class="show.subscription ? 'nav-icon-label-selected': ''">
                            Subscription
                        </div>
                    </div>
<!--                    <v-btn-->
<!--                            class="nav-btn-position"-->
<!--                            @click="showSection('manageCreditCard')"-->
<!--                    >Credit Card-->
<!--                    </v-btn>-->
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
                v-show="show.changePassword"
        >
            <change-password></change-password>
        </section>

        <section
                v-if="isContractor()"
                v-show="show.subscription"
        >
            <subscription></subscription>
        </section>


<!--        <section-->
<!--                v-show="show.manageCreditCard"-->
<!--        >-->
<!--            <manage-credit-card></manage-credit-card>-->
<!--        </section>-->

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
                    profile: true,
                    changePassword: false,
                    manageCreditCard: false,
                    subscription: false,
                },
                user: this.$attrs.user
            }
        },
        computed: {

        },
        methods: {
            isContractor(){
                return Spark.state.user.usertype === 'contractor'
            },
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