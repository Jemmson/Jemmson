<template>
    <!-- /all details of a bid -->
    <div class="flex flex-col" v-if="bid.job_name !== undefined">

        <!-- JOB STATUS -->
        <div class="border-b pb-4 mb-6">
            <div class="status" :class="getLabelClass(bid.status)">
                {{ status }}
                <i class="fa 
                          fa-question-circle 
                          bid-status-info
                          bid-status-shadow
                          " aria-hidden="true"></i>
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
                <a target="_blank" v-if="showAddress"
                   :href="'https://www.google.com/maps/search/?api=1&query=' + bid.location.address_line_1">
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

        
                <div class="flex justify-around">
                     <button class="btn btn-blue btn-width mr-6" name="showNotes" id="showNotes"
                        @click="customerNotes = !customerNotes">
                        Customer Notes For Job
                    </button>
                    <button v-show="bid.paid_with_cash_message !== ''" class="btn btn-blue btn-width ml-6" name="showPaidWithCashNotes" id="showPaidWithCashNotes"
                        @click="showPaidWithCashNotes = !showPaidWithCashNotes">
                        Paid With Cash Instructions
                    </button>
                </div>

                <div class="wrapper">
                    <div>
                        <div v-show="!isCustomer" class="">
                            <div v-if="bid.customer.customer.notes !== ''" class="" style="">
                                <transition name="slide-fade">
                                    <div class="mt-3 mr-6" v-show="customerNotes">{{ bid.customer.customer.notes }}</div>
                                </transition>
                            </div>
                            <div v-else>
                                <transition name="slide-fade">
                                    <div class="mt-3" v-show="customerNotes">The customer does not have any notes for
                                        this job
                                    </div>
                                </transition>
                            </div>
                        </div>
                        <div v-show="isCustomer" class="">
                            <transition name="slide-fade">
                                <div v-show="customerNotes">
                                    <div class="mt-4">
                                        <textarea
                                                class="form-control"
                                                :value="bid.customer.customer.notes"
                                                name=""
                                                id=""
                                                cols="40"
                                                rows="10"
                                                @keyup="customerNotesMessage = $event.target.value"
                                        >

                                        </textarea>
                                        <!--<textarea type="text" class="form-control"-->
                                        <!--name="message"-->
                                        <!--:value="bid.customer.customer.notes"-->
                                        <!--placeholder="Optional Message">-->
                                    </div>
                                    <div class="mt-2">
                                        <button class="btn btn-red"
                                                @click.prevent="updateGeneralContractorNotes()"
                                                :disabled="disableCustomerNotesButton"
                                                ref="custNotesUpdate">
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
                    <div v-show="bid.paid_with_cash_message !== ''" class="">
                        <transition name="slide-fade">
                            <div class="mt-3 ml-6" v-show="showPaidWithCashNotes">{{ bid.paid_with_cash_message }}</div>
                        </transition>
                    </div>
                </div>



        <div class="flex justify-between mt-4">
            <span class="label mb-4">TOTAL PRICE:</span>
            <div v-if="!isCustomer">
                <span class="font-bold">${{ bid.bid_price }}</span>
            </div>
            <div v-else>
                <span v-if="bid.status !== 'bid.in_progress' && bid.status !== 'bid.initiated' "  class="font-bold">${{ bid.bid_price }}</span>
                <span v-else class="font-bold"><i>PENDING</i></span>
            </div>
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
import { mapGetters, mapMutations, mapActions } from "vuex";

export default {
  props: {
    bid: Object,
    isCustomer: Boolean,
    customerName: String
  },
  data() {
    return {
      area: {
        area: ""
      },
      customerNotesMessage: "",
      showPaidWithCashNotes: false,
      disableCustomerNotesButton: false,
      customerNotes: false,
      areaError: "",
      locationExists: false,
      customerInfo: false
    };
  },
  computed: {
    ...mapGetters(["getCustomerName"]),
    agreedStartDate() {
      if (this.bid.agreed_start_date !== null) {
        let d = this.bid.agreed_start_date;
        let date = d.split(" ");
        let format_date = date[0].split("-");
        return format_date[1] + "/" + format_date[2] + "/" + format_date[0];
      }
    },
    showBidPrice() {
      if (User.isCustomer()) {
        const status = this.bid.status;
        if (status !== "bid.initiated" && status !== "bid.in_progress") {
          return true;
        }
        return false;
      }
      return true;
    },
    status() {
      return User.status(this.bid.status, this.bid);
    },
    showDeclinedMessage() {
      return (
        !this.isCustomer &&
        this.bid.declined_message !== null &&
        this.bid.status === "bid.declined"
      );
    },
    showAddress() {
      return (
        this.bid.location_id !== undefined &&
        this.bid.location_id !== null &&
        this.bid.location !== null
      );
    }
  },
  methods: {
    getLabelClass(status) {
      return Format.statusLabel(status);
    },
    ...mapMutations(["setCustomerName"]),
    ...mapActions(["actCustomerName"]),
    updateGeneralContractorNotes() {
      Customer.updateNotesForJob(
        this.customerNotesMessage,
        this.bid.customer_id
      );
    },
    updateArea() {
      // Customer.updateArea (this.area.area, this.bid.id);
    },
    showArea() {
      console.log("user type: " + User.isContractor());
      return this.area.area !== "" && User.isContractor();
    }
  },
  mounted: function() {}
};
</script>

<style lang="less" scoped>

// .bid-status-info {
//     border: white thin solid;
// }

@media (min-width: 450px) {
  .bid-status-info {
    margin-left: 0.5rem;
  }
}

@media (min-width: 620px) {
  .bid-status-info {
    margin-left: 0.75rem;
  }
}

// .bid-status-shadow {
//   box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.12), 0 2px 4px 0 rgba(0, 0, 0, 0.08);
// }

// .bid-status-shadow:active {
//   box-shadow: 0 15px 30px 0 rgba(0, 0, 0, 0.11),
//     0 5px 15px 0 rgba(0, 0, 0, 0.08);
// }

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
  padding: 1rem;
  padding-left: 6px;
  padding-right: 6px;
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