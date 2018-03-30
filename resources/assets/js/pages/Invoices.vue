<template>
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h1>Invoices Page</h1>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="invoice-search">Search Invoices</label>
                            <input type="text" id="invoice-search" class="form-control" placeholder="Search" v-model="searchTerm" @keyup="search">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-md-6" v-for="invoice in sInvoices" :key="invoice.id">
                <div style="margin-bottom: 22px;">
                    <router-link :to="'/invoice/' + invoice.id" class="btn btn-block btn-default btn-lg" >{{invoice.job_name}}</router-link>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {},
        data() {
            return {
                user: {},
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
                    return invoice.job_name.search(this.searchTerm) > -1;
                })
            }
        },
        mounted: function () {
            this.user = Spark.state.user;
            axios.get('invoices').then((data) => {
                this.invoices = data.data;
                this.sInvoices = this.invoices;
            });
        }
    }
</script>