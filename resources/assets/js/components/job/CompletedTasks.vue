<template>
    <!-- / all tasks ready for payment -->
    <div v-if="show"><!---->
        <div class="flex flex-col">
            <div class="flex space-between header-completed">
                <div class="flex-1">Task</div>
                <div class="flex-1">Qty</div>
                <div class="flex-1">Price</div>
                <div class="flex-1">Total</div>
                <div class="flex-1" v-if="isContractor">Task Price (Sub Contractor)</div>
                <div class="flex-1" v-else>Exclude</div>
                <div class="flex-1"></div>
            </div>
            <div class="flex pl-2 mb-2" v-for="jobTask in payableTasks" :key="jobTask.id">
                <div class="flex-1">{{ jobTask.task.name }}</div>
                <div class="flex-1 pl-1">{{ jobTask.qty }}</div>
                <div class="flex-1 pl-1">${{ jobTask.unit_price }}</div>
                <div class="flex-1 pl-1" v-if="isContractor">${{ jobTask.cust_final_price - jobTask.sub_final_price }}
                </div>
                <div class="flex-1 pl-1" v-else>${{ jobTask.cust_final_price }}</div>
                <div class="flex-1 pl-1" v-if="isContractor">${{ jobTask.sub_final_price }}</div>
                <div class="flex-1 pl-1" v-else>
                    <input type="checkbox" name="exclude" :id="'exclude-' + jobTask.id"
                           @click="addJobTaskToExcludedList(jobTask)">
                </div>
                <div class="flex-1 pl-1" v-if="showReopenBtn(jobTask)">
                    <v-btn
                            class="w-40"
                            color="primary"
                            @click.prevent="reopenTask(jobTask)" :disabled="disabled.reopen">
                            <span v-if="disabled.reopen">
                                <i class="fa fa-btn fa-spinner fa-spin"></i>
                            </span>
                        Reopen
                    </v-btn>
                </div>
                <div class="flex-1 pl-1" v-else>
                    <v-btn
                            class="w-40"
                            color="primary"
                            v-if="showDenyBtn(jobTask)" @click="openDenyTaskForm(jobTask)">
                        Deny
                    </v-btn>
                </div>
            </div>
            <div class="flex pl-2 mb-2" v-if="isContractor">
                <div></div>
                <div></div>
                <div>Total: ${{ totalCustomerPrice }}</div>
                <div>Total: ${{ totalSubPrice }}</div>
                <div></div>
            </div>
            <div class="flex pl-2 mb-2 mt-4">
                <div class="flex-1"></div>
                <div class="flex-1"></div>
                <div class="flex-1"></div>
                <div class="flex-1">
                    <!--                    <label v-if="isCustomer">Total: ${{ (totalCustomerPrice + totalSubPrice) - -->
                    <!--                        subtractFromTotal}}</label>-->
                    <label v-if="isCustomer">Total: ${{ totalPriceForAllCompletedTasks }}</label>
                </div>
                <div class="flex-1" v-if="isContractor">
                    <label>Total: ${{ totalCustomerPrice + totalSubPrice }}</label>
                </div>
            </div>
        </div>

        <div v-if="isCustomer" class="text-right">
            <v-btn
                    v-if="cashJobOrNotSetupWithStripe()"
                    class="w-full"
                    color="primary"
                    @click.prevent="paidCash = true" :disabled="disabled.payCash">
                        <span v-if="disabled.payCash">
                            <i class="fa fa-btn fa-spinner fa-spin"></i>
                        </span>
                Pay With Cash
            </v-btn>
            <v-btn
                    class="w-full"
                    color="primary"
                    v-if="creditCardJobAndContractorHasStripe()" @click.prevent="payAllPayableTasks()"
                    :disabled="disabled.payAll">
                        <span v-if="disabled.payAll">
                            <i class="fa fa-btn fa-spinner fa-spin"></i>
                        </span>
                Pay With Credit Card
            </v-btn>
        </div>
        <transition name="slide-fade">
            <div v-show="paidCash">
                <div class="form-group col-md-12">

                    <v-combobox
                            label="Instructions For Contractor"
                            :items="instructions"
                            v-model="cashMessage"
                    ></v-combobox>

                    <!--                    <label for="">Message</label>-->
                    <!--                    <input type="text" class="form-control"-->
                    <!--                           name="message"-->
                    <!--                           v-model="cashMessage"-->
                    <!--                           placeholder="Optional Message">-->
                </div>
                <div class="form-group col-md-12">
                    <v-btn
                            class="w-40"
                            color="primary"
                            @click.prevent="paidWithCash()"
                            :disabled="disableCashMessageButton"
                            ref="cashMessage">
                        <span v-if="disableCashMessageButton">
                            <i class="fa fa-btn fa-spinner fa-spin"></i>
                        </span>
                        Submit
                    </v-btn>
                </div>
            </div>
        </transition>

        <deny-task-modal
                v-if="isCustomer"
                :jobTask="jTask">
        </deny-task-modal>
    </div>
</template>

<script>

  import Card from '../shared/Card'
  import DenyTaskModal from '../task/DenyTaskModal'

  export default {
    props: {
      bid: Object
    },
    components: {
      Card,
      DenyTaskModal
    },
    data() {
      return {
        instructions: [
          'Pay In Person',
          'Left Under The Mat',
          'Check Is In The Mail'
        ],
        jTask: {},
        excluded: {},
        showPaidInCash: false,
        subtractFromTotal: 0,
        cashMessage: 'Pay In Person',
        paidCash: false,
        disableCashMessageButton: false,
        disabled: {
          payAll: false,
          reopen: false,
          payCash: false,
        }
      }
    },
    computed: {
      totalPriceForAllCompletedTasks() {
        let total = 0
        let payableT = this.payableTasks
        for (let i = 0; i < payableT.length; i++) {
          total = total + payableT[i].cust_final_price
        }
        return total
      },
      totalCustomerPrice() {
        let total = 0
        if (this.payableTasks !== null) {
          for (const task of this.payableTasks) {
            total += (task.cust_final_price - task.sub_final_price)
          }
        }
        return total
      },
      totalSubPrice() {
        let total = 0
        if (this.payableTasks !== null) {
          for (const task of this.payableTasks) {
            total += task.sub_final_price
          }
        }
        return total
      },
      jobTasks() {
        return this.bid.job_tasks
      },
      payableTasks() {
        return User.getAllPayableTasks(this.jobTasks)
      },
      showPayWithStripeBtn() {
        if (this.payableTasks.length > 0) {
          return User.stripePaymentRequested(this.payableTasks)
        }
        return false
      },
      show() {
        return this.payableTasks.length > 0
      },
      isContractor() {
        return User.isContractor()
      },
      isCustomer() {
        return User.isCustomer()
      }
    },
    methods: {

      cashJobOrNotSetupWithStripe() {
        return this.cashJob() || this.needsStripe()
      },

      creditCardJobAndContractorHasStripe(){
        return this.creditCardJob() && this.hasStripe()
      },

      needsStripe() {
        if (this.bid) {
          return this.bid.contractor.contractor.stripe_id === false
        }
      },

      hasStripe(){
        if (this.bid) {
          return this.bid.contractor.contractor.stripe_id !== false
        }
      },

      creditCardJob(){
        return this.bid.payment_type === 'creditCard'
      },

      cashJob() {
        return this.bid.payment_type === 'cash'
      },

      contractorIsSetupWithStripe() {
        return this.bid.contractor.stripe_id
      },
      showDenyBtn(jobTask) {
        const status = jobTask.status
        if (this.isCustomer) {
          return (status === 'bid_task.finished_by_general' || status === 'bid_task.approved_by_general')
        }
        return status === 'bid_task.finished_by_sub'
      },
      showReopenBtn(jobTask) {
        if (this.isContractor && (jobTask.status === 'bid_task.finished_by_general' || jobTask.status ===
          'bid_task.approved_by_general')) {
          return true
        }
        return false
      },
      showPayCashForTaskBtn(jobTask) {
        return (jobTask.status === 'bid_task.finished_by_general' || jobTask.status ===
          'bid_task.approved_by_general') &&
          User.isCustomer()
      },
      addJobTaskToExcludedList(jobTask) {
        if (document.getElementById('exclude-' + jobTask.id).checked) {
          this.excluded[jobTask.id] = true
          this.subtractFromTotal += jobTask.cust_final_price
        } else {
          this.excluded[jobTask.id] = false
          this.subtractFromTotal -= jobTask.cust_final_price
        }
      },
      openDenyTaskForm(jobTask) {
        this.jTask = jobTask
        $('#deny-task-modal_' + jobTask.id).modal()
      },

      paidWithCash() {
        // show message box to ask how contractor can pick up the cash
        // in person is an option
        // dropdown menu as well

        // this.paidCash = !this.paidCash;
        Customer.payAllPayableTasksWithCash(
          this.bid.id, this.excluded, this.disabled, this.cashMessage)
      },
      reopenTask(jobTask) {
        SubContractor.reopenTask(jobTask, this.disabled)
      },
      payAllPayableTasks() {
        Customer.payAllPayableTasks(this.bid.id, this.excluded, this.disabled)
      }
    },
    mounted() {}
  }
</script>

<style scoped>
    .header-completed {
        font-size: 14pt;
        background-color: #80808054;
        border-radius: 4px;
        padding-left: 11px;
        margin-bottom: 10px;
        font-weight: bold;
    }
</style>