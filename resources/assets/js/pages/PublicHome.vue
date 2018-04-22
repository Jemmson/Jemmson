<template>
    <div>
        <div style="text-align: center; margin-bottom: 1rem;">
            <div class="btn-group">
                <button class="login btn btn-lg btn-primary" v-on:click="route('login')">LOGIN</button>
                <button class="register btn btn-lg btn-primary" v-on:click="route('register')">REGISTER</button>
            </div>
        </div>
        <div class="container">

            <!-- Heading Row -->
            <div class="row">
                <div class="col-md-8">
                    <img class="img-responsive img-rounded" src="/img/TreeGrove.jpg" alt="">
                </div>
                <!-- /.col-md-8 -->
                <div class="col-md-4">
                    <h1 class="text-center">JEMSUB</h1>
                    <p>Welcome to your place where you can SUBCONTRACT work you dont have time for. A place where you
                        can INVOICE your customers and get paid using Stripe or with cash.</p>
                    <!--<a class="btn btn-primary btn-lg" href="#">Call to Action!</a>-->
                </div>
                <!-- /.col-md-4 -->
            </div>
            <!-- /.row -->

            <hr>

            <!-- Call to Action Well -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="well text-center">
                        SUBCONTRACT, INVOICE, GET PAID, EASILY!
                    </div>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->

            <!-- Content Row -->
            <div class="row">
                <div class="col-md-4">
                    <h2>Subcontract Work</h2>
                    <p>How often at a job do you run into work that you do not do or work you dont have time for?
                    How often do you simply lose income because you either call someone to handle the job for you or
                    just tell the customer to find someone else? Now with this app you can easily create a task and then
                     subcontract that task, mark up the task, and then easily get paid for the work.</p>
                    <!--<a class="btn btn-default" href="#">More Info</a>-->
                </div>
                <!-- /.col-md-4 -->
                <div class="col-md-4">
                    <h2>Invoice Customers</h2>
                    <p>You can create an invoice at the beginning of a job and then use that as a commmunication tool
                    throughout the jobs life cycle. You simply initiate a bid for all jobs. Then construct that bid,
                        subcontract any of those tasks, and then submit the bid to the customer. The customer then has
                    the ability to approve the bid. Once the bid has been approve then the work can be performed. Once the
                    work has been performed the customer can now approve the finish work and pay using Stripe or cash.</p>
                    <!--<a class="btn btn-default" href="#">More Info</a>-->
                </div>
                <!-- /.col-md-4 -->
                <div class="col-md-4">
                    <h2>Get Paid Easily </h2>
                    <p>How much money is wasted chasing down a check? How much of a pain is it to have to write a check
                        to a subcontractor after a job has been finished? With Stripe this will happen easily.
                     The customer pays the invoice and then the money is automatically split between the contractor
                        and the sub.</p>
                    <!--<a class="btn btn-default" href="#">More Info</a>-->
                </div>
                <!-- /.col-md-4 -->
            </div>
            <!-- /.row -->

            <!-- Footer -->
            <footer>
                <div class="row">
                    <div class="col-lg-12">
                        <p>Copyright &copy; Your Website 2018</p>
                    </div>
                </div>
            </footer>

        </div>
    </div>
</template>

<script>
  export default {
    computed: {},
    methods: {
      route (value) {
        if (value === 'login') {
          window.location = '/login'
        } else if (value === 'register') {
          window.location = '/register#/'
        }
      }
    }
  }
</script>