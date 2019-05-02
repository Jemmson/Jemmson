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

    <div class="col-12">
      <h1 class="card-title">Details</h1>
      <card>
        <div class="row">
          <div class="col-12 mb-3">
            <span class="">Customer Name:</span>
            <span v-if="isCustomer" class="float-right">{{ customerName }}</span>
            <span v-else class="float-right">{{ customerName }}</span>
          </div>
          <div class="col-12 mb-3">
            <span class="">Start Date:</span>
            <span class="float-right">{{ agreedStartDate }}</span>
          </div>
          <div class="col-12 mb-3">
            <span class="">Contractor Suggested Price:</span>
            <span class="float-right">${{ bid.bid_price }}</span>
          </div>
          <div class="col-12 mb-2">
            <span class="">Accepted Price:</span>
            <span class="float-right">${{ bid.bid_price + 50 }}</span>
          </div>
        </div>
      </card>
    </div>

    <div class="col-12">
      <h1 class="card-title mt-4">Payment Details</h1>
      <card>
        <div class="row">
          <div class="col-12 mb-3">
            <span class="">Payment Method Selected:</span>
            <span class="float-right">{{ 'Stripe' }}</span>
          </div>
          <div class="col-12 mb-2">
            <span class="">Payment Instructions:</span>
            <span class="float-right">{{ 'Pay in Person' }}</span>
          </div>
        </div>
      </card>
    </div>

    <div class="col-12" v-if="showAddress">
      <h1 class="card-title mt-4">Job Address</h1>
      <card>
        <div class="map-responsive">
        <iframe width="600" height="450" frameborder="0" style="border:0"
:src="'https://www.google.com/maps/embed/v1/search?q='+ bid.location.address_line_1 + ' ' + bid.location.city + ' ' + bid.location.state + ' ' + bid.location.zip +'&key=AIzaSyCI21pbEus0AZc4whkqwM3VaDO1YV1Dygs'" allowfullscreen></iframe>
        </div>  
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
    </div>

    <div class="col-12">
      <h1 class="card-title mt-4">Special Instructions</h1>
      <card>
        <div class="row">
          <div class="col-12">
            <span>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam commodo orci vitae arcu mattis, 
            quis efficitur erat aliquam. Aenean commodo sapien sed ipsum fermentum rutrum. Donec congue, arcu eu interdum.
            </span>
          </div>
        </div>
      </card>
    </div>

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
        if (this.bid.agreed_start_date !== undefined && this.bid.agreed_start_date !== null) {
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

    span {
      font-size: 15px;
    }

    /*@media (min-width: 762px) {*/
        /*.btn-width {*/
            /*width: 27%;*/
        /*}*/
    /*}*/
</style>