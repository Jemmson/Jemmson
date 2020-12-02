<template>
  <v-container>

    <!--    <h2 class="text-center uppercase black&#45;&#45;text" style="margin-bottom: 2rem;">Home Page</h2>-->

    <section>


      <v-card
          ref="contractorNavigationBar"
          v-if="isContractor()">
        <v-card-actions
            class="flex"
        >
          <div class="flex justify-content-around w-full">

            <div class="flex flex-col">
              <v-icon
                  :color="show.details ? 'success': ''"
                  class="nav-btn-position"
                  @click="showSection('details')"
              >mdi-details
              </v-icon>
              <div class="nav-icon-label" :class="show.details ? 'nav-icon-label-selected': ''">
                Details
              </div>
            </div>

            <img
                ref="subsNavButton"
                class="nav-btn-position"
                v-if="hasStripe()"
                @click="showSection('stripe')"
                style="height:1.6rem"
                src="/img/Stripe logo - slate.svg" alt=""
            >

            <div class="flex flex-col">
              <v-icon
                  :color="show.subs ? 'success': ''"
                  class="nav-btn-position"
                  @click="goToContractorsPage()"
              >mdi-face
              </v-icon>
              <div class="nav-icon-label" :class="show.subs ? 'nav-icon-label-selected': ''">
                Subs
              </div>
            </div>

            <div class="flex flex-col">
              <v-icon
                  color="primary"
                  class="nav-btn-position"
                  @click="viewHelp()"
              >mdi-help-circle
              </v-icon>
              <div class="nav-icon-label" :class="show.subs ? 'nav-icon-label-selected': ''">
                Help
              </div>
            </div>

          </div>
        </v-card-actions>
      </v-card>


    </section>

    <section v-if="show.stripe && hasStripe()">

      <stripe-verification-required
          v-if="requiresVerification()"
          ref="warningMessageAccountDisabled"
          :verification="getVerification()"
          @verified="accountVerified($event)"
      ></stripe-verification-required>


      <stripe-express-dashboard
          ref="stripeExpressDashboard"
      >
      </stripe-express-dashboard>
    </section>

    <section v-if="show.details">
      <v-card>
        <v-card-title>Welcome {{ getUserName() }}</v-card-title>
        <v-img :src="getPhoto()" aspect-ratio="1.7" alt="">
        </v-img>
        <!--        <v-card-text>-->
        <!--          <v-list dense>-->
        <!--            <v-list-item>-->
        <!--              <v-list-item-content>Address:</v-list-item-content>-->
        <!--              <v-list-item-content class="align-end">-->
        <!--                <div>{{ getAddressLine1() }}</div>-->
        <!--                <div>{{ getAddressLine2() }}</div>-->
        <!--                <div class="flex">-->
        <!--                  <div>{{ getCity() }},</div>-->
        <!--                  <div-->
        <!--                      style="margin-left: .2rem;"-->
        <!--                  >{{ getState() }}-->
        <!--                  </div>-->
        <!--                  <div-->
        <!--                      style="margin-left: .2rem;"-->
        <!--                  >{{ getZip() }}-->
        <!--                  </div>-->
        <!--                </div>-->
        <!--              </v-list-item-content>-->
        <!--            </v-list-item>-->
        <!--          </v-list>-->
        <!--          <v-card-subtitle v-if="isContractor()">Licenses:</v-card-subtitle>-->
        <!--          <v-list dense>-->
        <!--            <template v-for="license in getLicenses()">-->
        <!--              <v-list-item>-->
        <!--                <v-list-item-content class="align-end">Name:</v-list-item-content>-->
        <!--                <v-list-item-content class="align-end">{{ license.name }}</v-list-item-content>-->
        <!--              </v-list-item>-->
        <!--              <v-list-item>-->
        <!--                <v-list-item-content class="align-end">Number:</v-list-item-content>-->
        <!--                <v-list-item-content class="align-end">{{ license.number }}</v-list-item-content>-->
        <!--              </v-list-item>-->
        <!--              <v-list-item>-->
        <!--                <v-list-item-content class="align-end">Type:</v-list-item-content>-->
        <!--                <v-list-item-content class="align-end">{{ license.type }}</v-list-item-content>-->
        <!--              </v-list-item>-->
        <!--              <v-list-item>-->
        <!--                <v-list-item-content class="align-end">State:</v-list-item-content>-->
        <!--                <v-list-item-content class="align-end">{{ license.state }}</v-list-item-content>-->
        <!--              </v-list-item>-->
        <!--            </template>-->
        <!--          </v-list>-->
        <!--        </v-card-text>-->
        <v-card-actions>
          <v-btn
              id="editDemo"
              ref="editDemo"
              label="Edit"
              @click="$router.push('/settings')"
              color="primary"
              text=""></v-btn>
        </v-card-actions>
      </v-card>

      <v-card class="mt-1rem" v-if="isContractor()">
        <v-card-title>Jobs</v-card-title>
        <v-card-text>
          <v-card-subtitle>As A General Contractor</v-card-subtitle>
          <hr>
          <v-list dense>
            <v-list-item>
              <v-list-item-content>Number of Jobs:</v-list-item-content>
              <v-list-item-content class="align-end">{{ generalTotalJobNumber() }}</v-list-item-content>
            </v-list-item>
            <!--            <v-list-item>-->
            <!--              <v-list-item-content>Number of Subs:</v-list-item-content>-->
            <!--              <v-list-item-content class="align-end">{{ generalTotalNumberOfSubs() }}-->
            <!--              </v-list-item-content>-->
            <!--            </v-list-item>-->
          </v-list>
        </v-card-text>
      </v-card>
    </section>
    <br>

    <v-dialog
        v-model="openHelp"
    >
      <home-page-documentation></home-page-documentation>
    </v-dialog>

    <feedback
        page="home"
    ></feedback>
  </v-container>
</template>

<script>
import StripeExpressDashboard from '../components/stripe/StripeExpressDashboard';
import StripeVerificationRequired from "../components/stripe/StripeVerificationRequired";
import Feedback from '../components/shared/Feedback'
import HomePageDocumentation from "../components/documentation/pages/HomePageDocumentation";

export default {
  name: 'home',
  components: {
    Feedback,
    StripeExpressDashboard,
    HomePageDocumentation,
    StripeVerificationRequired
  },
  data() {
    return {
      openHelp: false,
      stripeVerified: true,
      generalJobs: [],
      show: {
        details: true,
        jobs: false,
        subs: false,
        contractors: false,
        customers: false,
        invoices: false,
        stripe: false,
      },
      user: {}
    }
  },
  methods: {

    viewHelp() {
      this.openHelp = !this.openHelp;
    },

    getPhoto() {
      if (this.haveSparkStateLoaded() && Spark.state.user !== null) {
        return Spark.state.user.photo_url
      }
    },

    goToContractorsPage() {
      // this.$store.commit('setCurrentPage', '/associatedContractors');
      // this.$router.push('/associatedContractors')
      window.location.href = '/#/associatedContractors'
    },

    hasStripe() {
      if (this.haveSparkStateLoaded() && Spark.state.user !== null) {
        return Spark.state.user.customer_stripe_id !== null
      }
    },

    getUserName() {
      // if (this.haveSparkStateLoaded() && Spark.state.user !== null) {
      if (Spark.state.user !== null) {
        return Spark.state.user.name
      }
    },

    getAddressLine1() {
      if (this.haveSparkStateLoaded() && Spark.state.user !== null) {
        if (this.isContractor()) {
          return Spark.state.user.contractor.location.address_line_1
        } else {
          return Spark.state.user.customer.location.address_line_1
        }
      }
    },

    getAddressLine2() {
      if (this.haveSparkStateLoaded() && Spark.state.user !== null) {
        if (this.isContractor()) {
          return Spark.state.user.contractor.location.address_line_2
        } else {
          return Spark.state.user.customer.location.address_line_2
        }
      }
    },

    getCity() {
      if (this.haveSparkStateLoaded() && Spark.state.user !== null) {
        if (this.isContractor()) {
          return Spark.state.user.contractor.location.city
        } else {
          return Spark.state.user.customer.location.city
        }
      }
    },

    getState() {
      if (this.haveSparkStateLoaded() && Spark.state.user !== null) {
        if (this.isContractor()) {
          return Spark.state.user.contractor.location.state
        } else {
          return Spark.state.user.customer.location.state
        }
      }
    },

    getZip() {
      if (this.haveSparkStateLoaded() && Spark.state.user !== null) {
        if (this.isContractor()) {
          return Spark.state.user.contractor.location.zip
        } else {
          return Spark.state.user.customer.location.zip
        }
      }
    },

    getLicenses() {
      if (this.haveSparkStateLoaded() && this.isContractor() && Spark.state.user !== null) {
        return Spark.state.user.contractor.licenses
      }
    },

    // async getUser() {
    //
    //     if (Spark && Spark.state && Spark.state.user === null) {
    //         const {data} = await axios.get('user/current')
    //         if (data.error) {
    //             console.log('getting user error', data)
    //         } else {
    //             Spark.state.user = data;
    //         }
    //     }
    // },

    haveSparkStateLoaded() {

      if (Spark
          && Spark.state && Spark.state.user === null) {
        this.$router.push('/splashScreen')
      }

      if (Spark
          && Spark.state && Spark.state.user && Spark.state.user.password_updated === 0) {
        this.$router.push('/furtherInfo')
      }

      return Spark && Spark.state

    },
    generalTotalJobNumber() {
      return this.generalJobs.length
    },
    generalTotalNumberOfSubs() {
      // return this.generalJobs[0].job_tasks[0].bid_contractor_job_tasks.length
    },
    accountVerified(verification) {
      if (verification) {
        this.stripeVerified = true
      } else {
        this.stripeVerified = false
      }
    },
    getVerification() {
      if (
          this.isContractor()
          && Spark.state.user.contractor
          && Spark.state.user.contractor.stripe_express
      ) {
        return Spark.state.user.contractor.stripe_express.stripe_account_verification
      }
    },
    async getJobs() {
      const {data} = await axios.get('/jobs')
      if (data.error) {
        console.log('error', data)
      } else {
        console.log('data', data)
        this.generalJobs = data
      }
    },
    requiresVerification() {
      if (
          this.isContractor()
          && Spark.state
          && Spark.state.user
          && Spark.state.user.contractor
          && Spark.state.user.contractor.stripe_express
          && Spark.state.user.contractor.stripe_express.stripe_account_verification
      ) {
        let verification = Spark.state.user.contractor.stripe_express.stripe_account_verification;
        if (
            verification.disabled_reason === null
            && verification.currently_due === null
            && verification.eventually_due === null
            && verification.past_due === null
            && verification.pending_verification === null
        ) {
          return false
        } else {
          return true
        }
      }
    },
    isContractor() {
      if (this.haveSparkStateLoaded() && Spark.state.user !== null) {
        return Spark.state.user.usertype === 'contractor'
      }
    },
    showSection(section) {
      this.hideAllSections();
      if (section === 'jobs') {
        this.show.jobs = true;
      } else if (section === 'details') {
        this.show.details = true;
      } else if (section === 'subs') {
        this.show.subs = true;
      } else if (section === 'contractors') {
        this.show.contractors = true;
      } else if (section === 'customers') {
        this.show.customers = true;
      } else if (section === 'invoices') {
        this.show.invoices = true;
      } else if (section === 'stripe') {
        this.show.stripe = true;
      }
    },
    hideAllSections() {
      this.show.details = false;
      this.show.jobs = false;
      this.show.subs = false;
      this.show.contractors = false;
      this.show.customers = false;
      this.show.invoices = false;
      this.show.stripe = false;
    },
  },
  mounted() {
    this.$store.commit('setCurrentPage', this.$router.history.current.path);
    this.getJobs();
    this.haveSparkStateLoaded();
    // this.getUser();
    // this.nextTick(() => {
    // });
  }
}
</script>

<style scoped>

</style>