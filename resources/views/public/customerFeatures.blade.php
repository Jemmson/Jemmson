@extends('spark::layouts.app')

@section('content')
<div class="container">
    <h1 class="text-center">Exciting Customer Features</h1>
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default  panel--styled">
                <div class="panel-body">
                    <div class="col-md-12 panelTop">
                        <div class="col-md-8">
                            <h2>Easily Track Jobs</h2>
                            <p>As a customer you will be able to easily track jobs
                                of each contractor or subcontractor</p>
                        </div>
                    </div>
                    <div class="col-md-12 panelBottom">
                        <a href="/public/customerJobTracking">
                            <button class="btn btn-default btn-primary">
                                Learn More
                            </button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default  panel--styled">
                <div class="panel-body">
                    <div class="col-md-12 panelTop">
                        <div class="col-md-8">
                            <h2>Security</h2>
                            <p>Credit Card security is paramount for our site. Your Credit Card is
                                protected by Stripe and the contractor will never have access to your
                                credit card information.</p>
                        </div>
                    </div>

                    <div class="col-md-12 panelBottom">
                        <a href="/public/customerSecurity">
                            <button class="btn btn-default btn-primary">
                                Learn More
                            </button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default  panel--styled">
                <div class="panel-body">
                    <div class="col-md-12 panelTop">
                        <div class="col-md-8">
                            <h2>Invoice Management</h2>
                            <p>Every Invoice that is generated between you and the
                            contractor will be protected and archived whether you continue
                            to have an account with us or not.</p>
                        </div>
                    </div>
                    <div class="col-md-12 panelBottom">
                        <a href="/public/customerInvoiceManagement">
                            <button class="btn btn-default btn-primary">
                                Learn More
                            </button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default  panel--styled">
                <div class="panel-body">
                    <div class="col-md-12 panelTop">
                        <div class="col-md-8">
                            <h2>Communication</h2>
                            <p>You will be able to easily communicate with your contractor
                            and recieve communications from them. you will be able to send them
                            notifications and emails through the application</p>
                        </div>
                    </div>

                    <div class="col-md-12 panelBottom">
                        <a href="/public/customerCommunication">
                            <button class="btn btn-default btn-primary">
                                Learn More
                            </button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection