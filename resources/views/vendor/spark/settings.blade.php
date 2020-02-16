@extends('spark::layouts.app')

@section('scripts')
    @if (Spark::billsUsingStripe())
        <script src="https://js.stripe.com/v3/"></script>
    @else
        <script src="https://js.braintreegateway.com/v2/braintree.js"></script>
    @endif
@endsection

@section('content')
    <spark-settings :user="user" :teams="teams" inline-template>
        <div class="spark-screen container">
            <div class="row">
                <!-- Tabs -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">
                                Settings
                            </div>
                            <div class="spark-settings-tabs">
                                <div class="flex space-evenly nav spark-settings-stacked-tabs" role="tablist">
                                    <!-- Profile Link -->
                                    <div role="presentation">
                                        <a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">
                                            <i class="fa fa-fw fa-btn fa-edit mr-half-rem"></i>Profile
                                        </a>
                                    </div>

                                    <!-- Security Link -->
                                    <div role="presentation">
                                        <a href="#security" aria-controls="security" role="tab" data-toggle="tab">
                                            <i class="fa fa-fw fa-btn fa-lock mr-half-rem"></i>Change Password
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Billing Tabs -->
                    @if (Spark::canBillCustomers())
                        <div class="card mt-2 mb-2">
                            <div class="card-body">
                                <div class="card-title">
                                    Billing
                                </div>
                                <div class="spark-settings-tabs">
                                    <div class="flex space-evenly  nav spark-settings-stacked-tabs" role="tablist">
                                        <!-- Subscription Link -->
                                        <div role="presentation">
                                            <a href="#subscription" aria-controls="subscription" role="tab"
                                               data-toggle="tab">
                                                <i class="fa fa-fw fa-btn fa-shopping-bag mr-half-rem"></i>Subscription
                                            </a>
                                        </div>

                                        <!-- Payment Method Link -->
                                        <div role="presentation">
                                            <a href="#payment-method" aria-controls="payment-method" role="tab"
                                               data-toggle="tab">
                                                <i class="fa fa-fw fa-btn fa-credit-card mr-half-rem"></i>Payment Method
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Tab Panels -->
                <div class="col-md-8">
                    <div class="tab-content">
                        <!-- Profile -->
                        <div role="tabpanel" class="tab-pane active" id="profile">
                            @include('spark::settings.profile')
                        </div>

                        <!-- Teams -->
                        @if (Spark::usesTeams())
                            <div role="tabpanel" class="tab-pane" id="{{str_plural(Spark::teamString())}}">
                                @include('spark::settings.teams')
                            </div>
                        @endif

                    <!-- Security -->
                        <div role="tabpanel" class="tab-pane" id="security">
                            @include('spark::settings.security')
                        </div>

                        <!-- API -->
                        @if (Spark::usesApi())
                            <div role="tabpanel" class="tab-pane" id="api">
                                @include('spark::settings.api')
                            </div>
                        @endif

                    <!-- Billing Tab Panes -->
                        @if (Spark::canBillCustomers())
                            @if (Spark::hasPaidPlans())
                            <!-- Subscription -->
                                <div role="tabpanel" class="tab-pane" id="subscription">
                                    <div v-if="user">
                                        @include('spark::settings.subscription')
                                    </div>
                                </div>
                            @endif

                        <!-- Payment Method -->
                            <div role="tabpanel" class="tab-pane" id="payment-method">
                                <div v-if="user">
                                    @include('spark::settings.payment-method')
                                </div>
                            </div>

                            <!-- Invoices -->
                            <div role="tabpanel" class="tab-pane" id="invoices">
                                @include('spark::settings.invoices')
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </spark-settings>
@endsection
