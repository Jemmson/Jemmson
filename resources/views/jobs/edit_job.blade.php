@extends('spark::layouts.app')

@section('content')

    <job id="{{ $job->id }}"
         startdate="{{ explode(' ', $job->agreed_start_date)[0] }}"
         enddate="{{ explode(' ', $job->agreed_end_date)[0] }}"
         jobname="{{ $job->job_name }}"
         contractor="{{ $contractor }}"
         bidprice="{{ $job->bid_price }}"
    ></job>

<home :user="user" inline-template>
    <div class="container">
        <!-- Application Dashboard -->
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">{{ __('headings.bid.main') }}</div>
                    @include('spark::shared.errors')
                    @include('shared.success')
                    <div class="panel-body">
                      <form class="form-horizontal" role="form" action="/job/{{$job->id}}" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="_method" value="PUT">
                        <div class="form-group">
                            <label class="col-md-4 control-label">Job Name</label>

                            <div class="col-md-6">
                                <input type="text" class="form-control" value="{{ $job->job_name }}" disabled>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">Contractor</label>

                            <div class="col-md-6">
                                <input type="text" class="form-control" value="{{ $contractor }}" disabled>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">Job Start Date</label>

                            <div class="col-md-6">
                                <input type="date" class="form-control" name="agreed_start_date" value="{{ explode(' ', $job->agreed_start_date)[0] }}" autofocus required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">Job End Date</label>

                            <div class="col-md-6">
                                <input type="date" class="form-control" name="agreed_end_date" value="{{ explode(' ', $job->agreed_end_date)[0] }}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">Job Price</label>

                            <div class="col-md-6">
                                <input type="number" class="form-control" name="bid_price"  value="{{ $job->bid_price }}" required>
                            </div>
                        </div>
                        <!-- Save Button -->
                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa m-r-xs fa-sign-in"></i>Save
                                </button>
                            </div>
                        </div>
                      </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</home>
@endsection
