@inject('styles', 'App\Services\StylesService');
@extends('spark::layouts.app')

@section('content')

    @if(Auth::user()->usertype == 'contractor')
        @include('contractors.bidList')
    @elseif(Auth::user()->usertype == 'customer')

    @endif

@endsection