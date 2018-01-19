@inject('styles', 'App\Services\StylesService');
@extends('spark::layouts.app')

@section('content')

        <bidlist :user="user" :bids="{{ $jobs }}">
        </bidlist>

@endsection