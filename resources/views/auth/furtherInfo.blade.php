@extends('spark::layouts.app')

@section('content')
    <div class="container">
            <further-info :user="user">
            </further-info>
    </div>
@endsection