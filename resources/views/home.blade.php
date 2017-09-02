@extends('spark::layouts.app')

@section('content')

    @if(isset($user))

        @if(Auth::user()->usertype == 'contractor')
            <h1>I am a contractor</h1>
            <h1>{{ $user->user_id }}</h1>
            <ul>
                <li><a href="/contractor/initiate-bid">Initiate Bid</a></li>
                <li><a href="/contractor/bid-list">Bid List</a></li>
            </ul>
        @elseif (Auth::user()->usertype == 'customer')
            <h1>I am a customer</h1>
            <h1>{{ $user->user_id }}</h1>
        @endif

    @endif

@endsection
