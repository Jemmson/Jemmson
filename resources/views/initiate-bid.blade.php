@extends('spark::layouts.app')

@section('content')

    @if(Auth::user()->usertype == 'contractor')
        <initiate-bid>
        </initiate-bid>
    @elseif(Auth::user()->usertype == 'customer')

    @endif

@endsection