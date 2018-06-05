<template>
    <div class="wrapper">
        <div class="title"><h1 class="text-center">Jemsub</h1></div>
        <div class="section main">
            <h2>Subcontract Work</h2>
            <p>How often at a job do you run into work that you do not do or work you dont have time for?
                How often do you simply lose income because you either call someone to handle the job for you or
                just tell the customer to find someone else? Now with this app you can easily create a task and then
                subcontract that task, mark up the task, and then easily get paid for the work.</p>
        </div>
        <div class="section pricing">
            <h2 style="color:black">Invoice Customers</h2>
            <p style="color:black">You can create an invoice at the beginning of a job and then use that as a commmunication tool
                throughout the jobs life cycle. You simply initiate a bid for all jobs. Then construct that bid,
                subcontract any of those tasks, and then submit the bid to the customer. The customer then has
                the ability to approve the bid. Once the bid has been approve then the work can be performed. Once the
                work has been performed the customer can now approve the finish work and pay using Stripe or cash.</p>
        </div>
        <div class="section features">
            <h2>Get Paid Easily</h2>
            <p>How much money is wasted chasing down a check? How much of a pain is it to have to write a check
                to a subcontractor after a job has been finished? With Stripe this will happen easily.
                The customer pays the invoice and then the money is automatically split between the contractor
                and the sub.</p>
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

<style scoped>

    .wrapper {
        display: grid;
        width: 100%;
    }

    .section {
        height: 630px;
        width: 100%;
    }

    .title {
        background-color: white;
    }

    .main {
        background-color: #203C4E;
    }

    .pricing {
        background-color: white;
    }

    .features {
        background-color: #378372;
    }

    h2 {
        color: rgb(255, 255, 255);
        padding: 3rem;
        font-family: "Open Sans", sans-serif;
        font-size: 40px;
        font-weight: 300;
        height: 88px;
        margin-left: 1rem;
        margin-top: 1rem;
    }

    p {
        color: rgb(255, 255, 255);
        padding-left: 4rem;
        padding-right: 4rem;
        padding-top: 3rem;
        font-family: "Open Sans", sans-serif;
        font-size: 18px;
        font-weight: 400;
        line-height: 27px;
        margin-top: 15px;
        /*width: 345px;*/
    }

    @media (min-width: 300px) {

    }

    @media (min-width: 1100px) {
        h2 {
            display: none;
        }
    }

</style>