<template>
    <div class="container text-center">
        <div v-if="(user.usertype === 'contractor') && user.contractor !== null">
            <h1>{{ user.contractor.company_name }}</h1>
        </div>
        <div v-else-if="(user.usertype === 'customer') && user.customer !== null">
            <h1>{{ user.name }}</h1>
        </div>
        <h3 class="home-sub-heading">Bids</h3>
        <hr>
        <div class="home-summary-data">
            <h4>Needs Approval</h4>
            <h5>5</h5>
        </div>
        <div class="home-summary-data">
            <h4>In Process</h4>
            <h5>3</h5>
        </div>
        <div class="home-summary-data">
            <h4>Waiting to be finished</h4>
            <h5>4</h5>
        </div>
        <div class="home-summary-data">
            <h4>Number of Finished Jobs</h4>
            <h5>4</h5>
        </div>
        <div v-if="(user.usertype === 'contractor') && user.contractor !== null">
            <h3 class="home-sub-heading home-sub-heading-tasks">Tasks</h3>
            <hr>
            <div class="home-summary-data">
                <h4>Needs To be Bid on</h4>
                <h5>5</h5>
            </div>
            <div class="home-summary-data">
                <h4>To Be Finished</h4>
                <h5>3</h5>
            </div>
            <div class="home-summary-data">
                <h4>Waiting on Customer Payment</h4>
                <h5>4</h5>
            </div>
            <div class="home-summary-data">
                <h4>Paid</h4>
                <h5>4</h5>
            </div>
        </div>
        <div class="summary-footer"></div>
    </div>
</template>

<script>

  export default {
    props: {
      user: Object
    },
    data () {
      return {}
    },
    computed: {},
    methods: {
      route (value) {
        if (value === 'express') {
          axios.post ('/stripe/express/dashboard').then ((response) => {
            console.log (response.data);
            window.location = response.data.url;
          });
        } else {
          this.$router.push (value)
        }
      }
    },
    mounted: function () {
    }
  }
</script>

<style scoped>
    .container {
        background-color: white;
        width: 90%;
        border-radius: 10px;
        background-image: linear-gradient(to right, rgba(22, 20, 17, 0.20), rgba(130, 182, 144, 0.20));
    }

    .home-sub-heading {
        display: flex;
        justify-content: left;
        margin-top: 2rem;
        margin-bottom: 0px;
        color: #81171F;
    }

    h3 {
        margin-top: 0rem;
        margin-bottom: 0px;
        display: flex;
    }

    .home-summary-data {
        display: flex;
        justify-content: space-between;
        margin-top: -1.5rem;
        padding-left: 3em;
        padding-right: 1em;
        color: black;
    }

    .summary-footer {
        height: 2rem;
    }

    .home-sub-heading-tasks {
        margin-top: 5rem;
    }

    h1 {
        color: #81171F;
    }

    h5 {
        color: #81171F;
        font-size: 2rem;
        font-weight: bolder;
    }
</style>