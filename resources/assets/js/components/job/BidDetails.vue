<template>
    <!-- /all details of a bid -->
    <div v-if="bid.job_name !== undefined">

        <!-- JOB STATUS -->
        <div class="status">
            <span>{{ status }}</span>
        </div>

        <!-- JOB NAME -->
        <h3 class="text-center">{{ bid.job_name }}</h3>

        <!-- CUSTOMER NAME -->
        <div class="customer">
            <span style="display: none">{{ actCustomerName(bid.customer_id) }}</span>
            <span class="title">Customer Name:</span><span class="title-value">{{ bid.customer.name }}</span>
        </div>

        <!-- CUSTOMER ADDRESS -->
        <div>
            <span class="title">Address:</span>
            <a class="text-center" target="_blank" v-if="bid.location_id !== undefined && bid.location_id !== null"
               :href="'https://www.google.com/maps/search/?api=1&query=' + bid.location.address_line_1">
                <address>
                    <span>{{ bid.location.address_line_1 }}</span>
                    <br>
                    <span>{{ bid.location.city }}, {{ bid.location.state }} {{ bid.location.zip }}</span>
                </address>
            </a>
            <div v-else>
                No Address is Set Yet
            </div>
        </div>
        
        <!-- JOB TOTAL PRICE -->
        <div class="job-total" v-if="showBidPrice">
            <span class="title job-status-label">Total Job Price:</span>
            <span class="title-value text-center  job-status-value">${{ bid.bid_price }}</span>
        </div>
    </div>
</template>

<script>
  import {mapGetters, mapMutations, mapActions} from 'vuex'

  export default {
    props: {
      bid: Object
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