@extends('spark::layouts.app')

@section('content')

    <h1>Edit Job</h1>

    {{ $userType }} <br>
    {{ $job }} <br>
    {{ $bids }} <br>
    {{ $contractor }} <br>
    {{ $customer }}<br>
    {{ $tasks }} <br>

    <job
            usertype="{{ $userType }}"
            :job="{{ $job }}"
            :bids="{{ $bids }}"
            :contractor="{{ $contractor }}"
            :customer="{{ $customer }}"
            :tasks="{{ $tasks }}"
            >
    </job>

    <footer style="height: 10rem"></footer>

@endsection
