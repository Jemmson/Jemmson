<template>

    <v-card
            v-if="showDashboardLoginBtn"
            @click="login"
            :loading="busy">
        <v-card-title>Login to Stripe Dashboard</v-card-title>
        <v-card-actions>
            <img
                    style="height:5rem"
                    src="/img/Stripe logo - slate.svg" alt=""
            >
        </v-card-actions>
    </v-card>

</template>

<script>
    export default {
        // /Set up an express account with our stripe platform
        data: function () {
            return {
                busy: false
            }
        },
        computed: {
            showDashboardLoginBtn() {
                if (this.haveSparkStateLoaded()) {
                    return Spark.state.user.contractor !== null
                        && Spark.state.user.contractor.customer_stripe_id !== null
                }
            }
        },
        methods: {
            login() {
                this.busy = true;
                axios.post('/stripe/express/dashboard').then((response) => {
                    console.log(response.data);
                    this.busy = false;
                    window.location = response.data.url;
                });
            },
            haveSparkStateLoaded(){
                return Spark
                    && Spark.state
                    && Spark.state.user
            },
        }
    }
</script>
