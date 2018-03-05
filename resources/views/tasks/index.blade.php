@extends('spark::layouts.app')

@section('content')
    <!-- /assumed they have tasks? should we need to check if they are a subcontractor? -->
    <subcontractor-tasks :user="user" :bid-tasks="{{ $tasks }}">
    </subcontractor-tasks>
@endsection