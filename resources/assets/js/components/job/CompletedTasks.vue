<template>
    <!-- / all tasks ready for payment -->
    <div class="col-md-12" v-if="show">
        <div class="card card-1">
            <div class="panel-heading">
                Payable Tasks
            </div>
            <div class="panel-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Task Name</th>
                            <th scope="col">QTY</th>
                            <th scope="col">Task Price</th>
                            <th scope="col" v-if="isContractor">Task Price (Sub Contractor)</th>
                            <th scope="col" v-else>Exclude From Payment</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="jobTask in payableTasks" :key="jobTask.id">
                            <td>{{ jobTask.task.name }}</td>
                            <td>{{ jobTask.qty }}</td>
                            <td v-if="isContractor">${{ jobTask.cust_final_price - jobTask.sub_final_price }}</td>
                            <td v-else>${{ jobTask.cust_final_price }}</td>
                            <td v-if="isContractor">${{ jobTask.sub_final_price }}</td>
                            <td v-else>
                                <input type="checkbox" name="exclude" :id="'exclude-' + jobTask.id" @click="addJobTaskToExcludedList(jobTask)">
                            </td>
                            <td v-if="showReopenBtn(jobTask)">
                                <button class="btn-yellow" @click.prevent="reopenTask(jobTask)" :disabled="disabled.reopen">
                                    <span v-if="disabled.reopen">
                                        <i class="fa fa-btn fa-spinner fa-spin"></i>
                                    </span>
                                    Reopen
                                </button>
                            </td>
                            <td v-else>
                                <button class="btn-blue" v-if="showDenyBtn(jobTask)" @click="openDenyTaskForm(jobTask)">
                                    Deny
                                </button>
                            </td>
                        </tr>

                        <tr v-if="isContractor">
                            <td></td>
                            <td></td>
                            <td>Total: ${{ totalCustomerPrice }}</td>
                            <td>Total: ${{ totalSubPrice }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td>
                                <label v-if="isCustomer">Total: ${{ (totalCustomerPrice + totalSubPrice) - subtractFromTotal}}</label>
                            </td>
                            <td></td>
                            <td v-if="isContractor">
                                <label>Total: ${{ totalCustomerPrice + totalSubPrice }}</label>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="panel-footer">
                <div v-if="isCustomer" class="text-right">
                    <button class="btn-green" @click.prevent="paidWithCash()" :disabled="disabled.payCash">
                        <span v-if="disabled.payCash">
                            <i class="fa fa-btn fa-spinner fa-spin"></i>
                        </span>
                        Paid With Cash
                    </button>
                    <button class="btn-green" @click.prevent="payAllPayableTasks()" :disabled="disabled.payAll">
                        <span v-if="disabled.payAll">
                            <i class="fa fa-btn fa-spinner fa-spin"></i>
                        </span>
                        Pay With Stripe
                    </button>
                </div>
            </div>
        </div>
            <deny-task-modal v-if="isCustomer" :jobTask="jTask">
            </deny-task-modal>
    </div>
</template>

<script>
    export default {
        props: {
            bid: Object
        },
        data() {
            return {
                jTask: {},
                excluded: {},
                subtractFromTotal: 0,
                disabled: {
                    payAll: false,
                    reopen: false,
                    payCash: false,
                }
            }
        },
        computed: {
            totalCustomerPrice() {
                let total = 0;
                if (this.payableTasks !== null) {
                    for (const task of this.payableTasks) {
                        total += (task.cust_final_price - task.sub_final_price);
                    }
                }
                return total;
            },
            totalSubPrice() {
                let total = 0;
                if (this.payableTasks !== null) {
                    for (const task of this.payableTasks) {
                        total += task.sub_final_price;
                    }
                }
                return total;
            },
            jobTasks() {
                return this.bid.job_tasks;
            },
            payableTasks() {
                return User.getAllPayableTasks(this.jobTasks);
            },
            showPayWithStripeBtn() {
                if (this.payableTasks.length > 0) {
                    return User.stripePaymentRequested(this.payableTasks);
                }
                return false;
            },
            show() {
                return this.payableTasks.length > 0;
            },
            isContractor() {
                return User.isContractor();
            },
            isCustomer() {
                return User.isCustomer();
            }
        },
        methods: {
            showDenyBtn(jobTask) {
                const status = jobTask.status;
                if (this.isCustomer) {
                    return (status === 'bid_task.finished_by_general' || status === 'bid_task.approved_by_general');
                }
                return status === 'bid_task.finished_by_sub';
            },
            showReopenBtn(jobTask) {
                if (this.isContractor && (jobTask.status === 'bid_task.finished_by_general' || jobTask.status ===
                        'bid_task.approved_by_general')) {
                    return true;
                }
                return false;
            },
            showPayCashForTaskBtn(jobTask) {
                return (jobTask.status === 'bid_task.finished_by_general' || jobTask.status ===
                        'bid_task.approved_by_general') &&
                    User.isCustomer();
            },
            addJobTaskToExcludedList(jobTask) {
                if (document.getElementById('exclude-' + jobTask.id).checked) {
                    this.excluded[jobTask.id] = true;
                    this.subtractFromTotal += jobTask.cust_final_price;
                } else {
                    this.excluded[jobTask.id] = false;
                    this.subtractFromTotal -= jobTask.cust_final_price;
                }
            },
            openDenyTaskForm(jobTask) {
                this.jTask = jobTask;
                $('#deny-task-modal').modal();
            },
            paidWithCash() {
                Customer.payAllPayableTasksWithCash(this.bid.id, this.excluded, this.disabled);
            },
            reopenTask(jobTask) {
                SubContractor.reopenTask(jobTask, this.disabled);
            },
            payAllPayableTasks() {
                Customer.payAllPayableTasks(this.bid.id, this.excluded, this.disabled);
            }
        },
        mounted() {}
    }
</script>