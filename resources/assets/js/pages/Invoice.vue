<template>
  <div class="container">

    <!--        <pre>{{ invoice }}</pre>-->

    <v-card

    >
      <div class="flex align-center justify-content-between">
        <v-card-title class="w-break">{{ invoice ? invoice.job.job_name : '' }}</v-card-title>
        <div class="mr-1rem">
          <v-icon color="green">mdi-check-circle</v-icon>
          <div style="color: green">Paid</div>
        </div>
      </div>

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

          <div class="flex justify-content-around align-center">
            <v-card-title>{{ user.contractor.company_name }}</v-card-title>
          </div>
          <v-card-text>
            <div class="capitalize" style="font-weight: bold">Job Address:</div>
            <div>
              <v-btn
                  color="primary"
                  style="margin-left: -1rem"
                  text
                  @click="viewCustomerInfoForSubs()"
              >
                {{ invoice.job.customer.first_name }} {{ invoice.job.customer.last_name }}
              </v-btn>
              <div class="capitalize">{{ invoice.job.location.address_line_1 }}</div>
              <div class="capitalize">{{ invoice.job.location.address_line_2 }}</div>
              <div class="capitalize">{{ invoice.job.location.city }}, {{ invoice.job.location.state }},
                {{ invoice.job.location.zip }}
              </div>
            </div>
          </v-card-text>



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
                <td v-if="item.sub"
                    style="color: #1976d2"
                    @click="viewContractorInfoForSubsWithId( item.sub.id )"
                >{{ item.sub.company.company_name }}</td>
                <td v-if="item.general">{{ item.general.company.company_name }}</td>
                <td>{{ item.qty }}</td>
                <td>${{ getUnitPrice(item) }}</td>
              </tr>
              </tbody>
            </template>
          </v-simple-table>
          <hr>
          <v-row align="center">
            <v-card-subtitle>Total</v-card-subtitle>
            <v-spacer></v-spacer>
            <span class="mr-1rem">${{ getBidPrice(invoice) }}</span>
          </v-row>
        </div>

        <div v-if="invoice && invoice.user === 'sub'">
          <div class="flex justify-content-around align-center">
            <v-card-title>{{ user.contractor.company_name }}</v-card-title>
            <v-btn
                color="primary"
                text
                @click="viewContractorInfoForSubs()"
            >
              {{ invoice.contractor.company.company_name }}
            </v-btn>
          </div>
          <v-card-text>
              <div class="capitalize" style="font-weight: bold">Job Address:</div>
              <div>
                <v-btn
                    color="primary"
                    style="margin-left: -1rem"
                    text
                    @click="viewCustomerInfoForSubs()"
                >
                  {{ invoice.job.customer.first_name }} {{ invoice.job.customer.last_name }}
                </v-btn>
                <div class="capitalize">{{ invoice.job.location.address_line_1 }}</div>
                <div class="capitalize">{{ invoice.job.location.address_line_2 }}</div>
                <div class="capitalize">{{ invoice.job.location.city }}, {{ invoice.job.location.state }},
                  {{ invoice.job.location.zip }}
                </div>
              </div>
          </v-card-text>
          <v-simple-table>
            <template v-slot:default>
              <thead>
              <tr>
                <th class="text-left uppercase">Description</th>
                <th class="text-left uppercase">Qty</th>
                <th class="text-left uppercase">Total</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="item in invoice.job.job_tasks" :key="item.task_id">
                <td class="uppercase">{{ item.task.name }}</td>
                <td>{{ item.qty }}</td>
                <td>${{ subPrice(item) }}</td>
              </tr>
              </tbody>
            </template>
          </v-simple-table>
          <hr>
          <v-row align="center">
            <v-card-subtitle>Total</v-card-subtitle>
            <v-spacer></v-spacer>
            <span class="mr-1rem">${{ totalSubPrice() }}</span>
          </v-row>
        </div>
      </v-card-text>
    </v-card>

    <v-card class="mt-1rem" v-if="invoice && invoice.job">
      <v-card-title>Job Statuses</v-card-title>
      <v-card-text>
        <v-simple-table>
          <template>
            <thead>
            <tr>
              <th class="text-left uppercase">Status</th>
              <th class="text-left uppercase">Date</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="status in invoice.job.status" :key="status.id">
              <td class="uppercase">{{ status.status }}</td>
              <td>{{ dateOnlyHyphenDBTimestamp(status.created_at) }}</td>
            </tr>
            </tbody>
          </template>
        </v-simple-table>
      </v-card-text>
    </v-card>

    <feedback
        page="invoice"
    ></feedback>
  </div>
</template>

<script>

import Feedback from '../components/shared/Feedback'
import Utilities from "../components/mixins/Utilities";

export default {
  components: {
    Feedback
  },
  mixins: [Utilities],
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
  },
  methods: {

    viewCustomerInfoForSubs() {
      if (
          this.invoice
          && this.invoice.contractor
      ) {
        this.$store.commit('setCurrentPage', '/customer-info');
        this.$router.push({
          name: 'customer-info',
          params: {customerId: this.invoice.job.customer.id}
        })
      }
    },

    viewContractorInfoForSubsWithId(id) {
      if (
          this.invoice
          && this.invoice.contractor
      ) {
        this.$store.commit('setCurrentPage', '/contractor-info');
        this.$router.push({
          name: 'contractor-info',
          params: {contractorId: id}
        })
      }
    },

    viewContractorInfoForSubs() {
      if (
          this.invoice
          && this.invoice.contractor
      ) {
        this.$store.commit('setCurrentPage', '/contractor-info');
        this.$router.push({
          name: 'contractor-info',
          params: {contractorId: this.invoice.contractor.id}
        })
      }
    },

    subPrice(item) {

      let price = item.sub_final_price / 100

      return price.toFixed(2)
    },

    getBidPrice(invoice) {
      let price = parseFloat(invoice.job.bid_price) / 100
      return price.toFixed(2)
    },
    totalSubPrice() {
      let total = 0
      if (this.invoice !== null) {
        for (const task of this.invoice.job.job_tasks) {
          total += parseFloat(task.sub_final_price)
        }
      }
      return total / 100
    },
    getUnitPrice(item) {
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