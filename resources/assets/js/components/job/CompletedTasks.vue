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
                    <button class="btn btn-normal" @click.prevent="reopenTask(jobTask)" :disabled="disabled.reopen">
                            <span v-if="disabled.reopen">
                                <i class="fa fa-btn fa-spinner fa-spin"></i>
                            </span>
                        Reopen
                    </button>
                </div>
                <div class="flex-1 pl-1" v-else>
                    <button class="btn btn-normal" v-if="showDenyBtn(jobTask)" @click="openDenyTaskForm(jobTask)">
                        Deny
                    </button>
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
                <div class="flex-1">
                </div>
                <div class="flex-1">
                    <label v-if="isCustomer">Total: ${{ (totalCustomerPrice + totalSubPrice) -
                        subtractFromTotal}}</label>
                </div>
                <div class="flex-1" v-if="isContractor">
                    <label>Total: ${{ totalCustomerPrice + totalSubPrice }}</label>
                </div>
            </div>
        </div>


            <div v-if="isCustomer" class="text-right">
                <button class="btn btn-normal" @click.prevent="paidCash = true" :disabled="disabled.payCash">
                        <span v-if="disabled.payCash">
                            <i class="fa fa-btn fa-spinner fa-spin"></i>
                        </span>
                    Paid With Cash
                </button>
                <button class="btn btn-normal" @click.prevent="payAllPayableTasks()" :disabled="disabled.payAll">
                        <span v-if="disabled.payAll">
                            <i class="fa fa-btn fa-spinner fa-spin"></i>
                        </span>
                    Pay With Credit Card
                </button>
            </div>
            <transition name="slide-fade">
                <div v-show="paidCash">
                    <div class="form-group col-md-12">
                        <label for="">Message</label>
                        <input type="text" class="form-control"
                               name="message"
                               v-model="cashMessage"
                               placeholder="Optional Message">
                    </div>
                    <div class="form-group col-md-12">
                        <button
                                class="btn btn-normal"
                                @click.prevent="paidWithCash()"
                                :disabled="disableCashMessageButton"
                                ref="cashMessage">
                        <span v-if="disableCashMessageButton">
                            <i class="fa fa-btn fa-spinner fa-spin"></i>
                        </span>
                            Submit
                        </button>
                    </div>
                </div>
            </transition>

        <deny-task-modal v-if="isCustomer" :jobTask="jTask">
        </deny-task-modal>
    </div>
</template>

<script>

  import Card from '../shared/Card';
  import DenyTaskModal from '../task/DenyTaskModal';

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
        $('#deny-task-modal').modal()
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