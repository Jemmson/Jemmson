<template>
    <div class="container text-center">
        <!--<pre>{{ user }}</pre>-->
        <!--<h1>Jemmson App</h1>-->
        <div v-if="(this.user.usertype === 'contractor')">
            <h1 class="home-page-title">{{ this.user.contractor.company_name }}</h1>
            <div class="home-page-wrapper">
                <div class="home-page-initiate-bid home-page-section-style" @click="route('initiate-bid')">
                    <div class="home-page-initiate-bid-logo">
                        <img src="/img/mono-logo.png" style="height: 32px;">
                    </div>
                    <div class="home-page-initiate-bid-text">
                        Initiate a Bid Here
                    </div>
                </div>
                <div class="home-page-bid home-page-section-style" @click="route('bids')">
                    <div class="home-page-initiate-bid-logo">
                        <img src="/img/mono-logo.png" style="height: 32px;">
                    </div>
                    <div class="home-page-initiate-bid-text">
                        Look at Bids Here
                    </div>
                </div>
                <div class="home-page-task home-page-section-style" @click="route('tasks')">
                    <div class="home-page-initiate-bid-logo">
                        <img src="/img/mono-logo.png" style="height: 32px;">
                    </div>
                    <div class="home-page-initiate-bid-text">
                        Look at Tasks Here
                    </div>
                </div>
                <div class="home-page-past-invoices home-page-section-style" @click="">
                    <div class="home-page-initiate-bid-logo">
                        <img src="/img/mono-logo.png" style="height: 32px;">
                    </div>
                    <div class="home-page-initiate-bid-text">
                        Past Invoices
                    </div>
                </div>
                <div class="home-page-past-stripe-dashboard home-page-section-style" @click="">
                    <div class="home-page-initiate-bid-logo">
                        <img src="/img/mono-logo.png" style="height: 32px;">
                    </div>
                    <div class="home-page-initiate-bid-text">
                        Stripe Dashboard
                    </div>
                </div>
            </div>
        </div>
        <div v-else-if="(this.user.usertype === 'customer')">
            <h1 class="home-page-title">{{ this.user.name }}</h1>
            <div class="home-page-wrapper">
                <div class="home-page-initiate-bid home-page-section-style" @click="route('initiate-bid')">
                    <div class="home-page-initiate-bid-logo">
                        <img src="/img/mono-logo.png" style="height: 32px;">
                    </div>
                    <div class="home-page-initiate-bid-text">
                        Initiate a Bid Here
                    </div>
                </div>
                <div class="home-page-bid home-page-section-style" @click="route('bids')">
                    <div class="home-page-initiate-bid-logo">
                        <img src="/img/mono-logo.png" style="height: 32px;">
                    </div>
                    <div class="home-page-initiate-bid-text">
                        Look at Bids Here
                    </div>
                </div>
                <div class="home-page-task home-page-section-style" @click="route('tasks')">
                    <div class="home-page-initiate-bid-logo">
                        <img src="/img/mono-logo.png" style="height: 32px;">
                    </div>
                    <div class="home-page-initiate-bid-text">
                        Look at Tasks Here
                    </div>
                </div>
                <div class="home-page-task home-page-section-style" @click="">
                    <div class="home-page-initiate-bid-logo">
                        <img src="/img/mono-logo.png" style="height: 32px;">
                    </div>
                    <div class="home-page-initiate-bid-text">
                        Past Invoices
                    </div>
                </div>
                <div class="home-page-past-stripe-dashboard home-page-section-style" @click="">
                    <div class="home-page-initiate-bid-logo">
                        <img src="/img/mono-logo.png" style="height: 32px;">
                    </div>
                    <div class="home-page-initiate-bid-text">
                        Stripe Dashboard
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

  export default {
    props: {
    },
    data () {
      return {
        user: {}
      }
    },
    computed: {},
    methods: {
      route(value){
          this.$router.push (value)
      }
    },
    mounted: function () {
      console.log(Spark.state.user);
      this.user = Spark.state.user;
    }
  }
</script>