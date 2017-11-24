@extends('spark::layouts.app')

@section('content')

    <job job="{{ $job }}" contractor="{{ $contractor }}" customer="{{ $customer }}"></job>

@endsection
