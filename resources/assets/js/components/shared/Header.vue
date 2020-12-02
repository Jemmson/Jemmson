<template>
  <nav>

    <!--        <app-bar></app-bar>-->

    <section v-if="onHomePage
                || onCustomerInfo
                || onAssociatedContractorsPage
                || onContractorInfo
                || onSettingsPage
                || onAssessorPage
                || onJobTaskPage
                || onSplashScreen
                || onHelpPage
                || onFurtherInfoPage
                || onImageAssociationPage
        ">
      <header-bio
          ref="homePage"
          :page-title="pageTitle"
          :name="user.first_name + ' ' + user.last_name"
      ></header-bio>
    </section>

    <section v-else-if="onParticularJobPage">

      <header-bio
          ref="homePage4"
          :page-title="pageTitle"
          :name="user.first_name + ' ' + user.last_name"
      ></header-bio>

    </section>

    <section v-else-if="onInvoicesPage ||
                            onInvoicePage ||
                        onParticularInvoicePage ||
                        onImagesPage">

      <!--            onSettingsPage ||-->

      <header-bio
          ref="homePage1"
          :page-title="pageTitle"
          :name="user.first_name + ' ' + user.last_name"
      ></header-bio>
    </section>

    <section v-else-if="(onTasksPage || onJobsPage)">

      <div v-if="!isCustomer">
        <header-job-toggle
            ref="headerJobToggle"
            :name="user.first_name + ' ' + user.last_name"
        ></header-job-toggle>
      </div>

      <div v-else>
        <header-bio
            :page-title="pageTitle"
            ref="homePage2"
            :name="user.first_name + ' ' + user.last_name"
        ></header-bio>
      </div>

    </section>

    <section v-else-if="(
          onAddTaskPage || onAddSubPage
          ) && !isCustomer">
      <header-back-button
          ref="headerBackButton"
      ></header-back-button>

    </section>

    <section v-else-if="(
            onInitiateBidPage
            )
            && !isCustomer"
    >
      <header-bio
          ref="homePage"
          page-title="Initiate Bid"
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
      pageTitle: '',
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
    },
    onHomePage() {
      this.pageTitle = 'Home'
      return this.page === '/home' || this.page === '/home/'
    },
    onCustomerInfo() {
      this.pageTitle = 'Customer Info'
      return this.page === '/customer-info' || this.page === '/customer-info/'
    },
    onAssociatedContractorsPage() {
      this.pageTitle = 'Subs'
      return this.page === '/associatedContractors'
    },
    onContractorInfo() {
      this.pageTitle = 'Contractor Info'
      return this.page === '/contractor-info' || this.page === '/contractor-info/'
    },
    onJobsPage() {
      this.pageTitle = 'Jobs'
      return this.page === '/bids' || this.page === '/bids/' || this.page === '/bids/subs'
    },
    onParticularJobPage() {
      this.pageTitle = 'Job'
      return this.page.split('/')[1] === 'bid'
    },
    onJobTaskPage() {
      this.pageTitle = 'Job Task'
      return this.page === '/job/task' || this.page === '/job/tasks/'
    },
    onTasksPage() {
      this.pageTitle = 'Tasks'
      return this.page === '/tasks' || this.page === '/tasks/'
    },
    onInvoicesPage() {
      this.pageTitle = 'Receipts'
      return this.page === '/invoices' || this.page === '/invoices/'
    },
    onInvoicePage() {
      this.pageTitle = 'Receipt'
      return this.page === '/invoice' || this.page === '/invoice/'
    },
    onParticularInvoicePage() {
      this.pageTitle = 'Receipt'
      return this.page.split('/')[1] === 'invoice' ||
          this.page.split('/')[2] === 'invoice'
    },
    onSettingsPage() {
      this.pageTitle = 'Settings'
      return this.page === '/settings' || this.page === '/settings/'
    },
    onAssessorPage() {
      this.pageTitle = 'Assessor'
      return this.page === '/assessor' || this.page === '/assessor/'
    },
    onSplashScreen() {
      this.pageTitle = ''
      return this.page === '/splash-screen' || this.page === '/splash-screen/'
    },
    onHelpPage() {
      this.pageTitle = 'Help'
      return this.page === '/help' || this.page === '/help/'
    },
    onAddTaskPage() {
      this.pageTitle = 'Add Task'
      return this.page === '/job/add/task' || this.page === '/job/add/task/'
    },
    onImageAssociationPage() {
      this.pageTitle = 'Images'
      return this.page === '/image-association'
    },
    onAddSubPage() {
      this.pageTitle = 'Add Sub'
      return false
      // return this.page === '/home' || this.page === '/home/'
    },
    onInitiateBidPage() {
      this.pageTitle = 'Initiate Bid'
      return this.page === '/initiate-bid' || this.page === '/initiate-bid/'
    },
    onRegisterPage() {
      this.pageTitle = 'Register'
      return this.page === '/register' || this.page === '/register/'
    },
    onFurtherInfoPage() {
      this.pageTitle = 'Further Info'
      return this.page === '/furtherInfo' || this.page === '/furtherInfo/'
    },
    onRegisterQuickbooksPage() {
      this.pageTitle = 'Quickbooks'
      return this.page === '/registerQuickBooks' || this.page === '/registerQuickBooks/'
    },
    onImagesPage() {
      this.pageTitle = 'Images'
      return this.page.split('/')[3] === 'images'
    },
    onPublicHomePage() {
      this.pageTitle = ''
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

