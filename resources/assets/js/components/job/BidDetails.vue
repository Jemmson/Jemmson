<template>
    <!-- /all details of a bid -->
    <div v-if="bid.job_name !== undefined">

        <!-- JOB STATUS -->
        <div for="task-status" class="status" :class="getLabelClass(bid.status)">
            {{ status }}
        </div>

        <hr>

        <!-- CUSTOMER NAME -->
        <div>
            <h3 for="company_name" class="text-center" v-if="isCustomer">{{ bid.job_name }}</h3>
            <h3 for="company_name" class="text-center" v-else>{{ customerName }}</h3>
        </div>

        <!-- JOB NAME -->
        <div class="jobName">
            <span for="job_name">
                Job Name:
            </span>
            <span>
                {{ bid.job_name }}
            </span>
        </div>

        <!-- CUSTOMER ADDRESS -->
        <div>
            <span class="title">Address:</span>
            <a class="text-center" target="_blank" v-if="bid.location_id !== undefined && bid.location_id !== null"
               :href="'https://www.google.com/maps/search/?api=1&query=' + bid.location.address_line_1">
                <address v-if="bid.location !== null">
                    <br> {{ bid.location.address_line_1 }}
                    <br> {{ bid.location.city }}, {{ bid.location.state }} {{ bid.location.zip }}
                </address>
            </a>
            <div v-else class="text-center">
                No Address is Set Yet
            </div>
        </div>

        <!-- JOB TOTAL PRICE -->
        <div class="job-total" v-if="showBidPrice">
            <span class="title job-status-label">Total Job Price:</span>
            <span class="title-value text-center  job-status-value">${{ bid.bid_price }}</span>
        </div>


        <!-- Job Start Date -->
        <div>
            <label for="title">
                Start Date:
            </label>
            <p>
                {{ bid.agreed_start_date }}
            </p>
        </div>


        <!-- Declined Message -->
        <div class="col-xs-12" v-if="!isCustomer && bid.declined_message !== null && bid.status === 'bid.declined'">
            <h4>
                <label for="declined" class="label label-warning">Declined Reason</label>
            </h4>
            <p>
                <b>
                    {{ bid.declined_message}}
                </b>
            </p>
        </div>


        <!--<section class="col-xs-12 col-md-6">-->

            <!--<h3 for="company_name" v-if="isCustomer">{{ bid.job_name }}</h3>-->
            <!--<h3 for="company_name" v-else>{{ customerName }}</h3>-->

            <!--<a target="_blank" v-if="bid.location_id !== undefined && bid.location_id !== null"-->
               <!--:href="'https://www.google.com/maps/search/?api=1&query=' + bid.location.address_line_1">-->
                <!--<address v-if="bid.location !== null">-->
                    <!--<br> {{ bid.location.address_line_1 }}-->
                    <!--<br> {{ bid.location.city }}, {{ bid.location.state }} {{ bid.location.zip }}-->
                <!--</address>-->
            <!--</a>-->
        <!--</section>-->
        <!--<section class="col-xs-12 col-md-6">-->
            <!---->

            <!--<label for="title">-->
                <!--Start Date:-->
            <!--</label>-->
            <!--<p>-->
                <!--{{ bid.agreed_start_date }}-->
            <!--</p>-->
        <!--</section>-->



        <!-- JOB STATUS -->
        <!-- <div class="status">
            <span>{{ status }}</span>
        </div>
        <hr> -->
        <!-- JOB NAME -->
        <!-- <h3 class="text-center">{{ bid.job_name }}</h3>
        <hr> -->
        <!-- CUSTOMER NAME -->
        <!-- <div class="customer">
            <span style="display: none">{{ actCustomerName(bid.customer_id) }}</span>
            <span class="title">Customer Name:</span><span class="title-value">{{ bid.customer.name }}</span>
        </div>
        <hr> -->
        <!-- CUSTOMER ADDRESS -->
        <!-- <div>
            <span class="title">Address:</span>
            <a class="text-center" target="_blank" v-if="bid.location_id !== undefined && bid.location_id !== null"
               :href="'https://www.google.com/maps/search/?api=1&query=' + bid.location.address_line_1">
                <address>
                    <span>{{ bid.location.address_line_1 }}</span>
                    <br>
                    <span>{{ bid.location.city }}, {{ bid.location.state }} {{ bid.location.zip }}</span>
                </address>
            </a>
            <div v-else class="text-center">
                No Address is Set Yet
            </div>
        </div>
        <hr> -->
        <!-- JOB TOTAL PRICE -->
        <!-- <div class="job-total" v-if="showBidPrice">
            <span class="title job-status-label">Total Job Price:</span>
            <span class="title-value text-center  job-status-value">${{ bid.bid_price }}</span>
        </div> -->
    </div>
</template>

<script>
  import {mapGetters, mapMutations, mapActions} from 'vuex'

  export default {
    props: {
      bid: Object,
      isCustomer: Boolean,
      customerName: String
    },
    data () {
      return {
        area: {
          area: ''
        },
        areaError: '',
        locationExists: false
      }
    },
    computed: {
      ...mapGetters ([
        'getCustomerName'
      ]),
      showBidPrice () {
        if (User.isCustomer ()) {
          const status = this.bid.status;
          if (status !== 'bid.initiated' && status !== 'bid.in_progress') {
            return true;
          }
          return false;
        }
        return true;
      },
      status () {
        return User.status (this.bid.status, this.bid);
      }
    },
    methods: {
      getLabelClass (status) {
        return Format.statusLabel (status);
      },
      ...mapMutations ([
        'setCustomerName'
      ]),
      ...mapActions ([
        'actCustomerName'
      ]),
      updateArea () {
        // Customer.updateArea (this.area.area, this.bid.id);
      },
      showArea () {
        console.log ('user type: ' + User.isContractor ())
        return this.area.area !== '' && User.isContractor ();
      }
    },
    mounted: function () {
    }
  }
</script>

<style scoped>

    .jobName {
        display: flex;
        justify-content: space-between;
    }

    .customer {
        display: flex;
        justify-content: space-between;
    }

    .job-total {
        display: flex;
        justify-content: space-between;
    }

    .status {
        display: flex;
        justify-content: center;
        background-color: rgba(0, 128, 0, 0.34);
        border-radius: 10px;
        font-size: 2rem;
        padding: .5rem;
    }
</style>