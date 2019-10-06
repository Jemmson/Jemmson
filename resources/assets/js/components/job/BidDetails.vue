<template>
    <div class="row">
        <div class="col-12 mb-3 status" :class="getLabelClass(bid.status)">
            <div class="text-center font-weight-bold">{{ status }}</div>
        </div>
        <section class="col-12">
            <h1 class="card-title">Details</h1>
            <card>
                <main class="row">

                    <strong v-if="subTaskWarning" class="uppercase red ml-1rem mr-1rem">bid price less than the sum of
                        your subs bids</strong>

                    <content-section
                            input-classes="uppercase"
                            label="Job Name:"
                            :content="bid.job_name"
                            type="paymentType"></content-section>

                    <content-section
                            v-if="isCustomer"
                            label="Contractor Name:"
                            input-classes="uppercase"
                            :content="getCompanyName()"
                            type="contractorName"></content-section>

                    <content-section
                            v-if="!isCustomer"
                            input-classes="capitalize"
                            label="Customer Name:"
                            :content="customerName"
                            type="customerName"></content-section>


                    <content-section
                            v-if="isCustomer && !bidHasBeenSubmitted"
                            label="Start Date:"
                            content="Bid Not Complete"
                            type="startDate"></content-section>

                    <content-section
                            v-if="!isCustomer || (isCustomer && bidHasBeenSubmitted)"
                            label="Start Date:"
                            :content-classes="addTaskStartDate ? 'redColor' : ''"
                            :content="agreedStartDate"
                            type="startDate"></content-section>

                    <content-section
                            v-if="isCustomer && !bidHasBeenSubmitted"
                            label="Total Bid Price:"
                            content="Bid Not Complete"
                            type="totalBidPrice"></content-section>

                    <content-section
                            v-if="!isCustomer || (isCustomer && bidHasBeenSubmitted)"
                            label="Total Bid Price:"
                            :content-classes="addTaskBidPrice ? 'redColor' : ''"
                            :content="bidPrice"
                            type="totalBidPrice"></content-section>
                    <hr>

                    <section class="w-full">

                        <div v-if="!this.isCustomer" class="mr-1rem ml-1rem">
                            <button class="w-100 btn btn-sm btn-normal flex-1"
                                    name="addTaskToBid" id="addTaskToBid"
                                    @click="$router.push('/job/add/task')"
                            >
                                Add A Task
                            </button>

                        </div>
                    </section>

                </main>
            </card>
        </section>


        <!-- / tasks -->


        <section ref="job_tasks" class="col-12"
                 v-if="bid.job_tasks !== undefined"
        >
            <div v-if="!isCustomer">
                <h1 class="card-title mt-4">Job Tasks</h1>
                <card>
                    <div>

                        <span class="">
                        (<b ref="job_task_length">{{bid.job_tasks.length}}</b>)
                        </span> Total

                        <button class="btn btn-normal btn-sm float-right"
                                style="width:75%"
                                @click.prevent="viewTasks()">View, Edit, and Add Subs
                        </button>

                    </div>

                    <hr>

                    <div class="flex flex-col">
                        <div class="flex space-between" v-for="jt in bid.job_tasks">
                            <div class="capitalize font-bold-1">{{ jt.task.name }}</div>
                            <div>
                                <div class="list-card-info" v-if="jt.bid_contractor_job_tasks.length > 0">(Subs)</div>
                                <div v-else></div>
                            </div>
                            <div class="list-card-info-red" v-if="jt.status === 'bid_task.denied'">Declined</div>
                            <div>{{ formatPrice(jt.cust_final_price) }}</div>
                        </div>
                    </div>

                </card>
            </div>

            <div v-else-if="bid.status !== 'bid.initiated' && bid.status !== 'bid.in_progress'">
                <h1 class="card-title mt-4">Job Tasks</h1>
                <card>

                    <div>
                            <span class="">
                        (<b ref="job_task_length_customer">{{bid.job_tasks.length}}</b>)
                        </span> Total

                        <button class="btn btn-normal btn-sm float-right"
                                @click.prevent="viewTasks()">View Tasks
                        </button>
                    </div>

                    <hr>

                    <div class="flex flex-col">
                        <div class="flex space-between" v-for="jt in bid.job_tasks">
                            <div class="capitalize font-bold-1">{{ jt.task.name }}</div>
                            <div>{{ formatPrice(jt.cust_final_price) }}</div>
                        </div>
                    </div>

                </card>
            </div>

        </section>

        <section class="col-12" v-if="customerHasCompletedTasks()">
            <h1 class="card-title mt-4">Completed Tasks</h1>
            <card>
                <main class="row w-full ml-0">
                    <completed-tasks class="w-full" :bid="bid">
                    </completed-tasks>
                </main>
            </card>
        </section>


        <!--        <section class="col-12">-->
        <!--            <h1 v-if="isCustomer" class="card-title mt-4">Payment Details For Contractor</h1>-->
        <!--            <h1 v-else class="card-title mt-4">Payment Details For Customer</h1>-->
        <!--            <card>-->
        <!--                <main class="row">-->
        <!--                    <section class="col-12 mb-3">-->
        <!--                        <label for="paymentType" class="">Payment Method Selected:</label>-->
        <!--                        <select v-model="selectedPayment" class="float-right form-control" name="paymentType"-->
        <!--                                id="paymentType">-->
        <!--                            <option value="creditCard">Credit Card</option>-->
        <!--                            <option value="cash">Cash</option>-->
        <!--                        </select>-->
        <!--                    </section>-->
        <!--                    <section ref="paymentInstructions" class="col-12 mb-2" v-if="selectedPayment === 'cash'">-->
        <!--                        <label for="paymentInstructions" class="">Payment Instructions:</label>-->
        <!--                        <input ref="paymentInstructionsMessage"-->
        <!--                               id="paymentInstructions"-->
        <!--                               class="float-right form-control"-->
        <!--                               v-model="payWithCashMessage">-->
        <!--                        <button class="btn btn-sm btn-normal float-right mt-half-rem"-->
        <!--                                ref="paywithCashButton"-->
        <!--                                :disabled="disabled.submitMessage"-->
        <!--                                @click="submitPayWithCashMessage"-->
        <!--                        >-->
        <!--                            <span v-if="disabled.submitMessage">-->
        <!--                                        <i class="fa fa-btn fa-spinner fa-spin"></i>-->
        <!--                                      </span>-->
        <!--                            Submit-->
        <!--                        </button>-->

        <!--                        <div v-if="successfulUpdate === 'true'">-->
        <!--                            Your message has been updated-->
        <!--                        </div>-->

        <!--                        <div v-if="successfulUpdate === 'false'">-->
        <!--                            Your message was not updated. please try again-->
        <!--                        </div>-->


        <!--                    </section>-->
        <!--                </main>-->
        <!--            </card>-->
        <!--        </section>-->

        <section ref="job_address" class="col-12" v-if="showAddress">
            <h1 class="card-title mt-4">Job Address</h1>
            <card>

                <div class="flex flex-col">
                    <div>
                        {{ bid.location.address_line_1 }}
                    </div>
                    <div>
                        {{ bid.location.city }}, {{ bid.location.state }} {{ bid.location.zip }}
                    </div>
                </div>

                <hr>

                <main class="map-responsive">
                    <iframe
                            width="450"
                            height="250"
                            frameborder="0" style="border:0"
                            :src="'https://www.google.com/maps/embed/v1/search?key=AIzaSyBAQZB-zS1HVbyNe2JEk1IgNVl0Pm2xsno&q=' +
                            bid.location.address_line_1 + ' ' +
                            bid.location.city + ' ' +
                            bid.location.state + ' ' +
                            bid.location.zip
                            " allowfullscreen>
                    </iframe>
                </main>

<!--                '&key=AIzaSyCI21pbEus0AZc4whkqwM3VaDO1YV1Dygs'"-->
                <!-- <a target="_blank"
                      :href="'https://www.google.com/maps/search/?api=1&query=' + bid.location.address_line_1">
                        {{ bid.location.address_line_1 }}
                        <br>
                        {{ bid.location.city }}, {{ bid.location.state }} {{ bid.location.zip }}
                    </a> -->
                <!-- <div class="flex flex-col">
                              <span class="label mb-4">TOTAL PRICE:</span>
                              <span>${{ bid.bid_price }}</span>
                    </div>-->
            </card>
        </section>

        <section class="col-12">
            <h1 v-if="isCustomer" class="card-title mt-4">Special Instructions For Contractor</h1>
            <h1 v-else class="card-title mt-4">Special Instructions From Customer</h1>
            <card>
                <main class="row">
                    <section class="col-12">

                        <div style="display: none;">{{ messageFromCustomer }}</div>
                        <div style="display: none;">{{ getPaidWithCashMessage }}</div>


                        <textarea ref="message_text_area"
                                  v-model="customerNotesMessage"
                                  name="notes" id="notes" cols="30" rows="10"
                                  class="form-control"
                                  :disabled="!isCustomer"
                        >

                            </textarea>

                        <button v-if="isCustomer" class="btn btn-normal btn-sm"
                                style="margin-top: .5rem"
                                ref="update_customer_notes_button"
                                @click="updateGeneralContractorNotes"
                        >Submit
                        </button>

                    </section>
                </main>
            </card>
        </section>

        <!--        <div class="col-12">-->
        <!--            <h1 class="card-title ml-4 mt-4">Attachments</h1>-->
        <!--            <div class="mb-4">-->
        <!--                <img src="img/test.jpg" style="height: 100px;" alt="Attachments">-->
        <!--            </div>-->
        <!--        </div>-->

        <!--        <div class="col-12 mb-4">-->
        <!--            <card>-->
        <!--                <div class="row">-->
        <!--                    <div class="col">-->
        <!--                        <p class="d-inline">Upload Attachment</p>-->
        <!--                        <i class="fas fa-plus-circle text-primary float-right sm-icon"></i>-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--            </card>-->
        <!--        </div>-->

        <stripe :user="getCurrentUser()">
        </stripe>

    </div>
</template>

<script>
  import Info from '../shared/Info'
  import Format from '../../classes/Format'
  import Card from '../shared/Card'
  import Stripe from '../stripe/Stripe'
  import GeneralContractorBidActions from './GeneralContractorBidActions'
  import { mapGetters, mapMutations, mapActions } from 'vuex'
  import ContentSection from '../shared/ContentSection'
  import CompletedTasks from './CompletedTasks'

  export default {
    components: {
      Card,
      Stripe,
      Info,
      ContentSection,
      CompletedTasks,
      GeneralContractorBidActions
    },
    props: {
      bid: Object,
      isCustomer: Boolean,
      customerName: String
    },
    created: function() {

      Bus.$on('needsStripe', () => {
        $('#stripe-modal').modal()
      })

    },
    data() {
      return {
        area: {
          area: ''
        },
        addTaskStartDate: false,
        addTaskBidPrice: false,
        statuses: [
          {
            type: 'Bid Initiated',
            description:
              'Contractor has sent a bid but has not added a task to the job'
          },
          {
            type: 'BID CHANGE REQUESTED - PLEASE REVIEW',
            description:
              'Customer has not approved the bid and is asking for a change to be made'
          },
          {
            type: 'Bid In Progress',
            description:
              'Contractor has added tasks to the bid but has not yet submitted it to the customer'
          },
          {
            type: 'Waiting on Customer Approval',
            description:
              'Contractor has submitted the finished bid and is now waiting for the customer to approve it'
          },
          {
            type: 'In Progress',
            description:
              'The job is in progress and it is waiting for the contrator sub to finish the job'
          },
          {
            type: 'Job Completed',
            description:
              'The Customer has paid for the job and the job is completed'
          }
        ],
        subTaskWarning: false,
        customerNotesMessage: '',
        showPaidWithCashNotes: false,
        disableCustomerNotesButton: false,
        customerNotes: false,
        customerNotes_contractor: false,
        areaError: '',
        payWithCashMessage: '',
        successfulUpdate: '',
        locationExists: false,
        customerInfo: false,
        paymentTypeCash: false,
        paymentTypeStripe: true,
        selectedPayment: 'creditCard',
        submissionCard: false,
        cancelBidCard: false,
        disabled: {
          cancelBid: false,
          jobCompleted: false,
          submitBid: false,
          submitMessage: false
        }
      }
    },
    computed: {
      ...mapGetters(['getCustomerName']),
      agreedStartDate() {
        if (this.bid.agreed_start_date !== undefined && this.bid.agreed_start_date !== null) {
          this.addTaskStartDate = false
          let d = this.bid.agreed_start_date
          let date = d.split(' ')
          let format_date = date[0].split('-')
          return format_date[1] + '/' + format_date[2] + '/' + format_date[0]
        } else {
          if (this.isCustomer) {
            return ''
          } else {
            this.addTaskStartDate = true
            return 'Add A Task'
          }

        }
      },
      messageFromCustomer() {
        if (this.bid.customer) {
          this.customerNotesMessage = this.bid.customer.customer.notes
          return this.bid.customer.customer.notes
        }
      },
      getPaidWithCashMessage() {
        if (this.bid) {
          if (this.bid.paid_with_cash_message) {
            this.payWithCashMessage = this.bid.paid_with_cash_message
            this.selectedPayment = 'cash'
            return this.bid.paid_with_cash_message
          }
        }
      },
      bidPrice() {
        if (
          this.bid.bid_price &&
          (this.bid.status === 'bid.initiated' ||
            this.bid.status === 'bid.in_progress' ||
            this.bid.status === 'job.approved' ||
            this.bid.status === 'bid.declined' ||
            this.bid.status === 'bid.sent'
          )
        ) {
          this.addTaskBidPrice = false
          let theBidPrice = this.bid.bid_price
          return '$ ' + Format.decimal(theBidPrice)
        } else {
          if (this.isCustomer) {
            return ''
          } else {
            this.addTaskBidPrice = true
            return 'Add A Task'
          }

        }
      },
      showBidPrice() {
        if (this.isCustomer) {
          const status = this.bid.status
          if (status !== 'bid.initiated' && status !== 'bid.in_progress') {
            return true
          }
          return false
        }
        return true
      },
      status() {
        return User.status(this.bid.status, this.bid, Spark.state.user)
      },
      bidHasBeenSubmitted() {
        return this.bid.status !== 'bid.initiated' &&
          this.bid.status !== 'bid.in_progress'
      },
      showDeclinedMessage() {
        return (
          !this.isCustomer &&
          this.bid.declined_message !== null &&
          this.bid.status === 'bid.declined'
        )
      },

      disableSubmitBid() {
        return this.bid.status === 'bid.sent'
      },

      showAddress() {
        return (
          this.bid.location_id !== undefined &&
          this.bid.location_id !== null &&
          this.bid.location !== null &&
          !this.isCustomer
        )
      }
    },
    watch: {
      bid: function() {
        this.bid = this.bid
      }
    },
    methods: {
      formatPrice(price) {
        return '$ ' + Format.decimal(price)
      },
      customerHasCompletedTasks() {
        let taskIsFinished = false
        if (this.bid && this.bid.job_tasks) {
          for (let i = 0; i < this.bid.job_tasks.length; i++) {
            if (this.bid.job_tasks[i].status === 'bid_task.finished_by_general') {
              taskIsFinished = true
            }
          }
        }
        return this.isCustomer && taskIsFinished
      },
      getCompanyName() {
        if (this.bid) {
          if (this.bid.job_tasks && (this.bid.job_tasks.length !== 0)) {
            if (this.bid.job_tasks[0].task && this.bid.job_tasks[0].task.contractor) {
              return this.bid.job_tasks[0].task.contractor.company_name
            }
            if (this.bid.contractor.contractor) {
              return this.bid.contractor.contractor.company_name
            }
          } else if (this.bid.contractor) {
            return this.bid.contractor.contractor.company_name
          }
        }
      },
      bidHasNoTasks() {
        if (this.bid.job_tasks) {
          return this.bid.job_tasks.length === 0
        }
      },
      getCurrentUser() {
        if (Spark.state) {
          return Spark.state.user
        }
      },
      cancelDialog() {
        this.cancelBidCard = false
        this.submissionCard = false
        this.disabled.cancelBid = false
        this.disabled.submitBid = false
      },
      openCancelDialogCard() {
        this.cancelBidCard = true
        this.disabled.cancelBid = true
      },
      showPreApprovedActions() {
        return this.bid.status !== 'job.approved' && this.bid.status !== 'job.completed' && this.isGeneral(this.bid.contractor_id, Spark.state.user.id)
      },
      cancelTheBid() {
        this.disabled.cancelBid = true
        this.cancelBidCard = false
        this.disabled.cancelBid = false
      },
      submitTheBid() {
        this.submissionCard = false
        this.disabled.submitBid = false
        this.notifyCustomerOfFinishedBid()
      },
      showSubmissionCard() {
        console.log('hello')
        this.cancelBidCard = false
        this.submissionCard = true
        this.disabled.submitBid = true
      },

      showCancelCard() {
        this.disabled.cancelBid = true
        this.submissionCard = false
        this.cancelBidCard = true
      },

      notifyCustomerOfFinishedBid() {

        // go through each job task and compare the sub price to the contractor task price
        // first check if there is a sub.
        // check if the sub price is an accepted price
        // compare the the accepted sub price to the contractor price
        // if the accepted sub price is higher then throw an error

        let subTaskWarning = false
        for (let i = 0; i < this.bid.job_tasks.length; i++) {
          if (this.bid.job_tasks[i].sub_final_price > this.bid.job_tasks[i].cust_final_price) {
            subTaskWarning = true
          }
        }

        if (!subTaskWarning) {
          this.subTaskWarning = false
          GeneralContractor.notifyCustomerOfFinishedBid(this.bid, this.disabled)
        } else {
          console.log('subs price is higher than contractor price')
          this.subTaskWarning = true
        }

      },

      isGeneral(contractor_id, user_id) {
        if (this.bid !== null) {
          return contractor_id === user_id
        }
        return false
      },
      viewTasks() {
        this.$router.push('/job/tasks')
      },
      paymentMethod(paymentType) {
        if (paymentType === 'cash') {
          this.selectedPayment = 'cash'
          this.paymentTypeCash = true
          this.paymentTypeStripe = false
        } else {
          this.selectedPayment = 'stripe'
          this.paymentTypeCash = false
          this.paymentTypeStripe = true
        }
      },
      async submitPayWithCashMessage() {
        this.disabled.submitMessage = true
        try {
          const data = await axios.post('/paidWithCashMessage', {
            jobId: this.bid.id,
            paidWithCashMessage: this.payWithCashMessage
          })

          if (data.data.message) {
            this.successfulUpdate = 'true'
            setTimeout(function() {
              this.successfulUpdate = ''
              this.disabled.submitMessage = false
            }.bind(this), 2000)
          } else {
            this.successfulUpdate = 'false'
            setTimeout(function() {
              this.successfulUpdate = ''
              this.disabled.submitMessage = false
            }.bind(this), 2000)
          }

        } catch (error) {
          console.log('error')
        }
      },
      getLabelClass(status) {
        return Format.statusLabel(status)
      },
      showNotes() {
        this.customerNotes = !this.customerNotes
      },
      ...mapMutations(['setCustomerName']),
      ...mapActions(['actCustomerName']),
      updateGeneralContractorNotes() {
        Customer.updateNotesForJob(
          this.customerNotesMessage,
          this.bid.customer.id
        )
      },
      updateArea() {
        // Customer.updateArea (this.area.area, this.bid.id);
      },
      showArea() {
        // console.log('user type: ' + User.isContractor())
        return this.area.area !== '' && !this.isCustomer
      },
      initializePayWithCashMessageValue() {
        if (this.bid.paid_with_cash_message) {
          console.log('I am true')
          console.log(this.bid.paid_with_cash_message)
          this.payWithCashMessage = this.bid.paid_with_cash_message
          this.customerNotesMessage = this.bid.customer.customer.notes
        }

        if (this.bid.customer) {
          console.log(this.bid.customer.customer.notes)
          this.customerNotesMessage = this.bid.customer.customer.notes
        }
        console.log(this.bid.paid_with_cash_message)
        console.log(this.bid.customer)
      }
    },
    mounted() {
      this.initializePayWithCashMessageValue()
    }
  }
</script>

<style lang="less" scoped>

    .status {
        padding-top: 1rem;
        padding-bottom: 1rem;
        font-family: auto;
        font-size: 24pt;
    }

    .no-notes {
        text-align: center;
        margin-right: -9rem;
    }

    .status-header {
        font-size: 1rem;
        margin-left: -1rem;
        margin-right: -2rem;
        text-align: start;
        margin-bottom: .15rem;
        padding: .25rem;
        margin-top: .15rem;
    }

    .description {
        font-size: .9rem;
        margin-left: -1rem;
        margin-right: -2rem;
        text-align: start;
        margin-bottom: .15rem;
        /*background-color: beige;*/
        padding: .25rem;
        margin-top: .15rem;
        border-radius: 5px;
    }

    /*.spacing {*/
    /*margin-bottom: 10rem;*/
    /*}*/

    .wrapper {
        display: grid;
        grid-template-columns: 1fr 1fr;
    }

    .w-100 {
        width: 100%;
    }

    // .btn-width {
    //     width: 15rem;
    // }
    .status {
        /*padding: 1rem;*/
        /*padding-left: 6px;*/
        /*padding-right: 6px;*/
        align-items: center;
        justify-content: space-evenly;
    }

    .btn-width {
        width: 100%;
    }

    .notes-width {
        max-width: 75%;
    }

    span {
        font-size: 15px;
    }

    /*@media (min-width: 762px) {*/
    /*.btn-width {*/
    /*width: 27%;*/
    /*}*/
    /*}*/
</style>