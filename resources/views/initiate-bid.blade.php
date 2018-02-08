@extends('spark::layouts.app')

@section('content')

    @if(Auth::user()->usertype == 'contractor')
        <div class="container">
            <!-- Application Dashboard -->
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <div class="panel panel-default">
                        {{-- / --}}
                        <span>{{ session('error') }}</span>
                        <div class="panel-heading">Initiate Bid</div>

                        <div class="panel-body">
                            <form action="/initiate-bid/" method="post">

                                {{ csrf_field() }}
                                <initiate-bid></initiate-bid>
                                <button name="submit" class="btn btn-default btn-primary">Submit</button>
                            </form>
                        </div>
                        @include('misc.errors')
                    </div>
                </div>
            </div>
            <hr>
        </div>
    @elseif(Auth::user()->usertype == 'customer')

    @endif

@endsection