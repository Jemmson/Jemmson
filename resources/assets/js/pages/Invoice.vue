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
                            <label class="label">
                                Cash Payment Instructions
                            </label>
                            <p>
                                {{ invoice.paid_with_cash_message }}
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
                <div class="flex flex-col">
                    <div class="flex justify-between">
                        <div class="invoice-item invoice-header-item">Task Name</div>
                        <div class="invoice-item invoice-header-item">QTY</div>
                        <div class="invoice-item invoice-header-item">Task Price</div>
                        <div class="invoice-item invoice-header-item" v-if="isContractor">Task Price (Sub)</div>
                        <div class="invoice-item invoice-header-item" v-if="isContractor">Profit</div>
                    </div>
                    <div class="flex flex-col" v-if="invoice.job_tasks.length > 0">
                        <div class="flex justify-between mb-2 mt-2" v-for="task in invoice.job_tasks" :key="task.id">
                            <div class="invoice-item">{{ task.task.name }}</div>
                            <div class="invoice-item">{{ task.qty }}</div>
                            <div class="invoice-item">${{ task.cust_final_price }}</div>
                            <div class="invoice-item" v-if="isContractor">${{ task.sub_final_price }}</div>
                            <div class="invoice-item" v-if="isContractor">${{ task.cust_final_price - task.sub_final_price }}</div>
                        </div>
                        <div class="divider-line mt-2 mb-2"></div>
                        <div class="flex justify-between">
                            <div class="invoice-item">Totals:</div>
                            <div class="invoice-item"></div>
                            <div class="invoice-item">${{ totalCustomerPrice }}</div>
                            <div class="invoice-item" v-if="isContractor">${{ totalSubPrice }}</div>
                            <div class="invoice-item" v-if="isContractor">${{ totalCustomerPrice + totalSubPrice }}</div>
                        </div>
                    </div>
                </div>
            </card>
        </div>
        <feedback></feedback>
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
        return User.isContractor()
      },
      totalCustomerPrice() {
        let total = 0
        if (this.invoice !== null) {
          for (const task of this.invoice.job_tasks) {
            total += (task.cust_final_price - task.sub_final_price)
          }
        }
        return total
      },
      totalSubPrice() {
        let total = 0
        if (this.invoice !== null) {
          for (const task of this.invoice.job_tasks) {
            total += task.sub_final_price
          }
        }
        return total
      }
    },
    methods: {},
    mounted: function() {
      const id = this.$route.params.id
      axios.get('/invoice/' + id).then((data) => {
        this.invoice = data.data
      })
    }
  }
</script>

<style>

    .invoice-item {
        width: 40px;
    }

    .invoice-header-item {
        font-weight: 700;
        font-size: .875rem;
    }

    .divider-line {
        border-bottom: thick solid black;
    }

    @media(min-width: 792px) {
        .invoice-item {
            width: 110px;
        }
    }

</style>