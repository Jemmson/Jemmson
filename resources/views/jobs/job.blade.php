@extends('spark::layouts.app')

@section('content')
<home :user="user" inline-template>
    <div class="container">
        <!-- Application Dashboard -->
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">{{ __('headings.bid.main') }}</div>

                    <div class="panel-body">
                      <h3>{{$job->job_name}}</h3>
                      <h4>created at: {{$job->created_at}} by: {{$contractor}}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</home>
@endsection
