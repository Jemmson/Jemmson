<template>
    <div class="container">
        <div class="row">
            <search-bar>
                <input type="text" placeholder="Search Invoices" v-model="searchTerm" @keyup="search">
            </search-bar>
            <div class="col-xs-12 col-md-6" v-for="invoice in sInvoices" :key="invoice.id">
                <div v-if="invoice.job_id !== undefined">
                    <div style="margin-bottom: 22px;">
                        <router-link :to="'/sub/invoice/' + invoice.id" class="btn btn-block btn-default btn-lg" >{{invoice.task.name}}</router-link>
                    </div>
                </div>
                <div v-else>
                    <div style="margin-bottom: 22px;">
                        <router-link :to="'/invoice/' + invoice.id" class="btn btn-block btn-default btn-lg" >{{invoice.job_name}}</router-link>
                    </div>
                </div>
            </div>
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
        mounted: function () {
            axios.get('invoices').then((data) => {
                this.invoices = data.data;
                this.sInvoices = this.invoices;
            });
        }
    }
</script>