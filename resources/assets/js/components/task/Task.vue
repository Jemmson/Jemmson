<template>

  <v-container
      v-if="showBid(bidTask)"
  >

    <v-card
        class="mb-1rem"
    >
      <v-card-title
          v-if="needsStripeForCreditCardPayments() && isCreditCardJob()"
          class="w-break text-center uppercase error--text p-2"
          style="color: black"
      >You will need to set up a credit card to bid on this job
      </v-card-title>
      <div class="flex justify-between" style="align-items: center;">
        <v-card-title>{{ bidTask.job_task.job.job_name }}</v-card-title>
        <div class="flex flex-col nav-icon-spacing"
             v-if="getPaymentType('cash')"
        >
          <v-icon
          >mdi-cash
          </v-icon>
          <div class="nav-icon-label">
            Cash Job
          </div>
        </div>
        <div class="flex flex-col nav-icon-spacing"
             v-else-if="getPaymentType('creditCard')"
        >
          <v-icon
          >mdi-credit-card
          </v-icon>
          <div class="nav-icon-label">
            Credit Card Job
          </div>
        </div>
      </div>
      <v-card-subtitle
          style="padding: 0;"
      >
        <v-btn
            color="primary"
            text
            @click="viewContractorInfoForSubs()"
        >
          {{ getCompanyName() }}
        </v-btn>
      </v-card-subtitle>
      <div class="flex justify-between" style="align-items: center;">
        <v-card-subtitle
            style="padding-top: 0; padding-bottom: 0;"
            class="uppercase w-full"
            v-if="showBid(bidTask)"
        >
          {{ jobName(bidTask) }}
        </v-card-subtitle>
        <v-card-subtitle class="capitalize w-full"> {{ getLatestStatus() }}</v-card-subtitle>
      </div>

      <v-card-text v-show="showTheTask">
        <v-card>
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
              <div class="flex flex-col">
                <v-icon
                    :color="show.location ? 'success': ''"
                    class="nav-btn-position"
                    @click="showSection('location')"
                >mdi-google-maps
                </v-icon>
                <div class="nav-icon-label" :class="show.location ? 'nav-icon-label-selected': ''">
                  Location
                </div>
              </div>

              <div class="flex flex-col"
                   v-if="showDeclinedMsg(bidTask) || subHasMessage(bidTask)"
              >
                <v-icon
                    :color="show.messages ? 'success': ''"
                    class="nav-btn-position"
                    color="error"
                    @click="showSection('messages')"
                >mdi-message
                </v-icon>
                <div class="nav-icon-label" :class="show.messages ? 'nav-icon-label-selected': ''">
                  Messages
                </div>
              </div>

              <div class="flex flex-col">
                <v-icon
                    :color="show.images ? 'success': ''"
                    class="nav-btn-position"
                    @click="showSection('images')"
                >mdi-image
                </v-icon>
                <div class="nav-icon-label" :class="show.images ? 'nav-icon-label-selected': ''">
                  Photos
                </div>
              </div>
            </div>
          </v-card-actions>
        </v-card>


        <div v-show="showTheTask">
          <section class="mt-1rem"
                   v-if="show.details"
          >
            <v-card
                :disabled="needsStripeForCreditCardPayments() && isCreditCardJob()"
            >
              <v-card-title class="w-break">Task Details</v-card-title>
              <v-card-text>
                <v-row
                    class="justify-content-around mt-1rem"
                >
                  <strong class="uppercase th-font">Start Date</strong>
                  <strong class="uppercase th-font">Quantity</strong>
                  <strong class="uppercase th-font">Price</strong>
                </v-row>
                <v-row
                    class="justify-content-around mb-15"
                >
                  <div v-if="bidTask.proposed_start_date">{{ prettyDate(bidTask) }}</div>
                  <div v-else>Not Set</div>
                  <div>{{ getTaskQuantity(bidTask) }}</div>
                  <div
                      v-if="subHasEnteredAPrice(bidTask)"
                      v-text="'$ ' + getBidPrice(bidTask)"
                  ></div>
                  <div v-else>Price Not Set</div>
                </v-row>
                <v-divider></v-divider>
                <div
                    v-if="isBidOpen(bidTask)"
                >
                  <v-text-field
                      type="text"
                      v-mask="getCurrencyMask()"
                      v-model="bidPrice"
                      :rules="[bidPriceIsRequired()]"
                      :id="bidTask ? 'price-' + bidTask.id : ''"
                      label="Bid Price *"
                      @change="formatInput($event)"
                  ></v-text-field>
                  <v-text-field
                      type="date"
                      :rules="[startDateIsRequired()]"
                      v-model="startDate"
                      label="Start Date *"
                  ></v-text-field>
                </div>
                <v-btn
                    ref="submit"
                    class=""
                    color="primary"
                    :disabled="canSubmit()"
                    text
                    bottom
                    outlined
                    @click.prevent="update(bidTask)"
                    v-bind:id="bidTask ? bidTask.id: null" :loading="disabled.submit">
                  Submit
                </v-btn>
              </v-card-text>
            </v-card>
          </section>

          <section class="mt-1rem"
                   v-if="show.location"
          >
            <v-card>
              <v-card-title>Location</v-card-title>
              <v-card-text v-if="getAddress(bidTask) !== 'Address Not Available'">
                <v-list dense>
                  <v-list-item>
                    <v-list-item-content>Address:</v-list-item-content>
                    <v-list-item-content class="align-end">
                      <div>{{ getAddressLine1(bidTask) }}</div>
                      <div class="flex">
                        <div>{{ getCity(bidTask) }},</div>
                        <div
                            style="margin-left: .2rem;"
                        >{{ getLocationState(bidTask) }}
                        </div>
                        <div
                            style="margin-left: .2rem;"
                        >{{ getZip(bidTask) }}
                        </div>
                      </div>
                    </v-list-item-content>
                  </v-list-item>
                </v-list>
                <hr>
                <main class="map-responsive">
                  <iframe
                      width="450"
                      height="250"
                      frameborder="0" style="border:0"
                      :src="'https://www.google.com/maps/embed/v1/search?key=AIzaSyBAQZB-zS1HVbyNe2JEk1IgNVl0Pm2xsno&q=' +
                            bidTask.job_task.location.address_line_1 + ' ' +
                            bidTask.job_task.location.city + ' ' +
                            bidTask.job_task.location.state + ' ' +
                            bidTask.job_task.location.zip
                            " allowfullscreen>
                  </iframe>
                </main>
              </v-card-text>

            </v-card>

          </section>

          <section class="mt-1rem"
                   v-if="show.messages"
          >
            <v-card>
              <v-card-title>Messages</v-card-title>

              <v-card-text v-if="subHasMessage(bidTask)">

                <div class="flex flex-col"
                     style="font-weight: bold;"
                >
                  <div>Sub Instructions:</div>
                  <p style="margin-left: 10px; color: black;">{{ getSubMessage(bidTask) }}</p>
                </div>

                <hr>

                <div v-if="showDeclinedMsg(bidTask)" class="flex flex-col"
                     style="font-weight: bold;"
                >
                  <div>Declined Reason:</div>
                  <p style="margin-left: 10px">{{ getDeclinedMessage(bidTask) }}</p>
                </div>

              </v-card-text>

            </v-card>
          </section>

          <section class="mt-1rem"
                   v-if="show.images"
          >

            <v-card>
              <v-card-title>Images</v-card-title>
              <v-card-text>
                <task-images class="images w-full ml-one-quarter-negative" :jobTask="getJobTask(bidTask)"
                             type="sub">
                </task-images>
              </v-card-text>
            </v-card>

          </section>

          <section class="mt-1rem"
                   ref="actionSection"
                   v-if="jobTaskHasBeenApproved()"
          >

            <v-card>
              <v-card-title>Actions</v-card-title>
              <v-card-actions>
                <v-btn
                    ref="markJobFinishedButton"
                    :disabled="!stripeVerified"
                    class="w-full mt-1rem"
                    color="primary"
                    text
                    v-if="showFinishedBtn(bidTask)"
                    @click="finished(bidTask)"
                    :loading="disabled.finished">
                  Click Me When Task Is Finished
                </v-btn>
              </v-card-actions>
            </v-card>

          </section>
        </div>

      </v-card-text>


      <!--            <div class="flex flex-col">-->
      <!--                <div class="flex justify-content-between"-->
      <!--                     style="margin-top: .5rem;-->
      <!--                            margin-left: 10px;"-->
      <!--                >-->
      <!--                    <div style="font-weight: bold; font-size: 15px; margin-right: 6px;">Job Name:</div>-->
      <!--                    <div style="font-size: 15px; margin-right: 6px;">{{ bidTask.job_task.job.job_name }}</div>-->
      <!--                </div>-->

      <!--                <div class="flex align-center justify-content-between"-->
      <!--                     style="margin-top: .5rem;-->
      <!--                            margin-left: 10px;"-->
      <!--                >-->
      <!--                    <div style="font-weight: bold; font-size: 15px; margin-right: 6px;">General Contractor:</div>-->
      <!--                    <v-btn-->
      <!--                            style="font-size: 15px"-->
      <!--                            color="primary"-->
      <!--                            text-->
      <!--                            @click="viewContractorInfoForSubs()"-->
      <!--                    >-->
      <!--                        {{ getCompanyName() }}-->
      <!--                    </v-btn>-->
      <!--                </div>-->
      <!--            </div>-->

      <!--      <div class="flex justify-content-between">-->

      <!--        <div class="flex flex-end" style="margin-right: 1rem;">-->
      <!--          <stripe-verification-required-->
      <!--              style="width: 0%;-->
      <!--                                margin-right: 2rem;-->
      <!--                                margin-bottom: 0rem;-->
      <!--                                height: 0rem;-->
      <!--                                margin-top: 1rem;"-->
      <!--              v-if="requiresVerification()"-->
      <!--              ref="warningMessageAccountDisabled"-->
      <!--              :verification="getVerification()"-->
      <!--              @verified="accountVerified($event)"-->
      <!--          ></stripe-verification-required>-->

      <!--        </div>-->
      <!--      </div>-->
      <v-card-actions>
        <v-btn
            v-show="!showTheTask"
            text
            color="primary"
            @click="showTheTask = !showTheTask"
            width="40%"
        >Show
        </v-btn>
        <v-btn
            v-show="showTheTask"
            color="primary"
            text
            @click="showTheTask = !showTheTask"
            width="40%"
        >Hide
        </v-btn>
        <v-spacer></v-spacer>
        <v-btn
            text
            color="red"
            width="40%"
            @click="showDeleteTaskModal(bidTask)"
        >DELETE
        </v-btn>
      </v-card-actions>


    </v-card>


    <delete-task-modal
        @action="deleteTheTask($event)"
        title="Do You Wish To Delete This Task?"
    >
    </delete-task-modal>

    <show-task-modal
        :job-task="bidTask"
    >
    </show-task-modal>

  </v-container>

</template>

<script>

import TaskImages from '../../components/task/UploadTaskImages'
import ContentSection from '../shared/ContentSection'
import DeleteTaskModal from '../../components/job/DeleteTaskModal'
import ShowTaskModal from '../../components/job/ShowTaskModal'
import Status from '../../components/mixins/Status'
import Card from '../shared/Card'
import Currency from '../../components/mixins/Currency'
import StripeVerificationRequired from '../../components/stripe/StripeVerificationRequired'
import StripeMixin from "../mixins/StripeMixin";

export default {
  name: 'Task',
  components: {
    TaskImages,
    ContentSection,
    ShowTaskModal,
    DeleteTaskModal,
    Card,
    StripeVerificationRequired
  },
  mixins: [
    Status,
    StripeMixin,
    Currency
  ],
  updated() {
    this.getStoredBidPrice
  },
  computed: {
    getStoredBidPrice() {
      // if (localStorage.getItem('bidPrice' + this.bidTask.id)) {
      //   this.bidTask.bid_price = localStorage.getItem('bidPrice' + this.bidTask.id)
      // }
    }
  },
  mounted() {
    this.bidTask ? this.paymentType = this.bidTask.payment_type : this.paymentType = null
  },
  data() {
    return {
      stripeVerified: true,
      show: {
        details: true,
        messages: false,
        images: false,
        location: false
      },
      paymentType: 'cash',
      showTheTask: false,
      disabled: {
        submit: false,
        finished: false,
        deleteTask: false
      },
      deleteTask: {
        id: ''
      },
      jobTask: {},
      formattedBidPrice: '',
      bidPrice: '',
      startDate: '',
      startDateError: false
    }
  },
  props: {
    bidTask: Object,
    user: Object
  },
  methods: {

    bidPriceIsRequired() {
      if (this.bidPrice.length > 3) {
        return true;
      } else {
        return 'A Bid Is Required'
      }
    },

    startDateIsRequired() {
      if (this.startDate.length === 10) {
        return true;
      } else {
        return 'A Start Date Is Required';
      }
    },

    bidPriceIsRequiredCheck() {
      return this.bidPrice.length > 3;
    },

    startDateIsRequiredCheck() {
      return this.startDate.length === 10;
    },

    canSubmit() {
      return !this.startDateIsRequiredCheck() || !this.bidPriceIsRequiredCheck()
    },

    getCompanyName() {
      if (
          this.bidTask
          && this.bidTask.job_task
          && this.bidTask.job_task.task
          && this.bidTask.job_task.task.contractor
      ) {
        return this.bidTask.job_task.task.contractor.company_name;
      }
    },

    viewContractorInfoForSubs() {

      if (
          this.bidTask
          && this.bidTask.job_task
          && this.bidTask.job_task.job
      ) {
        this.$store.commit('setCurrentPage', '/contractor-info');
        this.$router.push({
          name: 'contractor-info',
          params: {contractorId: this.bidTask.job_task.job.contractor_id}
        })
      }

    },

    getVerification() {
      if (
          Spark.state.user.contractor
          && Spark.state.user.contractor.stripe_express
      ) {
        return Spark.state.user.contractor.stripe_express.stripe_account_verification
      }
    },

    requiresVerification() {
      if (
          Spark.state.user.contractor
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

    accountVerified(verification) {
      if (verification) {
        this.stripeVerified = true
      } else {
        this.stripeVerified = false
      }
    },

    showSection(section) {
      this.hideAllSections();
      if (section === 'details') {
        this.show.details = true;
      } else if (section === 'messages') {
        this.show.messages = true;
      } else if (section === 'images') {
        this.show.images = true;
      } else if (section === 'location') {
        this.show.location = true;
      }
    },

    hideAllSections() {
      this.show.details = false;
      this.show.messages = false;
      this.show.images = false;
      this.show.location = false;
    },

    isCreditCardJob() {
      if (this.bidTask.job_task
          && this.bidTask.job_task
          && this.bidTask.job_task.job
      ) {
        return this.bidTask.job_task.job.payment_type === 'creditCard'
      }
    },

    getCurrencyMask() {
      return this.currencyMask(this.bidPrice)
    },

    subHasEnteredAPrice(bidTask) {
      if (bidTask && bidTask.job_task) {
        return bidTask.bid_price !== 0
        // return true
      }
    },

    getBidPrice(bidTask) {
      if (bidTask) {
        return this.convertNumToString(this.formatInput(bidTask.bid_price)).toLocaleString()
      }
    },

    formatInput(input) {
      if (typeof input === 'string') {
        const numLength = input.length
        let pricef = ''
        if (numLength < 3) {
          pricef = '.' + input
          this.formattedBidPrice = pricef
        } else if (numLength > 2) {
          let price = ''
          for (let i = 0; i < numLength - 2; i++) {
            price = price + input[i]
          }
          pricef = price + '.' + input[numLength - 2] + input[numLength - 1]
          this.formattedBidPrice = pricef
        }
        return pricef
      } else if (typeof input === 'number') {
        let bidPrice = input / 100
        this.formattedBidPrice = bidPrice
        return bidPrice
      }
    },

    getLatestStatus() {
      if (
          this.bidTask
          && this.bidTask.job_task
          && this.bidTask.job_task.job
          && this.bidTask.job_task.job.sub_status
          && this.bidTask.job_task.job.sub_status.length > 0
      ) {
        return this.formatStatus(this.getSubStatus_latest(this.bidTask))
      }
    },
    showTheTaskModal() {
      $('#show-task-modal').modal('show')
    },
    showDeleteTaskModal(bidTask) {
      if (bidTask && bidTask.job_task) {
        let job_task = bidTask.job_task
        this.deleteTask.id = job_task.id
        this.jobTask = job_task
        $('#delete-task-modal').modal('show')
      }
    },
    deleteTheTask(action) {
      if (action === 'delete') {
        this.deleteTheActualTask(this.deleteTask.id)
      }
      $('#delete-task-modal').modal('hide')
    },
    async deleteTheActualTask(id) {
      try {
        const data = await axios.post('/jobTask/delete', {
          id: id
        })
        this.getBid(this.job_task.job.id)
      } catch (error) {
        console.log(error)
      }
    },

    zero() {
      let zero = 0;
      return zero.toString();
    },

    startDateAndPriceExist() {
      return this.startDate.length === 10 && this.bidPrice.length > 3;
    },

    update(bidTask) {
      if (this.startDateAndPriceExist() && bidTask && bidTask.job_task) {
        let id = bidTask.id
        // debugger;
        let bid_price = $('#price-' + id).val()
        bid_price = this.convertPriceToIntegers(bid_price)
        let po = this.paymentType
        this.disabled.submit = true
        axios.post('/bidTask', {
          id: id,
          bid_price: bid_price,
          start_date: this.startDate,
          paymentType: po,
          job_task_id: bidTask.job_task.id,
          subId: bidTask.contractor_id,
          generalId: bidTask.job_task.job.contractor_id
        }).then((response) => {
          // TODO: security review
          Vue.toasted.success('Bid Sent.')
          User.emitChange('bidUpdated')
          this.disabled.submit = false
        }).catch((error) => {
          Vue.toasted.error(error.response.data.message)
          this.disabled.submit = false
        })
      } else {
        this.startDateError = true
      }
    },
    setPaymentType(value) {
      this.paymentType = value
    },
    getPaymentType(type) {
      if (this.bidTask
          && this.bidTask.job_task
          && this.bidTask.job_task.job) {
        if (type === 'cash') {
          return this.bidTask.job_task.job.payment_type === 'cash'
        } else {
          return this.bidTask.job_task.job.payment_type === 'creditCard'
        }
      }
    },
    showBid(bid) {
      // TODO: backend what should happen to the bids that wheren't accepted

      const status = this.getLatestStatus()

      return status !== 'denied'
          && status !== 'canceled_by_customer'
          && status !== 'canceled_by_general'
          && status !== 'canceled_bid_task'
          && status !== 'paid'

      // if (bid.job_task === null) {
      //   return false
      // }
      // return (
      //   this.subsBidHasBeenAccepted()
      //   && (this.jobTaskHasBeenApproved() || this.jobHasBeenCompleted() || this.jobTaskHasBeenAccepted())
      //   || (this.jobHasBeenSentToTheCustomer() || this.jobTaskHasBeenInitiated))
    },

    subsBidHasBeenAccepted() {
      if (this.bidTask && this.bidTask.job_task) {
        return this.bidTask.id === this.bidTask.job_task.bid_id
      }
    },

    jobTaskHasBeenApproved() {
      if (this.bidTask && this.bidTask.job_task) {
        const status = this.getLatestJobTaskStatus1(this.bidTask.job_task);
        return status == 'approved by customer'
            || status == 'declined subs work'
            || status == 'customer changes finished task'
      }
    },

    jobHasBeenCompleted() {
      if (this.bidTask && this.bidTask.job_task) {
        return this.bidTask.job_task.job.status === 'job.completed'
      }
    },

    jobTaskHasBeenAccepted() {
      if (this.bidTask && this.bidTask.job_task) {
        return this.bidTask.job_task.status === 'bid_task.accepted'
      }
    },

    jobHasBeenSentToTheCustomer() {
      if (this.bidTask && this.bidTask.job_task) {
        return this.bidTask.job_task.status === 'bid_task.bid_sent'
      }
    },

    jobTaskHasBeenInitiated() {
      if (this.bidTask && this.bidTask.job_task) {
        return this.bidTask.job_task.status === 'bid_task.initiated'
      }
    },

    getLabelClass(bidTask) {

      if (bidTask && bidTask.job_task) {

        let status = this.getStatus(bidTask)

        return Format.statusLabel(
            status,
            this.isGeneral(bidTask),
            this.isCustomer(bidTask),
            bidTask
        )

        // if (this.isUserTheGeneral(bidTask)) {
        //   return Format.statusLabel(
        //     bidTask.job_task.status,
        //     this.isGeneral(bidTask),
        //     this.isCustomer(bidTask),
        //     bidTask
        //   )
        // } else {
        //   return Format.statusLabel(bidTask.status)
        // }
      }

    },
    isGeneral(bidTask) {
      if (bidTask && bidTask.job_task) {
        return Spark.state.user.id === bidTask.job_task.contractor_id
      }
    },
    isCustomer(bidTask) {
      if (bidTask && bidTask.job_task) {
        return Spark.state.user.usertype === 'customer'
      }
    },
    status(bid_task) {
      return User.status(bid_task.status, bid_task.job_task, false)
    },

    jobName(bidTask) {
      if (bidTask && bidTask.job_task) {
        return Format.jobName(bidTask.job_task.task.name)
      }
    },

    prettyDate(bidTask) {
      if (bidTask && bidTask.proposed_start_date) {
        let date = bidTask.proposed_start_date;
        if (date == null)
          return '';
        // return the date and ignore the time
        date = date.split(' ');
        return date[0];
      }
    },

    getTaskQuantity(bidTask) {
      if (bidTask && bidTask.job_task) {
        return bidTask.job_task.qty.toString()
      }
    },

    isBidOpen(bid) {
      const status = this.getLatestStatus();
      if (
          status !== 'approved by customer'
          && status !== 'finished job'
          && status !== 'finished job denied by contractor'
          && status !== 'customer changes finished task'
          && status !== 'finished job approved by contractor'
          && status !== 'waiting for customer payment'
          && status !== 'paid'
      ) {
        return true
      } else {
        return false
      }
    },
    getAddress(bidTask) {
      if (bidTask.job_task && bidTask.job_task.location) {
        if (bidTask.job_task.location !== null) {
          return bidTask.job_task.location.address_line_1 + ' ' +
              bidTask.job_task.location.address_line_2 + ' ' +
              bidTask.job_task.location.city + ' ' +
              bidTask.job_task.location.state + ' ' +
              bidTask.job_task.location.zip
        } else {
          return 'Address Not Available'
        }
      }
    },

    getAddressLine1(bidTask) {
      if (bidTask.job_task && bidTask.job_task.location) {
        return bidTask.job_task.location.address_line_1
      }
      return ''
    },
    getCity(bidTask) {
      if (bidTask.job_task && bidTask.job_task.location) {
        return bidTask.job_task.location.city
      }
      return ''
    },
    getLocationState(bidTask) {
      if (bidTask.job_task && bidTask.job_task.location) {
        return bidTask.job_task.location.state
      }
      return ''
    },
    getZip(bidTask) {
      if (bidTask.job_task && bidTask.job_task.location) {
        return bidTask.job_task.location.zip
      }
      return ''
    },

    getAddressOld(bidTask) {

      if (bidTask && bidTask.job_task) {
        if (bidTask.job_task.location !== null) {
          return bidTask.job_task.location.address_line_1 + ' ' +
              bidTask.job_task.location.address_line_2 + ' ' +
              bidTask.job_task.location.city + ' ' +
              bidTask.job_task.location.state + ' ' +
              bidTask.job_task.location.zip
        } else {
          return 'Address Not Available'
        }

        // return bidTask.job_task.location.address_line_1+" "+

        // <a target="_blank" href="https://www.google.com/maps/search/?api=1&amp;query=3140 Talon Track Apt. 800  McCulloughton Utah 42620-5408">
        // let location_id = 0;
        // if (bidTask.job_task.location_id !== null) {
        //   location_id = bidTask.job_task.location_id;
        // } else {
        //   location_id = bidTask.job_task.job.location_id;
        // }
        // Customer.getAddress(location_id, this.location)
        // return this.location.location
      }

    },
    showDeclinedMsg(bidTask) {
      if (bidTask && bidTask.job_task) {
        let msg = bidTask.job_task.declined_message
        return msg !== null && msg !== ''
      }
    },

    getDeclinedMessage(bidTask) {
      if (bidTask && bidTask.job_task) {
        return bidTask.job_task.declined_message
      }
    },

    subHasMessage(bidTask) {
      if (bidTask && bidTask.job_task) {
        return bidTask.job_task.sub_message !== null && bidTask.job_task.sub_message != ''
      }
    },

    getSubMessage(bidTask) {
      if (bidTask && bidTask.job_task) {
        return bidTask.job_task.sub_message
      }
    },

    getJobTask(bidTask) {
      if (bidTask && bidTask.job_task) {
        return bidTask.job_task
      }
    },

    getStatus(bidTask) {
      if (bidTask && bidTask.job_task) {
        return bidTask.job_task.status
      }
    },

    showFinishedBtn(bid) {
      if (bid && bid.job_task) {
        let status = this.getLatestJobTaskStatus1(bid.job_task);
        return status == 'approved by customer'
            || status == 'declined subs work'
            || status == 'customer changes finished task'
      }
    },

    getLatestJobTaskStatus1(task) {

      let status = null;

      if (task) {
        if (task.job && task.job.job_task_statuses) {
          status = this.formatStatus(this.getJobTaskStatus_latest(task.job))
        } else {
          status = this.formatStatus(this.getTheLatestJobTaskStatus(task.job.job_task_status))
        }
      }
      return status
    },


    finished(bid) {
      SubContractor.finishedTask(bid, this.disabled)
    },
    async getBid(id) {
      try {
        const {
          data
        } = await axios.get('/job/' + id)
        if (data[0]) {
          this.bidTask = data[0]
          this.$store.commit('setJob', data[0])
        } else {
          this.bidTask = data
          this.$store.commit('setJob', data)
        }
        this.$store.commit('setJob', data)
      } catch (error) {
        if (
            error.message === 'Not Authorized to access this resource/api' ||
            error.response !== undefined && error.response.status === 403
        ) {
          this.$router.push('/bids')
        }
        Vue.toasted.error('You are unable to view this bid. Please pick the bid you wish to see.')
      }
    }
  }
}
</script>

<style scoped>

.th-font {
  font-size: 12pt;
}

.input-margins {
  /*margin-right: 11rem;*/
}

.list-card {
  margin-left: 0rem !important;
}

.blue {
  /*background-color: #1c3d5a;*/
}

.images {
  margin: 1rem;
}

.address-adjust {
  padding: .5rem;
}

.hr {
  border: black solid thin;
}

.wrapper {
  display: grid;
  grid-template-columns: 1fr 1fr;
  grid-row-gap: .5rem;
  /*margin-top: 1rem;*/
  /*padding: .75rem;*/
  /*grid-row-gap: 1rem;*/
}

.p-1rem {
  padding: 1rem 1rem 1rem 1rem;
}

.form-control-input {
  /*width: 40%;*/
}

.text-size {
  font-size: 14pt;
}

.task-box {
  border: white thin solid;
  border-radius: 5px;
  margin: .1rem;
  padding: .75rem;
}

.task {
  width: 100%;
  padding: .25rem .25rem .25rem .25rem;
  /*border: black thin solid;*/
  border-radius: 5px;
}

.btn-primary {
  background-color: #3045a2;
}

.box {
  border: white thin solid;
  border-radius: 5px;
  margin: 1rem .1rem 1rem .1rem;
  background-color: #3490dc52;
  padding: .75rem;
}

.space {
  padding-left: 2rem;
}

.job-status {
  width: 100%;
  text-align: center;
  color: white;
  font-size: 14pt;
  margin-right: 1.75rem;
  /*margin: 1rem 1rem 1rem 1rem;*/
  padding: .5rem 1rem .5rem 1rem;
  border-radius: 5px;
}

</style>
