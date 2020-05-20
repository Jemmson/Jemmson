<template>
    <div class="container">

        <!--        <pre>{{ invoice }}</pre>-->

        <v-card

        >
            <v-card-title class="w-break">{{ invoice ? invoice.job.job_name : '' }}</v-card-title>

            <v-card-text>

                <div v-if="invoice && invoice.user === 'customer'">
                    <v-simple-table>
                        <template v-slot:default>
                            <thead>
                            <tr>
                                <th class="text-left">Task Name</th>
                                <th class="text-left">Contractor</th>
                                <th class="text-left">Qty</th>
                                <th class="text-left">Price</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="item in invoice.job.job_tasks" :key="item.task_id">
                                <td>{{ item.task.name }}</td>
                                <td v-if="item.sub">{{ item.sub.company.company_name }}</td>
                                <td v-if="item.general">{{ item.general.company.company_name }}</td>
                                <td>{{ item.qty }}</td>
                                <td>{{ getUnitPrice(item) }}</td>
                            </tr>
                            </tbody>
                        </template>
                    </v-simple-table>
                    <hr>
                    <v-row align="center">
                        <v-card-subtitle>Total</v-card-subtitle>
                        <v-spacer></v-spacer>
                        <span class="mr-1rem">{{ getBidPrice(invoice) }}</span>
                    </v-row>
                </div>

                <div v-if="invoice && invoice.user === 'general'">
                    <v-simple-table>
                        <template v-slot:default>
                            <thead>
                            <tr>
                                <th class="text-left">Task Name</th>
                                <th class="text-left">Contractor</th>
                                <th class="text-left">Qty</th>
                                <th class="text-left">Price</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="item in invoice.job.job_tasks" :key="item.task_id">
                                <td>{{ item.task.name }}</td>
                                <td v-if="item.sub">{{ item.sub.company.company_name }}</td>
                                <td v-if="item.general">{{ item.general.company.company_name }}</td>
                                <td>{{ item.qty }}</td>
                                <td>{{ getUnitPrice(item)}}</td>
                            </tr>
                            </tbody>
                        </template>
                    </v-simple-table>
                    <hr>
                    <v-row align="center">
                        <v-card-subtitle>Total</v-card-subtitle>
                        <v-spacer></v-spacer>
                        <span class="mr-1rem">{{ getBidPrice(invoice) }}</span>
                    </v-row>
                </div>

                <div v-if="invoice && invoice.user === 'sub'">
                    <v-simple-table>
                        <template v-slot:default>
                            <thead>
                            <tr>
                                <th class="text-left">Task Name</th>
                                <th class="text-left">Contractor</th>
                                <th class="text-left">Qty</th>
                                <th class="text-left">Price</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="item in invoice.job.job_tasks" :key="item.task_id">
                                <td>{{ item.task.name }}</td>
                                <td v-if="item.sub">{{ item.sub.company.company_name }}</td>
                                <td v-if="item.general">{{ item.general.company.company_name }}</td>
                                <td>{{ item.qty }}</td>
                                <td>{{ getUnitPrice(item) }}</td>
                            </tr>
                            </tbody>
                        </template>
                    </v-simple-table>
                    <hr>
                    <v-row align="center">
                        <v-card-subtitle>Total</v-card-subtitle>
                        <v-spacer></v-spacer>
                        <span class="mr-1rem">{{ getBidPrice(invoice) }}</span>
                    </v-row>
                </div>

            </v-card-text>

        </v-card>

        <feedback
                page="invoice"
        ></feedback>
    </div>
</template>

<script>

    import Feedback from '../components/shared/Feedback'

    export default {
        components: {
            Feedback
        },
        props: {
            user: Object,
        },
        data() {
            return {
                invoice: null
            }
        },
        created() {
            document.body.scrollTop = 0 // For Safari
            document.documentElement.scrollTop = 0 // For Chrome, Firefox, IE and Opera
        },
        computed: {
            isContractor() {
                return User.isContractor()
            },
            //   totalCustomerPrice() {
            //     let total = 0
            //     if (this.invoice !== null) {
            //       for (const task of this.invoice.job_tasks) {
            //         total += (task.cust_final_price - task.sub_final_price)
            //       }
            //     }
            //     return total
            //   },
            //   totalSubPrice() {
            //     let total = 0
            //     if (this.invoice !== null) {
            //       for (const task of this.invoice.job_tasks) {
            //         total += task.sub_final_price
            //       }
            //     }
            //     return total
            //   }
        },
        methods: {
            getBidPrice(invoice) {
                return parseFloat(invoice.job.bid_price) / 100
            },
            getUnitPrice(item){
                return parseFloat(item.unit_price) / 100
            }
        },
        mounted: function () {
            this.$store.commit('setCurrentPage', '/invoice');
            const id = this.$route.params.id
            axios.get('/invoice/' + id).then((data) => {
                this.invoice = data.data
            })
        }
    }
</script>

<style>

</style>