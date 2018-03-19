<template>
    <div class="container text-center">
        <!--<pre>{{ user }}</pre>-->
        <h1>Jemmson App</h1>
        <div v-if="(this.user.usertype === 'contractor')">
            <h1>I am a contractor</h1>
        </div>
        <div v-else-if="(this.user.usertype === 'customer')">
            <h1>I am a customer</h1>
        </div>
        <div v-else="">
            <div class="row" style="margin-top: 5rem">
                <div class="col-md-6">
                    <div class="panel panel-default panel-flush">
                        <div class="panel-heading">
                            <h2>Are you a Customer?</h2>
                        </div>
                        <div class="panel-body">
                            <p style="margin: 1rem 1rem 1rem 1rem; font-size: 2rem;">The Jemmson Application can offer you a
                                great way to find a
                                quality contractor that has a lot of great features.</p>
                            <ol>
                                <li style="font-size: 2rem;">Security</li>
                                <li style="font-size: 2rem;">Job Tracking</li>
                                <li style="font-size: 2rem;">In app messaging</li>
                            </ol>
                            <hr>
                            <a href="/customerFeatures" class="btn btn-primary" style="margin-bottom: 2rem;">Learn More</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-default panel-flush">
                        <div class="panel-heading">
                            <h2>Are you a Contractor?</h2>
                        </div>
                        <div class="panel-body">
                            <p style="margin: 1rem 1rem 1rem 1rem; font-size: 2rem;">The Jemmson Application can offer you a
                                great way to find a
                                quality contractor that has a lot of great features.</p>
                            <ol>
                                <li style="font-size: 2rem;">Security</li>
                                <li style="font-size: 2rem;">Job Tracking</li>
                                <li style="font-size: 2rem;">In app messaging</li>
                            </ol>
                            <hr>
                            <a href="/contractorFeatures" class="btn btn-primary" style="margin-bottom: 2rem;">Learn
                                More</a>
                        </div>
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
    methods: {},
    mounted: function () {
      console.log(Spark.state.user);
      this.user = Spark.state.user;
    }
  }
</script>