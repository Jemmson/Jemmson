<template>

    <div class="container-fluid">

        <v-card>
            <v-card-title>
                Receipts
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
                    :headers="headers"
                    :items="receipts()"
                    :search="search"
                    @click:row="goToInvoice('/invoice/'+ $event.id)"
            ></v-data-table>
        </v-card>
        <!--        <search-bar>-->
        <!--            <input type="text" class="form-control" placeholder="Search Paid Receipts" v-model="searchTerm"-->
        <!--                   @keyup="search">-->
        <!--        </search-bar>-->

        <!--        &lt;!&ndash; <paginate name="sBids" :list="sBids" :per="6" tag="div" class="paginated mt-4" v-show="sBids.length > 0"> &ndash;&gt;-->
        <!--        <div class="paginated mt-4 mb-1">-->
        <!--            <v-card v-for="invoice in sInvoices.jobs" v-bind:key="invoice.id"-->
        <!--                    @click.native="invoice.job_id !== undefined ? goToInvoice('/sub/invoice/' + invoice.id) : goToInvoice('/invoice/' + invoice.id)"-->
        <!--            >-->
        <!--                <v-card-title v-if="invoice.job_id !== undefined">{{invoice.task.name}}</v-card-title>-->
        <!--                <v-card-title class="headline">{{invoice.job_name}}</v-card-title>-->

        <!--                <v-list-item two-line>-->
        <!--                    <v-list-item-content>-->
        <!--                        <v-list-item-title>Completed On:</v-list-item-title>-->
        <!--                        <v-list-item-subtitle>{{completedOn(invoice.completed_bid_date)}}</v-list-item-subtitle>-->
        <!--                    </v-list-item-content>-->
        <!--                </v-list-item>-->
        <!--                <v-list-item two-line>-->
        <!--                    <v-list-item-content>-->
        <!--                        <v-list-item-title>Job Name:</v-list-item-title>-->
        <!--                        <v-list-item-subtitle>{{invoice.job_name}}</v-list-item-subtitle>-->
        <!--                    </v-list-item-content>-->
        <!--                </v-list-item>-->
        <!--                <v-list-item two-line>-->
        <!--                    <v-list-item-content>-->
        <!--                        <v-list-item-title>General Contractor:</v-list-item-title>-->
        <!--                        <v-list-item-subtitle>{{invoice.contractor.company_name}}</v-list-item-subtitle>-->
        <!--                    </v-list-item-content>-->
        <!--                </v-list-item>-->
        <!--            </v-card>-->
        <!--        </div>-->
        <feedback
                page="invoices"
        ></feedback>
    </div>
</template>

<script>
  import Feedback from '../components/shared/Feedback'
  import SearchBar from '../components/shared/SearchBar'

  export default {
    components: {
      Feedback,
      SearchBar
    },
    props: {
      user: Object,
    },
    data() {
      return {

        search: '',
        headers: [{
          text: 'ID',
          align: 'left',
          sortable: false,
          value: 'id',
        },
          {text: 'Job Name', value: 'job_name'},
          {text: 'General', value: 'general'},
          {text: 'Sub', value: 'sub'},
          {text: 'Customer', value: 'customer'},
          {text: 'Finished', value: 'finished'},
        ],

        invoices: [],
        sInvoices: [],
        searchTerm: '',
        paginate: ['sInvoices']
      }
    },
    computed: {},
    methods: {
      getReceipt(row) {

        this.$router.push({name: receipt})

        console.log('row', row)
      },
      invoiceLink(id) {
        console.log('invoice id: ' + id)
      },
      receipts() {
        if (this.invoices.jobs) {
          let receipts = []

          for (let i = 0; i < this.invoices.jobs.length; i++) {
            receipts.push({
              id: this.invoices.jobs[i].id,
              job_name: this.invoices.jobs[i].job_name,
              general: this.invoices.jobs[i].contractor.company_name,
              sub: '',
              customer: this.invoices.jobs[i].customer.name,
              finished: this.invoices.jobs[i].completed_bid_date
            })
          }

          return receipts
        }

      },
      goToInvoice(location) {
        // debugger;
        this.$router.push(location)
      },
      // search() {
      //   console.log('searching: ' + this.searchTerm)
      //
      //   this.sInvoices = this.invoices.filter((invoice) => {
      //     if (this.searchTerm == '') {
      //       return true
      //     }
      //     return invoice.job_name.toLowerCase().search(this.searchTerm.toLowerCase()) > -1
      //   })
      // },
      completedOn(date) {
        if (date) {
          return date.split(' ')[0]
        }
      },
    },
    created: function() {
      document.body.scrollTop = 0 // For Safari
      document.documentElement.scrollTop = 0 // For Chrome, Firefox, IE and Opera
      axios.get('invoices').then((data) => {
        // this.invoices = data.data
        this.invoices = {
          'jobs': [{
            'id': 1,
            'job_name': 'new job',
            'completed_bid_date': null,
            'contractor_id': 1,
            'customer_id': 2,
            'contractor': {'company_name': 'Jemmson'},
            'customer': {'name': 'Shawn Pike', 'tax_rate': 0}
          },
            {
              'id': 2,
              'job_name': 'new job1',
              'completed_bid_date': null,
              'contractor_id': 1,
              'customer_id': 2,
              'contractor': {'company_name': 'Jemmson'},
              'customer': {'name': 'Shawn Pike', 'tax_rate': 0}
            },
            {
              'id': 3,
              'job_name': 'new job2',
              'completed_bid_date': null,
              'contractor_id': 1,
              'customer_id': 2,
              'contractor': {'company_name': 'Jemmson'},
              'customer': {'name': 'Shawn Pike', 'tax_rate': 0}
            }]
        }
        this.sInvoices = this.invoices
      })
    },
    mounted() {
      this.$store.commit('setCurrentPage', this.$router.history.current.path)
    },
  }
</script>

<style scoped>
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