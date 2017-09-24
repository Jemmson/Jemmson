@extends('spark::layouts.app')

@section('content')

    @if(isset($user))
       
        @if($user->usertype == 'contractor')

            <div class="container" style="margin-top: 2rem">
                <div class="row" style="margin-top: 2rem">
                    <div class="col-md-4">
                        <img src="{{ asset('/img/color-logo.png', false) }}"
                             class="img-rounded" alt="spark" width="" height="">
                    </div>
                    <div class="col-md-2"></div>
                    <div class="col-md-6">
                        <h1>{{ $user->getDetails()->company_name }}</h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="panel panel-default  panel--styled">
                            <div class="panel-body">
                                <div class="col-md-12 panelTop">
                                    <div class="col-md-8">
                                        <h2>Initiate Bid</h2>
                                        <p>Select me to intiate a bid with a customer</p>
                                    </div>
                                </div>
                                <div class="col-md-12 panelBottom">
                                    <a href="/initiate-bid">
                                        <button class="btn btn-default btn-primary">
                                            Initiate Bid
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
                                        <h2>Bid List</h2>
                                        <p>Select me to look at all of your bids</p>
                                    </div>
                                </div>

                                <div class="col-md-12 panelBottom">
                                    <a href="/contractor/bid-list">
                                        <button class="btn btn-default btn-primary">
                                            Bid List
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
                    <div class="col-md-6">
                        <div class="panel panel-default  panel--styled">
                            <div class="panel-body">
                                <div class="col-md-12 panelTop">
                                    <div class="col-md-8">
                                        <h2>Ratings</h2>
                                        <p>Select me to to look at all of my ratings</p>
                                    </div>
                                </div>

                                <div class="col-md-12 panelBottom">
                                    <a href="">
                                        <button class="btn btn-default btn-primary">
                                            Ratings
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
                                        <h2>My Sub Contractors</h2>
                                        <p>Select me to look at your sub contractors</p>
                                    </div>
                                </div>
                                <div class="col-md-12 panelBottom">
                                    <a href="">
                                        <button class="btn btn-default btn-primary">
                                            My Sub Contractors
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
                                        <h2>Settings</h2>
                                        <p>Select me to look at your settings</p>
                                    </div>
                                </div>
                                <div class="col-md-12 panelBottom">
                                    <a href="/settings">
                                        <button class="btn btn-default btn-primary">
                                            Settings
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
                                        <p>Select me to look at your previous bids</p>
                                    </div>
                                </div>
                                <div class="col-md-12 panelBottom">
                                    <a href="/previousbids">
                                        <button class="btn btn-default btn-primary">
                                            Previous Bids
                                        </button>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
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
                                        <h2>Initiate Bid</h2>
                                        <p>Select me to intiate a bid with a contractor</p>
                                    </div>
                                </div>
                                <div class="col-md-12 panelBottom">
                                    <a href="">
                                        <button class="btn btn-default btn-primary">
                                            Initiate Bid
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
                                        <h2>Bid List</h2>
                                        <p>Select me to look at all of your current bids</p>
                                    </div>
                                </div>

                                <div class="col-md-12 panelBottom">
                                    <a href="">
                                        <button class="btn btn-default btn-primary">
                                            Bid List
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
                    <div class="col-md-6">
                        <div class="panel panel-default  panel--styled">
                            <div class="panel-body">
                                <div class="col-md-12 panelTop">
                                    <div class="col-md-8">
                                        <h2>Settings</h2>
                                        <p>Select me to to look at your settings</p>
                                    </div>
                                </div>

                                <div class="col-md-12 panelBottom">
                                    <a href="">
                                        <button class="btn btn-default btn-primary">
                                            Settings
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
