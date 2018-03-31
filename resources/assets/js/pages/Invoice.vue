<template>
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h1>Invoice Page</h1>
                    </div>
                </div>
            </div>
            <div v-if="invoice !== null">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <section class="col-xs-12 col-md-6">
                                <h3 for="company_name" v-if="isContractor">{{ user.contractor.company_name }}</h3>
                                <h3 for="company_name" v-if="!isContractor">{{ user.name }}</h3>
                                <address v-if="invoice.location !== null">
                                    <br> {{ invoice.location.address_line_1 }}
                                    <br> {{ invoice.location.city }}, {{ invoice.location.state }} {{ invoice.location.zip }}
                                </address>
                            </section>
                            <section class="col-xs-12 col-md-6">
                                <label for="job_name">
                                    Job Name:
                                </label>
                                <p>
                                    {{ invoice.job_name }}
                                </p>

                                <label for="title">
                                    Date Completed:
                                </label>
                                <p>
                                    {{ invoice.updated_at }}
                                </p>
                            </section>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">Task Name</th>
                                        <th scope="col"></th>
                                        <th scope="col">Task Price (Contractor)</th>
                                        <th scope="col" v-if="isContractor">Task Price (Sub Contractor)</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="task in invoice.job_tasks" :key="task.id">
                                        <td>{{ task.task.name }}</td>
                                        <td></td>
                                        <td>${{ task.cust_final_price - task.sub_final_price }}</td>
                                        <td v-if="isContractor">${{ task.sub_final_price }}</td>
                                    </tr>

                                    <tr v-if="isContractor">
                                        <td></td>
                                        <td></td>
                                        <td>Total: ${{ totalCustomerPrice }}</td>
                                        <td>Total: ${{ totalSubPrice }}</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td v-if="isContractor"></td>
                                        <td><label>Total: ${{ totalCustomerPrice + totalSubPrice }}</label></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            user: Object,
        },
        data() {
            return {
                invoice: null
            }
        },
        computed: {
            isContractor() {
                return User.isContractor();
            },
            totalCustomerPrice() {
                let total = 0;
                if (this.invoice !== null) {
                    for (const task of this.invoice.job_tasks) {
                        total += task.cust_final_price - task.sub_final_price;
                    }
                }
                return total;
            },
            totalSubPrice() {
                let total = 0;
                if (this.invoice !== null) {
                    for (const task of this.invoice.job_tasks) {
                        total += task.sub_final_price;
                    }
                }
                return total;
            }
        },
        methods: {},
        mounted: function () {
            const id = this.$route.params.id;
            axios.get('/invoice/' + id).then((data) => {
                this.invoice = data.data;
            });
        }
    }
</script>