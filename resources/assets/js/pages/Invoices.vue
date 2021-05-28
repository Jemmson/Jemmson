<template>

  <div class="container-fluid">

    <h2 class="text-center uppercase black--text" style="margin-bottom: 2rem;">Receipts Page</h2>


    <v-tabs class="justify-center" style="margin-top: 3.5rem;">
      <v-tab
          v-if="jobsLength()"
          style="margin-left: auto; margin-right: auto;"
          class="w-full"
          @click.prevent="showReceipts('jobs')"
      >General
      </v-tab>
      <v-tab
          v-if="subsLength()"
          style="margin-left: auto; margin-right: auto;"
          class="w-full"
          @click.prevent="showReceipts('subs')"
      >Subs
      </v-tab>
      <v-tab
          v-if="customerLength()"
          style="margin-left: auto; margin-right: auto;"
          class="w-full mt-1rem"
          @click.prevent="showReceipts('customer')"
      >Customer
      </v-tab>
    </v-tabs>

    <v-card v-if="show.jobs">
      <v-card-title>General Receipts
        <v-spacer></v-spacer>
        <v-text-field
            v-model="search"
            append-icon="mdi-table-search"
            label="Search"
            hide-details
        ></v-text-field>
      </v-card-title>

      <v-data-table
          :headers="jobHeaders"
          :items="jobReceipts"
          :search="search"
          disable-sort
          @click:row="goToInvoice('/invoice/'+ $event.id)"
      >
        <hr>
        <template v-slot:item="{ item }" @click="goToInvoice('/invoice/'+ item.id)"
                  class="flex justify-content-around align-center border-bottom select-item">
            <div>{{ item.job_name }}</div>
            <div>{{ item.customer }}</div>
            <div>{{ item.finished }}</div>
            <hr>
        </template>
      </v-data-table>
    </v-card>

    <v-card v-if="show.subs">
      <v-card-title>Sub Receipts
        <v-spacer></v-spacer>
        <v-text-field
            v-model="search"
            append-icon="mdi-table-search"
            label="Search"
            single-line
            hide-details
        ></v-text-field>
      </v-card-title>
      <v-data-table
          :headers="subHeaders"
          :items="subReceipts"
          :search="search"
          @click:row="goToInvoice('/invoice/'+ $event.job_id)"
      >
        <template v-slot:item="{ item }" @click="goToInvoice('/invoice/'+ item.id)"
                  class="flex justify-content-around align-center border-bottom select-item">
            <div>{{ item.job_name }}</div>
            <div>{{ item.customer }}</div>
            <div>{{ item.finished }}</div>
            <hr>
        </template>
      </v-data-table>
    </v-card>

    <v-card v-if="show.customer">
      <v-card-title>
        Customer Receipts
        <v-spacer></v-spacer>
        <v-text-field
            v-model="search"
            append-icon="mdi-table-search"
            label="Search"
            single-line
            hide-details
        ></v-text-field>
      </v-card-title>
      <v-data-table
          :headers="customerHeaders"
          :items="customerReceipts"
          disable-sort
          :search="search"
          @click:row="goToInvoice('/invoice/'+ $event.id)"
      >
        <hr>
        <template v-slot:item="{ item }" @click="goToInvoice('/invoice/'+ item.id)"
                  class="flex justify-content-around align-center border-bottom select-item">
          <div>{{ item.job_name }}</div>
          <hr>
        </template>
        <template v-slot:item="{ item }" @click="goToInvoice('/invoice/'+ item.id)"
                  class="flex justify-content-around align-center border-bottom select-item">
          <div>{{ item.general }}</div>
          <hr>
        </template>
        <template v-slot:item="{ item }" @click="goToInvoice('/invoice/'+ item.id)"
                  class="flex justify-content-around align-center border-bottom select-item">
          <div>{{ item.finished }}</div>
          <hr>
        </template>
      </v-data-table>
    </v-card>

    <feedback
        page="invoices"
    ></feedback>
  </div>
</template>

<script>
import Feedback from '../components/shared/Feedback'
import SearchBar from '../components/shared/SearchBar'
import Utilities from "../components/mixins/Utilities";

export default {
  components: {
    Feedback,
    SearchBar
  },
  props: {
    user: Object,
  },
  mixins: [
    Utilities
  ],
  data() {
    return {

      search: '',
      customerHeaders: [
        {text: 'Job Name', value: 'job_name'},
        {text: 'General', value: 'general'},
        {text: 'Finished', value: 'finished'}
      ],
      customerReceipts: [],
      jobHeaders: [
        {text: 'Job Name', value: 'job_name'},
        {text: 'Finished', value: 'finished'},
      ],
      jobReceipts: [],
      subHeaders: [
        {
          text: 'ID',
          align: 'left',
          sortable: false,
          value: 'id',
        },
        {text: 'Qty', value: 'qty'},
        {text: 'Job Name', value: 'job_name'},
        {text: 'General', value: 'general'},
        {text: 'Customer', value: 'customer'},
        {text: 'Price', value: 'price'},
        {text: 'Finished', value: 'finished'},
      ],
      subReceipts: [],
      invoices: [],
      sInvoices: [],
      searchTerm: '',
      paginate: ['sInvoices'],
      show: {
        jobs: false,
        subs: false,
        customer: false
      }
    }
  },
  computed: {},
  methods: {
    showReceipts(receipt) {
      this.hideReceipts();

      if (receipt === 'jobs') {
        this.show.jobs = true;
      } else if (receipt === 'subs') {
        this.show.subs = true;
      } else if (receipt === 'customer') {
        this.show.customer = true;
      }
    },

    jobsLength() {
      if (this.invoices && this.invoices[0] && this.invoices[0].jobs) {
        return this.invoices[0].jobs.length > 0;
      } else {
        return false
      }
    },

    subsLength() {
      if (this.invoices && this.invoices[1] && this.invoices[1].jobTasks) {
        return this.invoices[1].jobTasks.length > 0;
      } else {
        return false
      }
    },

    customerLength() {
      if (this.invoices && this.invoices[0] && this.invoices[0].customerJobs) {
        this.show.customer = true;
        return this.invoices[0].customerJobs.length > 0;
      }
    },

    hideReceipts() {
      this.show.jobs = false;
      this.show.subs = false;
      this.show.customer = false;
    },

    getReceipt(row) {

      this.$router.push({name: receipt})

      console.log('row', row)
    },
    invoiceLink(id) {
      console.log('invoice id: ' + id)
    },
    receipts(jobs) {
      if (jobs) {
        let job
        let status
        for (let i = 0; i < jobs[0].jobs.length; i++) {
          job = jobs[0].jobs[i]
          status = job.statuses[job.statuses.length - 1]
          if (status.status === 'paid') {
            this.jobReceipts.push({
              id: job.id,
              job_name: job.job_name,
              general: job.contractor.company_name,
              customer: job.customer.name,
              price: job.bid_price / 100,
              finished: this.dateOnly(status.updated_at)
            })
          }
        }
      }

    },
    createCustomerReceipts(jobs) {
      if (jobs) {
        let job
        let status
        for (let i = 0; i < jobs[0].customerJobs.length; i++) {
          job = jobs[0].customerJobs[i]
          status = job.statuses[job.statuses.length - 1]
          if (status.status === 'paid') {
            this.customerReceipts.push({
              id: job.id,
              job_name: job.job_name,
              general: job.contractor.company_name,
              customer: job.customer.name,
              price: job.bid_price / 100,
              finished: this.dateOnly(status.updated_at)
            })
          }
        }
      }
    },

    jobHasNotBeenAdded(val) {
      return this.subReceipts.filter(val => {
        return val.job_name === "2020-100-Pike-Shawn"
      }).length === 0
    },

    createSubReceipts(jobs) {
      if (jobs) {
        let job
        let status
        let jobName
        for (let i = 0; i < jobs[1].jobTasks.length; i++) {
          job = jobs[1].jobTasks[i]
          status = job.statuses[job.statuses.length - 1]
          jobName = job.job_name;
          if (
              status.status === 'paid'
              && this.subReceipts.filter(val => {
                return val.job_name === job.job_name
              }).length === 0
          ) {
            this.subReceipts.push({
              id: job.id,
              job_id: job.job_id,
              qty: job.qty,
              job_name: job.job_name,
              general: job.general.company_name,
              customer: job.customer.name,
              price: job.sub_final_price / 100,
              finished: this.dateOnly(status.updated_at)
            })
          }
        }
      }

    },
    goToInvoice(location) {
      this.$router.push(location)
    },
    completedOn(date) {
      if (date) {
        return date.split(' ')[0]
      }
    },
  },
  created: function () {
    document.body.scrollTop = 0 // For Safari
    document.documentElement.scrollTop = 0 // For Chrome, Firefox, IE and Opera
    axios.get('invoices').then((data) => {
      console.log('data', JSON.stringify(data.data))
      this.invoices = data.data

      if (
          data
          && data.data[0]
          && data.data[0].customerJobs
          && data.data[0].customerJobs.length > 0
      ) {
        this.createCustomerReceipts(data.data);
      }

      if (
          data
          && data.data[1]
          && data.data[1].jobTasks
          && data.data[1].jobTasks.length > 0
      ) {
        this.createSubReceipts(data.data);
        this.hideReceipts();
        this.show.subs = true;
      }

      if (
          data
          && data.data[0]
          && data.data[0].jobs
          && data.data[0].jobs.length > 0
      ) {
        this.receipts(data.data);
        this.hideReceipts();
        this.show.jobs = true;
      }
    })
  },
  mounted() {
    this.$store.commit('setCurrentPage', this.$router.history.current.path)
  },
}
</script>

<style scoped>

.select-item:hover {
  background: #e5e5e5;
  outline: none;
  -webkit-box-shadow: inset 0px 0px 5px #c1c1c1;
  -moz-box-shadow: inset 0px 0px 5px #c1c1c1;
  box-shadow: inset 0px 0px 5px #c1c1c1;
}

.select-item:active {
  background: #e5e5e5;
  outline: none;
  -webkit-box-shadow: inset 0px 0px 5px #c1c1c1;
  -moz-box-shadow: inset 0px 0px 5px #c1c1c1;
  box-shadow: inset 0px 0px 5px #c1c1c1;
}

.hoverOver:hover {
  background: red;
}

.hoverOver:active {
  background: green;
}

.full-height {
  height: 125vh;
}

.sizing {
  font-size: 14pt;
  padding: .75rem 0;
}

.invoice-section {
  height: 3rem;
  width: 100%;
  background-color: white;
  border-radius: 5px;
}
</style>