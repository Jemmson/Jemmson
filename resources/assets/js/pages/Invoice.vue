<template>
    <div class="flex flex-col">
        <card>
            <h1 class="self-center">Invoice Page</h1>
        </card>
        <div v-if="invoice !== null" ref="invoice-details">
            <card>
                <section class="flex flex-col">
                    <h3 for="company_name" v-if="isContractor" class="mb-4">{{ user.contractor.company_name }}</h3>
                    <h3 for="company_name" v-if="!isContractor" class="mb-4">{{ user.name }}</h3>
                    <div class="flex justify-between">
                        <div>

                        <label for="job_name" class="label">
                            Job Name:
                        </label>
                        <p>
                            {{ invoice.job_name }}
                        </p>
                        </div>

                        <div>
                        <label for="title" class="label">
                            Date Completed:
                        </label>
                        <p>
                            {{ invoice.updated_at }}
                        </p>
                        </div>            
                    </div>
                    <address v-if="invoice.location !== null">
                        <br> {{ invoice.location.address_line_1 }}
                        <br> {{ invoice.location.city }}, {{ invoice.location.state }} {{ invoice.location.zip }}
                    </address>
                </section>
            </card>

            <card>
                <table class="table-auto">
                    <thead>
                        <tr>
                            <th scope="col">Task Name</th>
                            <th scope="col">QTY</th>
                            <th scope="col">Task Price</th>
                            <th scope="col" v-if="isContractor">Task Price (Sub Contractor)</th>

                        </tr>
                    </thead>
                    <tbody v-if="invoice.job_tasks.length > 0">
                        <tr v-for="task in invoice.job_tasks" :key="task.id">
                            <td>{{ task.task.name }}</td>
                            <td>{{ task.qty }}</td>
                            <td v-if="isContractor">${{ task.cust_final_price - task.sub_final_price }}</td>
                            <td v-else>${{ task.cust_final_price }}</td>
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
                            <td>
                                <label>Total: ${{ totalCustomerPrice + totalSubPrice }}</label>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </card>
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
                        total += (task.cust_final_price - task.sub_final_price);
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