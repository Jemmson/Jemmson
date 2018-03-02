@extends('spark::layouts.app')

@section('content')
        <bidlist :user="user" :pbids="{{ $jobs }}">
        </bidlist>
@endsection