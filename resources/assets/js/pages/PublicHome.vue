<template>
  <div class="welcome-page-wrapper">
    <div class="welcome-page-invoices beige">
      <h1 class="create-invoices">Subcontract Indivdual Tasks</h1>
      <img class="create-invoices" src="/img/invoice.png" style="height: 200px;">
    </div>
    <div class="welcome-page-initiate-bid blue">
      <h1 class="create-invoices">Initiate Bids/Invoices</h1>
      <img class="create-invoices" src="/img/invoice.png" style="height: 200px;">
    </div>
    <div class="welcome-page-task beige">
      <h1 class="create-invoices">Bid on Tasks</h1>
      <img class="create-invoices" src="/img/invoice.png" style="height: 200px;">
    </div>
    <div class="welcome-page-powered-by blue">
      <h1 class="create-invoices">Powered By:</h1>
      <div class="powered-by-content">
        <a class="rootLink item-home colorize" href="/" aria-label="Stripe homepage"><h1><svg width="62" height="25"><title>Stripe</title><path d="M5 10.1c0-.6.6-.9 1.4-.9 1.2 0 2.8.4 4 1.1V6.5c-1.3-.5-2.7-.8-4-.8C3.2 5.7 1 7.4 1 10.3c0 4.4 6 3.6 6 5.6 0 .7-.6 1-1.5 1-1.3 0-3-.6-4.3-1.3v3.8c1.5.6 2.9.9 4.3.9 3.3 0 5.5-1.6 5.5-4.5.1-4.8-6-3.9-6-5.7zM29.9 20h4V6h-4v14zM16.3 2.7l-3.9.8v12.6c0 2.4 1.8 4.1 4.1 4.1 1.3 0 2.3-.2 2.8-.5v-3.2c-.5.2-3 .9-3-1.4V9.4h3V6h-3V2.7zm8.4 4.5L24.6 6H21v14h4v-9.5c1-1.2 2.7-1 3.2-.8V6c-.5-.2-2.5-.5-3.5 1.2zm5.2-2.3l4-.8V.8l-4 .8v3.3zM61.1 13c0-4.1-2-7.3-5.8-7.3s-6.1 3.2-6.1 7.3c0 4.8 2.7 7.2 6.6 7.2 1.9 0 3.3-.4 4.4-1.1V16c-1.1.6-2.3.9-3.9.9s-2.9-.6-3.1-2.5H61c.1-.2.1-1 .1-1.4zm-7.9-1.5c0-1.8 1.1-2.5 2.1-2.5s2 .7 2 2.5h-4.1zM42.7 5.7c-1.6 0-2.5.7-3.1 1.3l-.1-1h-3.6v18.5l4-.7v-4.5c.6.4 1.4 1 2.8 1 2.9 0 5.5-2.3 5.5-7.4-.1-4.6-2.7-7.2-5.5-7.2zm-1 11c-.9 0-1.5-.3-1.9-.8V10c.4-.5 1-.8 1.9-.8 1.5 0 2.5 1.6 2.5 3.7 0 2.2-1 3.8-2.5 3.8z"></path></svg></h1></a>
        <img class="create-invoices" src="/img/invoice.png" style="height: 200px;">
      </div>
    </div>

    <!--<div class="row">-->
      <!--<div class="col-md-6">-->
        <!--<div class="panel panel-default  panel&#45;&#45;styled">-->
          <!--<div class="panel-body">-->
            <!--<div class="col-md-12 panelTop">-->
              <!--<div class="col-md-8">-->
                <!--<h2>Easily Track Jobs</h2>-->
                <!--<p>As a contractor you will be able to easily track jobs of each customer or subcontractor</p>-->
              <!--</div>-->
            <!--</div>-->
            <!--<div class="col-md-12 panelBottom">-->
              <!--<a href="/public/contractorJobTracking">-->
                <!--<button class="btn btn-default btn-primary">-->
                  <!--Learn More-->
                <!--</button>-->
              <!--</a>-->
            <!--</div>-->
          <!--</div>-->
        <!--</div>-->
      <!--</div>-->
      <!--<div class="col-md-6">-->
        <!--<div class="panel panel-default  panel&#45;&#45;styled">-->
          <!--<div class="panel-body">-->
            <!--<div class="col-md-12 panelTop">-->
              <!--<div class="col-md-8">-->
                <!--<h2>Security</h2>-->
                <!--<p>Credit Card security is paramount for our site. Your Credit Card is protected by Stripe and the customer-->
                  <!--or subcontractor will never have access to your credit card information.</p>-->
              <!--</div>-->
            <!--</div>-->

            <!--<div class="col-md-12 panelBottom">-->
              <!--<a href="/public/contractorSecurity">-->
                <!--<button class="btn btn-default btn-primary">-->
                  <!--Learn More-->
                <!--</button>-->
              <!--</a>-->
            <!--</div>-->
          <!--</div>-->
        <!--</div>-->
      <!--</div>-->
    <!--</div>-->
    <!--<div class="row">-->
      <!--<div class="col-md-6">-->
        <!--<div class="panel panel-default  panel&#45;&#45;styled">-->
          <!--<div class="panel-body">-->
            <!--<div class="col-md-12 panelTop">-->
              <!--<div class="col-md-8">-->
                <!--<h2>Invoice Management</h2>-->
                <!--<p>Every Invoice that is generated between you and the customer or contractor will be protected and archived-->
                  <!--whether you continue to have an account with us or not.</p>-->
              <!--</div>-->
            <!--</div>-->
            <!--<div class="col-md-12 panelBottom">-->
              <!--<a href="/public/contractorInvoiceManagement">-->
                <!--<button class="btn btn-default btn-primary">-->
                  <!--Learn More-->
                <!--</button>-->
              <!--</a>-->
            <!--</div>-->
          <!--</div>-->
        <!--</div>-->
      <!--</div>-->
      <!--<div class="col-md-6">-->
        <!--<div class="panel panel-default  panel&#45;&#45;styled">-->
          <!--<div class="panel-body">-->
            <!--<div class="col-md-12 panelTop">-->
              <!--<div class="col-md-8">-->
                <!--<h2>Communication</h2>-->
                <!--<p>You will be able to easily communicate with your customers and subcontractors and recieve communications-->
                  <!--from them. you will be able to send them notifications and emails through the application</p>-->
              <!--</div>-->
            <!--</div>-->

            <!--<div class="col-md-12 panelBottom">-->
              <!--<a href="/public/contractorCommunication">-->
                <!--<button class="btn btn-default btn-primary">-->
                  <!--Learn More-->
                <!--</button>-->
              <!--</a>-->
            <!--</div>-->
          <!--</div>-->
        <!--</div>-->
      <!--</div>-->
    <!--</div>-->
    <!--&lt;!&ndash; / end contractor features &ndash;&gt;-->

    <!--<div class="row">-->
      <!--<div class="col-md-6">-->
        <!--<div class="panel panel-default  panel&#45;&#45;styled">-->
          <!--<div class="panel-body">-->
            <!--<div class="col-md-12 panelTop">-->
              <!--<div class="col-md-8">-->
                <!--<h2>Easily Track Jobs</h2>-->
                <!--<p>As a customer you will be able to easily track jobs of each contractor or subcontractor</p>-->
              <!--</div>-->
            <!--</div>-->
            <!--<div class="col-md-12 panelBottom">-->
              <!--<a href="/public/customerJobTracking">-->
                <!--<button class="btn btn-default btn-primary">-->
                  <!--Learn More-->
                <!--</button>-->
              <!--</a>-->
            <!--</div>-->
          <!--</div>-->
        <!--</div>-->
      <!--</div>-->
      <!--<div class="col-md-6">-->
        <!--<div class="panel panel-default  panel&#45;&#45;styled">-->
          <!--<div class="panel-body">-->
            <!--<div class="col-md-12 panelTop">-->
              <!--<div class="col-md-8">-->
                <!--<h2>Security</h2>-->
                <!--<p>Credit Card security is paramount for our site. Your Credit Card is protected by Stripe and the contractor-->
                  <!--will never have access to your credit card information.</p>-->
              <!--</div>-->
            <!--</div>-->

            <!--<div class="col-md-12 panelBottom">-->
              <!--<a href="/public/customerSecurity">-->
                <!--<button class="btn btn-default btn-primary">-->
                  <!--Learn More-->
                <!--</button>-->
              <!--</a>-->
            <!--</div>-->
          <!--</div>-->
        <!--</div>-->
      <!--</div>-->
    <!--</div>-->
    <!--<div class="row">-->
      <!--<div class="col-md-6">-->
        <!--<div class="panel panel-default  panel&#45;&#45;styled">-->
          <!--<div class="panel-body">-->
            <!--<div class="col-md-12 panelTop">-->
              <!--<div class="col-md-8">-->
                <!--<h2>Invoice Management</h2>-->
                <!--<p>Every Invoice that is generated between you and the contractor will be protected and archived whether you-->
                  <!--continue to have an account with us or not.</p>-->
              <!--</div>-->
            <!--</div>-->
            <!--<div class="col-md-12 panelBottom">-->
              <!--<a href="/public/customerInvoiceManagement">-->
                <!--<button class="btn btn-default btn-primary">-->
                  <!--Learn More-->
                <!--</button>-->
              <!--</a>-->
            <!--</div>-->
          <!--</div>-->
        <!--</div>-->
      <!--</div>-->
      <!--<div class="col-md-6">-->
        <!--<div class="panel panel-default  panel&#45;&#45;styled">-->
          <!--<div class="panel-body">-->
            <!--<div class="col-md-12 panelTop">-->
              <!--<div class="col-md-8">-->
                <!--<h2>Communication</h2>-->
                <!--<p>You will be able to easily communicate with your contractor and recieve communications from them. you will-->
                  <!--be able to send them notifications and emails through the application</p>-->
              <!--</div>-->
            <!--</div>-->

            <!--<div class="col-md-12 panelBottom">-->
              <!--<a href="/public/customerCommunication">-->
                <!--<button class="btn btn-default btn-primary">-->
                  <!--Learn More-->
                <!--</button>-->
              <!--</a>-->
            <!--</div>-->
          <!--</div>-->
        <!--</div>-->
      <!--</div>-->
    <!--</div>-->
  </div>
</template>

<script>
  export default {
    computed: {},
    methods: {
      route(value) {
        this.$router.push(value)
      }
    }
  }
</script>