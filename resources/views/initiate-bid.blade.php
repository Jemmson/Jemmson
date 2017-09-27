@extends('spark::layouts.app')

@section('content')

    @if(Auth::user()->usertype == 'contractor')
        <div class="container">
            <!-- Application Dashboard -->
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <div class="panel panel-default">
                        {{-- / --}}
                        <span>{{ session('error') }}</span>
                        <div class="panel-heading">Initiate Bid</div>

                        <div class="panel-body">
                            <form action="/initiate-bid/" method="post">

                                {{ csrf_field() }}


                                    <initiate-bid></initiate-bid>



                                <!--<div class="btn-group" data-toggle="buttons">-->
                                <!--<label class="btn btn-info active">-->
                                <!--<input type="radio" name="options" id="customer" autocomplete="off">Customer-->
                                <!--</label>-->
                                <!--<label class="btn btn-info">-->
                                <!--<input type="radio" name="options" id="subcontractor" autocomplete="off">Sub Contractor-->
                                <!--</label>-->
                                <!--</div>-->



                                <button name="submit" class="btn btn-default btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @elseif(Auth::user()->usertype == 'customer')

    @endif

@endsection