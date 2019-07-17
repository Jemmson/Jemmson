<template>
    <div class="row">
        <div class="col-12 mb-3">
            <div class="text-center font-weight-bold" :class="getLabelClass(bid.status)">{{ status }}</div>
            <!-- <info buttons="false" class="spacing" v-show="!isCustomer" title="Statuses">
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
                  </info> -->
        </div>

        <section class="col-12">
            <h1 class="card-title">Details</h1>
            <card>
                <main class="row">

                    <section class="col-12 mb-3" v-if="isCustomer">
                        <label for="contractorName">Contractor Name:</label>
                        <strong id="contractorName" ref="details_contractor_name" class="float-right">Contractor
                            Joe</strong>
                        <!--            <span ref="details_contractor_name" class="float-right">{{ contractorName }}</span>-->
                    </section>

                    <section class="col-12 mb-3" v-else>
                        <label for="customerName" class="">Customer Name:</label>
                        <strong id="customerName" ref="details_customer_name" class="float-right">{{ customerName
                            }}</strong>
                    </section>


                    <section class="col-12 mb-3">
                        <label for="startDate" class="">Start Date:</label>
                        <strong id="startDate" ref="details_start_date" class="float-right">{{ agreedStartDate
                            }}</strong>
                    </section>

                    <section class="col-12 mb-3">
                        <label for="totalBidPrice" class="">Total Bid Price:</label>
                        <strong id="totalBidPrice" ref="details_total_bid_price" class="float-right">{{ bidPrice
                            }}</strong>
                    </section>

                </main>
            </card>
        </section>

        <section class="col-12">
            <h1 class="card-title mt-4">Payment Details</h1>
            <card>
                <main class="row">
                    <section class="col-12 mb-3">
                        <label for="paymentType" class="">Payment Method Selected:</label>
                        <select v-model="selectedPayment" class="float-right form-control" name="paymentType"
                                id="paymentType">
                            <option value="creditCard">Credit Card</option>
                            <option value="cash">Cash</option>
                        </select>
                    </section>
                    <section ref="paymentInstructions" class="col-12 mb-2" v-if="selectedPayment === 'cash'">
                        <label for="paymentInstructions" class="">Payment Instructions:</label>
                        <input ref="paymentInstructionsMessage"
                               id="paymentInstructions"
                               class="float-right form-control"
                               v-model="payWithCashMessage">
                        <button class="btn btn-sm btn-primary float-right"
                                ref="paywithCashButton"
                                @click="submitPayWithCashMessage"
                        >Submit</button>
                    </section>
                </main>
            </card>
        </section>

        <section ref="job_address" class="col-12" v-if="showAddress">
            <h1 class="card-title mt-4">Job Address</h1>
            <card>
                <main class="map-responsive">
                    <iframe width="600" height="450" frameborder="0" style="border:0"
                            :src="'https://www.google.com/maps/embed/v1/search?q='+ bid.location.address_line_1 + ' ' + bid.location.city + ' ' + bid.location.state + ' ' + bid.location.zip +'&key=AIzaSyCI21pbEus0AZc4whkqwM3VaDO1YV1Dygs'"
                            allowfullscreen></iframe>
                </main>
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
            <h1 class="card-title mt-4">Special Instructions</h1>
            <card>
                <main class="row">
                    <section class="col-12">

                        <textarea name="notes" id="" cols="30" rows="10" class="form-control">

                        </textarea>

                        <button class="btn btn-default"
                                ref="update_customer_notes_button"
                                @click="updateGeneralContractorNotes"
                        >Submit</button>

                    </section>
                </main>
            </card>
        </section>

        <!-- / tasks -->
        <section class="col-12">
            <h1 class="card-title mt-4">Job Tasks</h1>
            <card @click.native="$router.push('/job/tasks')">
               {{  }} Total
                <span class="float-right" v-if="bid.job_tasks !== undefined">
          (<b ref="job_task_length">{{bid.job_tasks.length}}</b>)
        </span>
            </card>
        </section>

        <section class="col-12" v-if="!isCustomer" ref="add_new_task">
            <card class="mt-4" @click.native="$router.push('/job/add/task')">
                <main class="row">
                    <div class="col">
                        <p class="d-inline">Add New Task</p>
                        <i class="fas fa-chevron-right text-primary float-right sm-icon"></i>
                    </div>
                </main>
            </card>
        </section>

        <div class="col-12">
            <h1 class="card-title mt-4">Attachments</h1>
            <div class="mb-4">
                <img src="" alt="Attachments">
            </div>
        </div>

        <div class="col-12 mb-4">
            <card>
                <div class="row">
                    <div class="col">
                        <p class="d-inline">Upload Attachment</p>
                        <i class="fas fa-plus-circle text-primary float-right sm-icon"></i>
                    </div>
                </div>
            </card>
        </div>
    </div>
</template>

<script>
  import Info from '../shared/Info'
  import Format from '../../classes/Format'
  import Card from '../shared/Card'

  // import Customer from '../../classes/Customer'
  import { mapGetters, mapMutations, mapActions } from 'vuex'

  export default {
    components: {
      Card,
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
        payWithCashMessage: '',
        locationExists: false,
        customerInfo: false,
        paymentTypeCash: false,
        paymentTypeStripe: true,
        selectedPayment: 'creditCard'
      }
    },
    computed: {
      ...mapGetters(['getCustomerName']),
      agreedStartDate() {
        if (this.bid.agreed_start_date !== undefined && this.bid.agreed_start_date !== null) {
          let d = this.bid.agreed_start_date
          let date = d.split(' ')
          let format_date = date[0].split('-')
          return format_date[1] + '/' + format_date[2] + '/' + format_date[0]
        } else {
          return 'Not Set'
        }
      },
      bidPrice() {
        if (this.bid.status !== 'bid.initiated' && this.bid.status !== 'bid.in_progress') {
          return '$ ' + Format.decimal(this.bid.bid_price)
        } else {
          return 'In Process'
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
          this.bid.location !== null &&
          !this.isCustomer
        )
      }
    },
    methods: {
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
      submitPayWithCashMessage(){
        // TODO: update pay this method to update the pay with cash message in the back end
      },
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
        // console.log('user type: ' + User.isContractor())
        return this.area.area !== '' && !this.isCustomer
      },
      initializePayWithCashMessageValue() {
        console.log(this.bid.paid_with_cash_message)

        if (this.bid.paid_with_cash_message !== '') {

          console.log('I am true')
          console.log(this.bid.paid_with_cash_message)

          this.payWithCashMessage = this.bid.paid_with_cash_message
        }
      }
    },
    mounted() {
      this.initializePayWithCashMessageValue()
    }
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

    span {
        font-size: 15px;
    }

    /*@media (min-width: 762px) {*/
    /*.btn-width {*/
    /*width: 27%;*/
    /*}*/
    /*}*/
</style>