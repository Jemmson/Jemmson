<template>
  <div class="container">
    <div v-if="invoice !== null" ref="invoice-details" class="row">
      <div class="col-12 mb-4">
        <h1 class="card-title">Details</h1>
        <card>
          <div class="row">
            <div class="col-12 mb-3">
              <span class="">Job Name:</span>
              <span class="float-right">{{ invoice.job_name }}</span>
            </div>
            <div class="col-12 mb-3">
              <span class="">Date Completed:</span>
              <span class="float-right">{{ invoice.actual_end_date.split(' ')[0] }}</span>
            </div>
            <div class="col-12 mb-2">
              <span class="">Total Price:</span>
              <span class="float-right">${{ invoice.bid_price/100 }}</span>
            </div>
          </div>
        </card>
      </div>

      <div class="col-12">
        <card>
          <div class="row">
            <div class="col-12">
              <div class="">Task Name</div>
              <div class="">QTY</div>
              <div class="">Task Price</div>
              <div class="" v-if="isContractor">Task Price (Sub)</div>
              <div class="" v-if="isContractor">Profit</div>
            </div>
            <div class="col-12" v-if="invoice.job_tasks.length > 0">
              <div class="flex justify-between mb-2 mt-2" v-for="task in invoice.job_tasks" :key="task.id">
                <div class="">{{ task.task.name }}</div>
                <div class="">{{ task.qty }}</div>
                <div class="">${{ task.cust_final_price }}</div>
                <div class="" v-if="isContractor">${{ task.sub_final_price }}</div>
                <div class="" v-if="isContractor">${{ task.cust_final_price -
                                task.sub_final_price }}
                </div>
              </div>
              <div class="divider-line mt-2 mb-2"></div>
              <div class="col-12">
                <div class="">Totals:</div>
                <div class=""></div>
                <div class="">${{ totalCustomerPrice }}</div>
                <div class="" v-if="isContractor">${{ totalSubPrice }}</div>
                <div class="" v-if="isContractor">${{ totalCustomerPrice + totalSubPrice }}
                </div>
              </div>
            </div>
          </div>
        </card>
      </div>
    </div>
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

    . {
        width: 40px;
    }

    .invoice-header-item {
        font-weight: 700;
        font-size: .875rem;
    }

    .divider-line {
        border-bottom: thick solid black;
    }

    @media (min-width: 792px) {
        . {
            width: 110px;
        }
    }

</style>