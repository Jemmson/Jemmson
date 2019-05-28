<template>

  <div class="container-fluid">
    <search-bar>
      <input type="text" class="form-control" placeholder="Search Invoices" v-model="searchTerm" @keyup="search">
    </search-bar>

    <!-- <paginate name="sBids" :list="sBids" :per="6" tag="div" class="paginated mt-4" v-show="sBids.length > 0"> -->
    <div class="paginated mt-4 mb-1">

      <card class="list-card" v-for="invoice in sInvoices" v-bind:key="invoice.id" @click.native="invoice.job_id !== undefined ? goToInvoice('/sub/invoice/' + invoice.id) : goToInvoice('/invoice/' + invoice.id)">
        <div class="row">
          <div class="col-12 page-header-title">
            <div v-if="invoice.job_id !== undefined">
              {{invoice.task.name}}
            </div>
            <div v-else>
              {{invoice.job_name}}
            </div>
          </div>
          <div class="col-12 mt-1">
            <span class="float-left list-card-info">
              Customer Name Here
            </span>

            <span class="float-right mr-2 list-card-info">
              Completed On: {{invoice.completed_bid_date.split(' ')[0]}}
              <i class="far fa-calendar-check"></i>
            </span>

          </div>
        </div>
      </card>
    </div>
    <!-- </paginate> -->

    <!-- <div class="card mb-4 mt-3">
      <div class="card-body d-flex justify-content-center">
        <paginate-links for="sBids" :async="true" :limit="2" :show-step-links="true">
        </paginate-links>
      </div>
    </div> -->
  </div>
</template>

<script>

export default {
    props: {
      user: Object,
    },
    data() {
      return {
        invoices: [],
        sInvoices: [],
        searchTerm: '',
        paginate: ['sInvoices']
      }
    },
    computed: {},
    methods: {
      invoiceLink(id) {
        console.log('invoice id: ' + id)
      },
      goToInvoice(location) {
        // debugger;
        this.$router.push(location)
      },
      search() {
        console.log('searching: ' + this.searchTerm)

        this.sInvoices = this.invoices.filter((invoice) => {
          if (this.searchTerm == '') {
            return true
          }
          return invoice.job_name.toLowerCase().search(this.searchTerm.toLowerCase()) > -1
        })
      }
    },
    created: function() {
      axios.get('invoices').then((data) => {
        this.invoices = data.data
        this.sInvoices = this.invoices
      })
    },
    mounted() {
      this.$store.commit('setCurrentPage', this.$router.history.current.path);
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