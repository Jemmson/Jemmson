@extends('spark::layouts.app')

@section('content')

    <job usertype="{{ $userType }}" job="{{ $job }}" contractor="{{ $contractor }}" customer="{{ $customer }}" tasks="{{ $tasks }}"></job>

    <footer style="height: 10rem"></footer>

@endsection
