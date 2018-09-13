<template>
    <!-- /all details of a bid -->
    <div class="flex flex-col" v-if="bid.job_name !== undefined">

        <!-- JOB STATUS -->
        <div class="border-b pb-4 mb-6">
            <div class="status" :class="getLabelClass(bid.status)">
                {{ status }}
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
                <a target="_blank" v-if="showAddress" :href="'https://www.google.com/maps/search/?api=1&query=' + bid.location.address_line_1">
                    <address>
                        <br> {{ bid.location.address_line_1 }}
                        <br> {{ bid.location.city }}, {{ bid.location.state }} {{ bid.location.zip }}
                    </address>
                </a>
                <div v-else class="">
                    No Address is Set Yet
                </div>
            </div>
            <!-- <div class="flex flex-col">
                <span class="label mb-4">TOTAL PRICE:</span>
                <span>${{ bid.bid_price }}</span>
            </div> -->
        </div>

        <div class="flex justify-between mt-4">
            <span class="label mb-4">TOTAL PRICE:</span>
            <span class="font-bold">${{ bid.bid_price }}</span>
        </div>

        <!-- Customer Notes -->
        <!-- <button class="btn btn-blue" @click="customerInfo = !customerInfo">Customer Info</button>
        <div v-show="customerInfo" class="flex space-between flex-col">
            <p class="message">
                {{ bid.customer.customer.notes }}
            </p>
        </div> -->

        <!-- Declined Message -->
        <div class="flex space-between flex-col" v-if="showDeclinedMessage">
            <h4>
                <label class="status label label-warning red py-s">Declined Reason</label>
            </h4>
            <p class="message">
                {{ bid.declined_message}}
            </p>
        </div>
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
        locationExists: false,
        customerInfo: false
      }
    },
    computed: {
      ...mapGetters ([
        'getCustomerName'
      ]),
      agreedStartDate () {
        if (this.bid.agreed_start_date !== null) {
          let d = this.bid.agreed_start_date;
          let date = d.split (' ');
          let format_date = date[0].split ('-');
          return format_date[1] + '/' + format_date[2] + '/' + format_date[0];
        }
      },
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
      },
      showDeclinedMessage () {
          return !this.isCustomer && this.bid.declined_message !== null && this.bid.status === 'bid.declined';
      },
      showAddress () {
          return this.bid.location_id !== undefined && this.bid.location_id !== null && this.bid.location !== null;
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

<style lang="less" scoped>
.status {
    padding: 1rem;
    padding-left: 6px;
    padding-right: 6px;
}
</style>