<template>
    <!-- /all details of a bid -->
    <div class="job-main-wrapper" v-if="bid.job_name !== undefined">
        <div class="job-main-row job-main-header">
            <span class="title-value text-center">{{ bid.job_name }}</span>
            <span style="display: none">{{ actCustomerName(bid.customer_id) }}</span>
            <div>
                <span class="title">Customer Name:</span><span class="title-value">{{ bid.customer.name }}</span>
            </div>
        </div>
        <div class="job-main-row job-main-address">
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
            <!--<span class="title-value text-center">{{ bid.job_name }}</span>-->
        </div>
        <div class="job-main-row job-main-status">
            <div class="job-status">
                <span class="title job-status-label">Status:</span>
                <span class="title-value text-center  job-status-value">{{ status }}</span>
            </div>
            <div class="job-status" v-if="showBidPrice">
                <span class="title job-status-label">Total Job Price:</span>
                <span class="title-value text-center  job-status-value">${{ bid.bid_price }}</span>
            </div>
        </div>
    </div>
</template>

<script>
  import { mapGetters, mapMutations, mapActions } from 'vuex'
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
      ...mapGetters([
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
      ...mapMutations([
          'setCustomerName'
      ]),
      ...mapActions([
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
    .job-main-wrapper {
        display: grid;
        grid-template-rows: repeat(3, 1fr);
    }

    .job-main-header {
        background-color: #eee;
        display: grid;
    }

    .job-main-address {
        background-color: white;
        display: grid;
    }

    .job-main-row {
        border-radius: 4px;
    }

    .job-main-status {
        background-color: #eee;
        display: grid;
    }

    .title {
        padding-top: 1rem;
        padding-left: 1rem;
    }

    .title-value {
        padding-right: 1rem;
        padding-bottom: 1rem;
        padding-left: 1rem;
        font-size: 2rem;
        float: right;
    }

    .job-status {
        display: grid;
        grid-template-columns: 1fr 1fr;
    }

    .job-status-label {
    }

    .job-status-value {
        margin-left: auto;
    }
</style>