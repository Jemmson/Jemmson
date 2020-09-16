<template>
    <nav>

        <!--        <app-bar></app-bar>-->

        <section v-if="onHomePage()
                || onCustomerInfo ()
                || onAssociatedContractorsPage ()
                || onContractorInfo ()
                || onSettingsPage ()
                || onAssessorPage ()
                || onSplashScreen ()
                || onHelpPage ()
                || onFurtherInfoPage ()
        ">
            <header-bio
                    ref="homePage"
                    :name="user.first_name + ' ' + user.last_name"
            ></header-bio>
        </section>

        <section v-else-if="onParticularJobPage()">

            <header-bio
                    ref="homePage4"
                    :name="user.first_name + ' ' + user.last_name"
            ></header-bio>

        </section>

        <section v-else-if="onInvoicesPage() ||
                            onInvoicePage() ||
                        onParticularInvoicePage() ||
                        onImagesPage()">

            <!--            onSettingsPage() ||-->

            <header-bio
                    ref="homePage1"
                    :name="user.first_name + ' ' + user.last_name"
            ></header-bio>
        </section>

        <section v-else-if="(onTasksPage() || onJobsPage())">

            <div v-if="!isCustomer()">
                <header-job-toggle
                        ref="headerJobToggle"
                        :name="user.first_name + ' ' + user.last_name"
                ></header-job-toggle>
            </div>

            <div v-else>
                <header-bio
                        ref="homePage2"
                        :name="user.first_name + ' ' + user.last_name"
                ></header-bio>
            </div>

        </section>

        <section v-else-if="(
          onAddTaskPage() || onAddSubPage()
          ) && !isCustomer()">
            <header-back-button
                    ref="headerBackButton"
            ></header-back-button>

        </section>

        <section v-else-if="(
            onInitiateBidPage()
            || onImageAssociationPage())
            && !isCustomer()"
        >
            <header-bio
                    ref="homePage"
                    :name="user.first_name + ' ' + user.last_name"
            ></header-bio>
        </section>

    </nav>
</template>

<script>
    import {mapState} from 'vuex'
    import HeaderBio from '../header/HeaderBio'
    import HeaderBackButton from '../header/HeaderBackButton'
    import HeaderJobNameSettingsLogout from '../header/HeaderJobNameSettingsLogout'
    import HeaderJobToggle from '../header/HeaderJobToggle'
    import AppBar from '../header/AppBar'

    export default {
        name: 'Header',
        components: {
            AppBar,
            HeaderBio,
            HeaderBackButton,
            HeaderJobNameSettingsLogout,
            HeaderJobToggle
        },
        props: ['user'],
        data() {
            return {
                drawer: false,
                currentUser: ''
            }
        },
        computed: {
            ...mapState({
                page: state => state.page,
                // userFromState: state => state.user.user,
                // userType: (state) => {
                //   if (state.user.user !== undefined && state.user.user !== null) {
                //     return state.user.user.usertype
                //   }
                //   return null
                // },
                // bidsContractorSectionPicked: state => state.bidsContractorSectionPicked,
            }),
            getCompanyName() {
                if (this.user.contractor) {
                    return this.user.contractor.company_name
                }
            }
        },
        methods: {
            onHomePage() {
                return this.page === '/home' || this.page === '/home/'
            },
            onCustomerInfo() {
                return this.page === '/customer-info' || this.page === '/customer-info/'
            },
            onAssociatedContractorsPage() {
                return this.page === '/associatedContractors'
            },
            onContractorInfo() {
                return this.page === '/contractor-info' || this.page === '/contractor-info/'
            },
            onJobsPage() {
                return this.page === '/bids' || this.page === '/bids/' || this.page === '/bids/subs'
            },
            onParticularJobPage() {
                return this.page.split('/')[1] === 'bid'
            },
            onJobTaskPage() {
                return this.page === '/job/tasks' || this.page === '/job/tasks/'
            },
            onTasksPage() {
                return this.page === '/tasks' || this.page === '/tasks/'
            },
            onInvoicesPage() {
                return this.page === '/invoices' || this.page === '/invoices/'
            },
            onInvoicePage() {
                return this.page === '/invoice' || this.page === '/invoice/'
            },
            onParticularInvoicePage() {
                return this.page.split('/')[1] === 'invoice' ||
                    this.page.split('/')[2] === 'invoice'
            },
            onSettingsPage() {
                return this.page === '/settings' || this.page === '/settings/'
            },
            onAssessorPage() {
                return this.page === '/assessor' || this.page === '/assessor/'
            },
            onSplashScreen() {
                return this.page === '/splash-screen' || this.page === '/splash-screen/'
            },
            onHelpPage() {
                return this.page === '/help' || this.page === '/help/'
            },
            onAddTaskPage() {
                return this.page === '/job/add/task' || this.page === '/job/add/task/'
            },
            onImageAssociationPage() {
                return this.page === '/image-association'
            },
            onAddSubPage() {
                return false
                // return this.page === '/home' || this.page === '/home/'
            },
            onInitiateBidPage() {
                return this.page === '/initiate-bid' || this.page === '/initiate-bid/'
            },
            onRegisterPage() {
                return this.page === '/register' || this.page === '/register/'
            },
            onFurtherInfoPage() {
                return this.page === '/furtherInfo' || this.page === '/furtherInfo/'
            },
            onRegisterQuickbooksPage() {
                return this.page === '/registerQuickBooks' || this.page === '/registerQuickBooks/'
            },
            onImagesPage() {
                return this.page.split('/')[3] === 'images'
            },
            onPublicHomePage() {
                return this.page === '/home' || this.page === '/home/'
            },
            isCustomer() {
                return this.user.usertype === 'customer'
            },
        },
        mounted() {
            if (
                this.user.user !== undefined &&
                this.user.user !== null &&
                this.userFromState !== '') {
                this.$store.commit('setUser', this.user)
                this.currentUser = this.user
            }
        },
    }
</script>

<style lang="less" scoped>
    .home-row {
        height: 152px;
        margin-bottom: 4rem;
    }

    .bids-row {
        height: 112px;
    }

    .bid-row {
        height: 55px;
    }

    .default-row {
        height: 70px;
    }


    .profile-pic {
        margin-top: 57px;
        -webkit-border-radius: 50%;
        -moz-border-radius: 50%;
        border-radius: 50%;
        border: 5px solid rgba(255, 255, 255, 0.5);
    }

    .profile-details {
        margin-top: 52px;
        padding-left: 8rem;
    }

    p {
        font-size: 12px;
        font-weight: 600;
        margin-top: -3px;
        color: #ffffff;
        padding-left: 1rem;
    }

    h4 {
        font-size: 23px;
        color: #ffffff;
        font-weight: 600;
    }

    .bids-toggle {
        font-size: 12px;
    }

</style>

