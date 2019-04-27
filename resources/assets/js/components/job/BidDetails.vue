<template>
    <!-- /all details of a bid -->
    <div class="flex flex-col" v-if="bid.job_name !== undefined">
        <!-- JOB STATUS -->
        <div class="border-b pb-4 mb-6">
            <div
                    class="status flex justify-between"
                    ref="bidStatus"
                    :class="getLabelClass(bid.status)">
                <div></div>
                <div>{{ status }}</div>
                <div>
                    <info
                            buttons="false"
                            class="spacing"
                            v-show="!isCustomer"
                            title="Statuses">
                        <div slot="tldr">
                            <div class="flex flex-col">
                                <div v-for="status in statuses" :key="status.type">
                                    <div class="flex justify-between">
                                        <div class="mr-2">
                                            <strong class="status-header uppercase">{{ status.type }}:</strong>
                                            <div class="description">{{ status.description }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div slot="full">
                            <p>As a contractor the job goes through various statuses. The first status is:</p>
                            <div class="text-center">
                                <strong>Initiated</strong>
                            </div>
                            <p>
                                This status refers to a job that has just been initiated but there have been no tasks
                                assigned to the bid.
                            </p>
                        </div>
                    </info>
                </div>
            </div>
        </div>

        <div class="flex flex-col self-center mb-6 mt-3">
            <!-- <span class="label mb-2">JOB NAME</span> -->
            <p class="text-2xl font-extrabold uppercase">{{ bid.job_name }}</p>
        </div>

        <div class="flex justify-between mb-6">
            <div class="flex flex-col">
                <span class="label mb-2">CUSTOMER NAME</span>
                <span v-if="isCustomer">{{ customerName }}</span>
                <span v-else>{{ customerName }}</span>
            </div>
            <div class="flex flex-col">
                <span class="label mb-2">START DATE</span>
                <span>{{ agreedStartDate }}</span>
            </div>
        </div>

        <div class="flex justify-between mb-6">
            <div class="flex flex-col">
                <span class="label">JOB ADDRESS:</span>
                <button v-if="isCustomer" style="background-color: black; color: white;" class="btn btn-lg btn-primary" v-on:click="">ChangeJobAddress->implement</button>
                <a
                        target="_blank"
                        v-if="showAddress"
                        :href="'https://www.google.com/maps/search/?api=1&query=' + bid.location.address_line_1"
                >
                    <address>
                        <br>
                        {{ bid.location.address_line_1 }}
                        <br>
                        {{ bid.location.city }}, {{ bid.location.state }} {{ bid.location.zip }}
                    </address>
                </a>
            <button v-if="isCustomer" style="background-color: black; color: white;" class="btn btn-lg btn-primary" v-on:click="">CustomerCanAddJobPictures->implement</button>
            </div>
            <!-- <div class="flex flex-col">
                      <span class="label mb-4">TOTAL PRICE:</span>
                      <span>${{ bid.bid_price }}</span>
            </div>-->
        </div>


        <div v-show="!isCustomer">
            <button
                    class="btn btn-blue btn-width mb-1"
                    name="showNotes"
                    id="showNotesContractor"
                    ref="notesForCustomerButton_contractor"
                    @click="customerNotes_contractor = !customerNotes_contractor">
                Customer Notes For Job
            </button>

            <div v-show="customerNotes_contractor">
                <transition name="slide-fade">

                    <div>
                        <div v-show="customerNotes_contractor">
                            <div class="mt-3"
                                 ref="customerNotesInfo_contractor_empty">
                                {{ bid.customer.customer.notes }}
                            </div>
                        </div>
                        <!--<div v-else>-->
                            <!--<div class="mt-3 no-notes"-->
                                 <!--ref="customerNotesInfo_contractor">-->
                                <!--The customer does not have any notes for-->
                                <!--this job-->
                            <!--</div>-->
                        <!--</div>-->
                    </div>

                </transition>
                <!--<div v-else>-->
                <!--<transition name="slide-fade">-->
                <!---->
                <!--</transition>-->
                <!--</div>-->
            </div>
        </div>


        <div v-show="!isCustomer">
            <button
                    v-show="bid.paid_with_cash_message !== '' &&
                                bid.paid_with_cash_message !== null"
                    class="btn btn-blue btn-width mt-1"
                    name="showPaidWithCashNotes"
                    id="showPaidWithCashNotes"
                    ref="paidWithCashBtn"
                    @click="showPaidWithCashNotes = !showPaidWithCashNotes"
            >Paid With Cash Instructions
            </button>
            <div v-show="showPaidWithCashNotes">
                <transition name="slide-fade">
                    <div class="mt-3 ml-6" v-show="showPaidWithCashNotes">{{ bid.paid_with_cash_message }}</div>
                </transition>
            </div>
        </div>


        <div v-show="isCustomer">
            <button
                    class="btn btn-blue btn-width mb-1"
                    name="showNotes"
                    id="showNotesCustomer"
                    ref="notesForCustomerButton_customer"
                    @click="customerNotes = !customerNotes"
            >Customer Notes For Job
            </button>
            <div v-show="customerNotes" class>
                <transition name="slide-fade">
                    <div>
                        <div class="mt-4">
                            <textarea
                                    class="form-control"
                                    :value="bid.customer.customer.notes"
                                    cols="40"
                                    ref="customerNotesTextArea"
                                    rows="10"
                                    @keyup="customerNotesMessage = $event.target.value"
                            >blah</textarea>
                        </div>
                        <div class="mt-2">
                            <button
                                    class="btn btn-red"
                                    @click.prevent="updateGeneralContractorNotes()"
                                    :disabled="disableCustomerNotesButton"
                                    ref="customerNotesSubmitButton">
                                  <span v-if="disableCustomerNotesButton">
                                    <i class="fa fa-btn fa-spinner fa-spin"></i>
                                  </span>
                                Submit
                            </button>
                        </div>
                    </div>
                </transition>
            </div>
        </div>


        <div class="flex justify-between mt-4">
            <span class="label mb-4">TOTAL PRICE:</span>
            <div v-if="!isCustomer">
                <span class="font-bold">${{ bid.bid_price }}</span>
            </div>
            <div v-else>
        <span
                v-if="bid.status !== 'bid.in_progress' && bid.status !== 'bid.initiated' "
                class="font-bold"
        >${{ bid.bid_price }}</span>
                <span v-else class="font-bold">
          <i>PENDING</i>
        </span>
            </div>
        </div>

        <!-- Declined Message -->
        <div class="flex space-between flex-col" v-if="showDeclinedMessage">
            <h4>
                <label class="status label label-warning red py-s">Declined Reason</label>
            </h4>
            <p class="message">{{ bid.declined_message}}</p>
        </div>


        <!--<div v-show="isCustomer">-->
        <!--<div ref="hello" v-show="customerNotes">hello</div>-->
        <!--</div>-->

        <!--<div ref="world">-->
        <!--<div ref="hello" v-show="customerNotes">hello</div>-->
        <!--</div>-->


    </div>
</template>

<script>
  import Info from '../shared/Info'
  // import Customer from '../../classes/Customer'
  import { mapGetters, mapMutations, mapActions } from 'vuex'

  export default {
    components: {
      Info
    },
    props: {
      bid: Object,
      isCustomer: Boolean,
      customerName: String
    },
    data() {
      return {
        area: {
          area: ''
        },
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
          //   ,
          //   {
          //       type: '',
          //       description: ''
          //   },
          //   {
          //       type: '',
          //       description: ''
          //   },
          //   {
          //       type: '',
          //       description: ''
          //   },
          //   {
          //       type: '',
          //       description: ''
          //   },
          //   {
          //       type: '',
          //       description: ''
          //   }
        ],
        customerNotesMessage: '',
        showPaidWithCashNotes: false,
        disableCustomerNotesButton: false,
        customerNotes: false,
        customerNotes_contractor: false,
        areaError: '',
        locationExists: false,
        customerInfo: false
      }
    },
    computed: {
      ...mapGetters(['getCustomerName']),
      agreedStartDate() {
        if (this.bid.agreed_start_date !== null) {
          let d = this.bid.agreed_start_date
          let date = d.split(' ')
          let format_date = date[0].split('-')
          return format_date[1] + '/' + format_date[2] + '/' + format_date[0]
        }
      },
      showBidPrice() {
        if (User.isCustomer()) {
          const status = this.bid.status
          if (status !== 'bid.initiated' && status !== 'bid.in_progress') {
            return true
          }
          return false
        }
        return true
      },
      status() {
        return User.status(this.bid.status, this.bid)
      },
      showDeclinedMessage() {
        return (
          !this.isCustomer &&
          this.bid.declined_message !== null &&
          this.bid.status === 'bid.declined'
        )
      },
      showAddress() {
        return (
          this.bid.location_id !== undefined &&
          this.bid.location_id !== null &&
          this.bid.location !== null
        )
      }
    },
    methods: {
      getLabelClass(status) {
        return Format.statusLabel(status,)
      },
      showNotes() {
        this.customerNotes = !this.customerNotes
      },
      ...mapMutations(['setCustomerName']),
      ...mapActions(['actCustomerName']),
      updateGeneralContractorNotes() {
        Customer.updateNotesForJob(
          this.customerNotesMessage,
          this.bid.customer_id
        )
      },
      updateArea() {
        // Customer.updateArea (this.area.area, this.bid.id);
      },
      showArea() {
        console.log('user type: ' + User.isContractor())
        return this.area.area !== '' && User.isContractor()
      }
    },
    mounted: function() {}
  }
</script>

<style lang="less" scoped>

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

    /*@media (min-width: 762px) {*/
        /*.btn-width {*/
            /*width: 27%;*/
        /*}*/
    /*}*/
</style>