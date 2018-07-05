<template>
    <!-- /all details of a bid -->

    <div>
        <div v-if="bid.job_name !== undefined">

            <!-- JOB STATUS -->
            <div for="task-status" class="status green py-s" :class="getLabelClass(bid.status)">
                {{ status }}
            </div>

            <hr>

            <!-- CUSTOMER NAME -->
            <div>
                <h3 for="company_name" class="text-center" v-if="isCustomer">{{ bid.job_name }}</h3>
                <h3 for="company_name" class="text-center" v-else>{{ customerName }}</h3>
            </div>

            <!-- JOB NAME -->
            <div class="flex space-between">
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
            <div class="flex space-between" v-if="showBidPrice">
                <span class="title job-status-label">Total Job Price:</span>
                <span class="title-value text-center  job-status-value">${{ bid.bid_price }}</span>
            </div>


            <!-- Job Start Date -->
            <div class="flex space-between">
                <label>
                    Start Date:
                </label>
                <p>
                    {{ agreedStartDate }}
                </p>
            </div>


            <!-- Declined Message -->
            <div class="flex space-between flex-col"
                 v-if="!isCustomer && bid.declined_message !== null && bid.status === 'bid.declined'">
                <h4>
                    <label class="status label label-warning red py-s">Declined Reason</label>
                </h4>
                <p class="message">
                    {{ bid.declined_message}}
                </p>
            </div>
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
      }
    },
    computed: {
      ...mapGetters ([
        'getCustomerName'
      ]),
      agreedStartDate () {
        if (this.bid.agreed_start_date !== null) {
          let d = this.bid.agreed_start_date;
          let date = d.split(" ");
          let format_date = date[0].split("-");
          return format_date[1]+"/"+format_date[2]+"/"+format_date[0];
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

    .flex {
        display: flex;
    }

    .space-between {
        justify-content: space-between;
    }

    .green {
        background-color: rgba(0, 128, 0, 0.34);
    }

    .red {
        background-color: rgba(255, 64, 47, 0.78);
    }

    .py-l {
        padding-top: 3rem;
        padding-bottom: 3rem;
    }

    .py-m {
        padding-top: 2rem;
        padding-bottom: 2rem;
    }

    .py-s {
        padding-top: 1rem;
        padding-bottom: 1rem;
    }

    .px-l {
        padding-right: 3rem;
        padding-left: 3rem;
    }

    .px-m {
        padding-right: 2rem;
        padding-left: 2rem;
    }

    .px-s {
        padding-right: 1rem;
        padding-left: 1rem;
    }

    .status {
        display: flex;
        justify-content: center;
        border-radius: 10px;
        font-size: 2rem;
    }

    .message {
        font-size: 2.25rem;
        font-weight: bold;
    }

    .flex-col {
        flex-direction: column;
    }
</style>