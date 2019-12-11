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
        headers: [
          {
            text: 'ID',
            align: 'left',
            sortable: false,
            value: 'id',
          },
          {text: 'Job Name', value: 'job_name'},
          {text: 'General', value: 'general'},
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
          let job
          let status
          for (let i = 0; i < this.invoices.jobs.length; i++) {
            job = this.invoices.jobs[i]
            status = job.statuses[job.statuses.length - 1]
            if (status.status === 'paid') {
              receipts.push({
                id: job.id,
                job_name: job.job_name,
                general: job.contractor.company_name,
                customer: job.customer.name,
                finished: status.updated_at
              })
            }
          }

          return receipts
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
    created: function() {
      document.body.scrollTop = 0 // For Safari
      document.documentElement.scrollTop = 0 // For Chrome, Firefox, IE and Opera
      axios.get('invoices').then((data) => {
        this.invoices = data.data
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