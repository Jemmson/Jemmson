@extends('spark::layouts.app')

@section('content')

    @if(isset($user))

        @if($user->usertype == 'contractor')

            <div class="container" style="margin-top: 2rem">
                <div class="row" style="margin-top: 2rem">
                    <h1 class="text-center">{{ $user->getDetails()->company_name }}</h1>
                </div>
                <div class="btn-group home-nav">
                    <a class="btn btn-primary btn-large" href="/initiate-bid">Initiate Bid</a>
                    <a class="btn btn-primary btn-large" href="/bid/tasks">Bid Tasks</a>
                    <a class="btn btn-primary btn-large" href="/bid-list">Bid Lists</a>
                    <a class="btn btn-primary btn-large" href="/settings">Settings</a>
                    <express-dashboard-stripe>
                    </express-dashboard-stripe>
                </div>
            </div>

        @elseif ($user->usertype == 'customer')

            <div class="container" style="margin-top: 2rem">
                <div class="row" style="margin-top: 2rem">
                    <div class="col-md-4">
                    </div>
                    <div class="col-md-4">
                        <h1>{{ $user->name }}</h1>
                    </div>
                    <div class="col-md-4">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="panel panel-default  panel--styled">
                            <div class="panel-body">
                                <div class="col-md-12 panelTop">
                                    <div class="col-md-8">
                                        <h2>Bid List</h2>
                                        <p>Select me to look at all of your current bids</p>
                                    </div>
                                </div>

                                <div class="col-md-12 panelBottom">
                                    <a href="/bid-list">
                                        <button class="btn btn-default btn-primary">
                                            Bid List
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
                                        <h2>Previous Bids</h2>
                                        <p>Select me to look at all of your previous bids</p>
                                    </div>
                                </div>
                                <div class="col-md-12 panelBottom">
                                    <a href="">
                                        <button class="btn btn-default btn-primary">
                                            Previous Bids
                                        </button>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default  panel--styled">
                            <div class="panel-body">
                                <div class="col-md-12 panelTop">
                                    <div class="col-md-8">
                                        <h2>Type of Work</h2>
                                        <p>Select me to look at the type of work
                                            that I do and the prices that I charge</p>
                                    </div>
                                </div>
                                <div class="col-md-12 panelBottom">
                                    <a href="">
                                        <button class="btn btn-default btn-primary">
                                            Type of work
                                        </button>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        @endif

    @endif

@endsection
