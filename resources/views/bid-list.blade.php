@inject('styles', 'App\Services\StylesService');
@extends('spark::layouts.app')

@section('content')

    @if(Auth::user()->usertype == 'contractor')
        @if(Session::has('status'))
            <div class="container">
                <div class="row">
                    <div class="col-md-4"></div>
                    <div class="col-md-4">
                        <div class="alert alert-success">
                            {{ Session::get('status') }}
                        </div>
                    </div>
                    <div class="col-md-4"></div>
                </div>

            </div>


        @endif
        @include('contractors.bidList')
    @elseif(Auth::user()->usertype == 'customer')
        @include('customers.bidList')
    @endif

@endsection