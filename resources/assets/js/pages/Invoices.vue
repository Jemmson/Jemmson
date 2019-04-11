<template>
    <div class="flex flex-col justify-between full-height">
        <div>
            <search-bar>
                <input
                        type="text"
                        placeholder="Search Invoices"
                        v-model="searchTerm"
                        @keyup="search">
            </search-bar>
            <paginate v-show="sInvoices.length > 0"
                      ref="paginator" name="sInvoices"
                      :list="sInvoices"
                      :per="8"
                      class="paginated">
                <div v-for="invoice in paginated('sInvoices')"
                     v-bind:key="invoice.id"
                     class="invoice-section"
                >
                    <div
                            v-if="invoice.job_id !== undefined"
                            class="sizing text-center w-full h-full"
                            @click="goToInvoice('/sub/invoice/' + invoice.id)"
                    >
                        {{invoice.task.name}}
                    </div>
                    <div
                            v-else
                            @click="goToInvoice('/invoice/' + invoice.id)"
                            class="sizing text-center w-full h-full"
                    >
                        {{invoice.job_name}}
                    </div>
                </div>
            </paginate>
        </div>
        <div class="card p-5 card-body justify-center">
            <paginate-links for="sInvoices" :limit="2" :show-step-links="true">
            </paginate-links>
        </div>
        <feedback></feedback>
    </div>
</template>

<script>

  import SearchBar from '../components/shared/SearchBar'
  import Feedback from '../components/shared/Feedback'
  import Card from '../components/shared/Card'

  export default {
    components: {
      SearchBar,
      Feedback,
      Card
    },
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