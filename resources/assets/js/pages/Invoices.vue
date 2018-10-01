<template>
    <div class="flex flex-col">
        <search-bar>
            <input type="text" placeholder="Search Invoices" v-model="searchTerm" @keyup="search">
        </search-bar>
        <paginate v-show="sInvoices.length > 0" ref="paginator" name="sInvoices" :list="sInvoices" :per="8" class="paginated">
            <card v-for="invoice in paginated('sInvoices')" v-bind:key="invoice.id">
                <div v-if="invoice.job_id !== undefined" class="self-center">
                    <router-link :to="'/sub/invoice/' + invoice.id" class="w-full" >{{invoice.task.name}}</router-link>
                </div>
                <div v-else class="self-center">
                    <router-link :to="'/invoice/' + invoice.id" class="w-full" >{{invoice.job_name}}</router-link>
                </div>
            </card>
        </paginate>
        <div class="card p-5 card-body justify-center">
            <paginate-links for="sInvoices" :limit="2" :show-step-links="true">
            </paginate-links>
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
                invoices: {},
                sInvoices: {},
                searchTerm: '',
                paginate: ['sInvoices']
            }
        },
        computed: {},
        methods: {
            invoiceLink(id) {
                console.log('invoice id: ' + id);
            },
            search() {
                console.log('searching: ' + this.searchTerm);
                
                this.sInvoices = this.invoices.filter((invoice) => {
                    if (this.searchTerm == '') {
                        return true;
                    }
                    return invoice.job_name.toLowerCase().search(this.searchTerm.toLowerCase()) > -1;
                })
            }
        },
        created: function () {
            axios.get('invoices').then((data) => {
                this.invoices = data.data;
                this.sInvoices = this.invoices;
            });
        }
    }
</script>