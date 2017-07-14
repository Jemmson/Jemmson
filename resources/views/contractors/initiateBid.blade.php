@extends('spark::layouts.app')

@section('content')
<home :user="user" inline-template>
    <div class="container">
        <!-- Application Dashboard -->
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Initate Bid</div>

                    <div class="panel-body">
                      <form action="/contractor/initiate-bid/" method="post">

                          {{ csrf_field() }}

                          <label for="job-name">Job Name *
                              <input name="job-name" type="text" id="job-name" class="form-control">
                          </label>

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
                          <button class="btn btn-default btn-primary" >Submit</button>
                      </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</home>
@endsection
