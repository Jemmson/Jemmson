<template>
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h1>Invoice Page</h1>
                    </div>
                </div>
            </div>
            <div class="col-md-12" v-if="(user.usertype === 'contractor')">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <label for="title">
                            Invoice for: {{ invoice.job_name }}
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-md-12" v-else-if="(user.usertype === 'customer')">
                <div class="panel panel-default">
                    <div class="panel-body">

                    </div>
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
                invoice: {}
            }
        },
        computed: {},
        methods: {
        },
        mounted: function () {
            this.user = Spark.state.user;
            const id = this.$route.params.id;
            axios.get('/invoice/' + id).then((data) => {
                this.invoice = data.data;
            });
        }
    }
</script>