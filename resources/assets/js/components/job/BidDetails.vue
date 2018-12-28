<template>
    <!-- /all details of a bid -->
    <div class="flex flex-col" v-if="bid.job_name !== undefined">
        <!-- JOB STATUS -->
        <div class="border-b pb-4 mb-6">
            <div class="status flex justify-between" :class="getLabelClass(bid.status)">
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

        <div class="flex flex-col self-center mb-6">
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
                <div v-else class>No Address is Set Yet</div>
            </div>
            <!-- <div class="flex flex-col">
                      <span class="label mb-4">TOTAL PRICE:</span>
                      <span>${{ bid.bid_price }}</span>
            </div>-->
        </div>

        <div>
            <div v-if="bid.paid_with_cash_message !== '' && bid.paid_with_cash_message !== null"
                 class="flex justify-around">
                <button
                        class="btn btn-blue btn-width mr-6"
                        name="showNotes"
                        id="showNotes"
                        @click="customerNotes = !customerNotes"
                >Customer Notes For Job
                </button>
                <button
                        class="btn btn-blue btn-width ml-6"
                        name="showPaidWithCashNotes"
                        id="showPaidWithCashNotes"
                        @click="showPaidWithCashNotes = !showPaidWithCashNotes"
                >Paid With Cash Instructions
                </button>
            </div>
            <div v-else class="flex justify-around">
                <button
                        class="btn btn-blue btn-width"
                        name="showNotes"
                        id="showNotes"
                        @click="customerNotes = !customerNotes"
                >Customer Notes For Job
                </button>
            </div>
        </div>

        <div>

            <div v-if="!isCustomer">
                <div v-if="bid.paid_with_cash_message !== '' && bid.paid_with_cash_message !== null">
                    <div v-if="bid.customer.customer.notes !== '' && bid.customer.customer.notes !== null" class style>
                        <transition name="slide-fade">
                            <div class="mt-3 mr-6" v-show="customerNotes">{{ bid.customer.customer.notes }}</div>
                        </transition>
                    </div>
                    <div v-else>
                        <transition name="slide-fade">
                            <div class="mt-3 mr-6 no-notes" v-show="customerNotes">
                                The customer does not have any notes for
                                this job
                            </div>
                        </transition>
                    </div>
                    <transition name="slide-fade">
                        <div class="mt-3 ml-6" v-show="showPaidWithCashNotes">{{ bid.paid_with_cash_message }}</div>
                    </transition>
                </div>
                <div v-else>
                    <div v-if="bid.customer.customer.notes !== '' && bid.customer.customer.notes !== null" class style>
                        <transition name="slide-fade">
                            <div class="mt-3" v-show="customerNotes">{{ bid.customer.customer.notes }}</div>
                        </transition>
                    </div>
                    <div v-else>
                        <transition name="slide-fade">
                            <div class="mt-3 no-notes" v-show="customerNotes">
                                The customer does not have any notes for
                                this job
                            </div>
                        </transition>
                    </div>
                </div>
            </div>

            <div v-show="isCustomer" class>
                <transition name="slide-fade">
                    <div v-show="customerNotes">
                        <div class="mt-4">
                            <textarea
                                    class="form-control"
                                    :value="bid.customer.customer.notes"
                                    name
                                    id
                                    cols="40"
                                    rows="10"
                                    @keyup="customerNotesMessage = $event.target.value"
                            ></textarea>
                        </div>
                        <div class="mt-2">
                            <button
                                    class="btn btn-red"
                                    @click.prevent="updateGeneralContractorNotes()"
                                    :disabled="disableCustomerNotesButton"
                                    ref="custNotesUpdate"
                            >
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
    </div>
</template>

<script>
  import Info from '../shared/Info'
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
        return Format.statusLabel(status)
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

    @media (min-width: 762px) {
        .btn-width {
            width: 27%;
        }
    }
</style>