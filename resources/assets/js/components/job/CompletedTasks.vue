<template>
    <!-- / all tasks ready for payment -->
    <div class="col-md-12" v-if="show">
        <div class="panel panel-default">
            <div class="panel-heading">
                Payable Tasks
            </div>
            <div class="panel-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Task Name</th>
                            <th scope="col"></th>
                            <th scope="col">Task Price</th>
                            <th scope="col" v-if="isContractor">Task Price (Sub Contractor)</th>
                            <th scope="col" v-else></th>
                            <th scope="col" v-if="isContractor"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="task in jobTasks" :key="task.id">
                            <td>{{ task.task.name }}</td>
                            <td></td>
                            <td v-if="isContractor">${{ task.cust_final_price - task.sub_final_price }}</td>
                            <td v-else>${{ task.cust_final_price }}</td>
                            <td v-if="isContractor">${{ task.sub_final_price }}</td>
                            <td v-else>
                                <button class="btn btn-danger">Decline</button>
                            </td>
                            <td v-if="isContractor">
                                <button class="btn btn-warning">Reopen</button>
                            </td>
                            
                        </tr>

                        <tr>
                            <td></td>
                            <td></td>
                            <td>Total: ${{ totalCustomerPrice }}</td>
                            <td v-if="isContractor">Total: ${{ totalSubPrice }}</td>
                            <td v-else></td>
                            <td v-if="isContractor"></td>
                        </tr>
                        <tr v-if="isContractor">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>
                                <label>Total: ${{ totalCustomerPrice + totalSubPrice }}</label>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="panel-footer">
                <div v-if="isCustomer" class="text-right">
                    <button class="btn btn-success">Paid With Cash</button>
                    <button class="btn btn-success">Pay With Stripe</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            bid: Object
        },
        data() {
            return {
                disabled: {
                    payAll: false,
                }
            }
        },
        computed: {
             totalCustomerPrice() {
                let total = 0;
                if (this.payableTasks !== null) {
                    for (const task of this.payableTasks) {
                        total += task.cust_final_price - task.sub_final_price;
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
            payAllPayableTasks() {
                Customer.payAllPayableTasks(bid, disabled);
            }
        },
        mounted() {
        }
    }
</script>