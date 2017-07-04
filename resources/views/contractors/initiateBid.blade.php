@extends('layouts.master')

@section('content')

    <h2 class="center">Initiates Bid</h2>
    <!--<form action="ContractorBidList">-->
    <form action="/initiate-bid/" method="get">

        {{ csrf_field() }}

        <label for="email">Email *
            <input name="email" type="email" id="email" class="form-control">
        </label>
        <label for="phone">Phone
            <input name="phone" type="text" id="phone" class="form-control">
        </label><br>
        <!--<div class="btn-group" data-toggle="buttons">-->
        <!--<label class="btn btn-info active">-->
        <!--<input type="radio" name="options" id="customer" autocomplete="off">Customer-->
        <!--</label>-->
        <!--<label class="btn btn-info">-->
        <!--<input type="radio" name="options" id="subcontractor" autocomplete="off">Sub Contractor-->
        <!--</label>-->
        <!--</div>-->
        <br>
        <button class="btn btn-default btn-primary" @click.prevent="sendEmail">Submit</button>
    </form>

@endsection