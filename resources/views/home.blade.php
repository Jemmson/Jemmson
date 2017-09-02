@extends('spark::layouts.app')

@section('content')

    @if(isset($user))

        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"
              integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u"
              crossorigin="anonymous">

        @if(Auth::user()->usertype == 'contractor')

            <div class="container" style="margin-top: 2rem">
                <div class="row" style="margin-top: 2rem">
                    <div class="col-md-4">
                        <img src="{{ asset('/img/color-logo.png', false) }}"
                             class="img-rounded" alt="spark" width="" height="">
                    </div>
                    <div class="col-md-2"></div>
                    <div class="col-md-6">
                        <h1>{{ $user->company_name }}</h1>
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
                                    <a href="/contractor/initiate-bid">
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
            </div>

        @elseif (Auth::user()->usertype == 'customer')
            <h1>I am a customer</h1>
            <h1>{{ $user->user_id }}</h1>
        @endif

        <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js"
                integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n"
                crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js"
                integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb"
                crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
                integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
                crossorigin="anonymous"></script>

    @endif

@endsection
