@extends('spark::layouts.app')

@section('content')

    <job
            usertype="{{ $userType }}"
            p-job="{{ $job }}"
            p-bids="{{ $bids }}"
            p-contractor="{{ $contractor }}"
            p-customer="{{ $customer }}"
            p-tasks="{{ $tasks }}"
            >
    </job>

    <footer style="height: 10rem"></footer>

@endsection
