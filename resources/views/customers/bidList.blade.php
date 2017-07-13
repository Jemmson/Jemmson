@extends('spark::layouts.app')

@section('content')
<home :user="user" inline-template>
    <div class="container">
        <!-- Application Dashboard -->
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">{{ __('headings.bidlist.main') }}</div>

                    <div class="panel-body">
                      @if ($bids)
                        @foreach ($bids as $key => $bid)
                          <div><label>{{$bid['jobName']}} &nbsp</label><button class="btn info-btn" type="button" name="button">{{$bid['status']}}</button></div>
                          <br>
                        @endforeach
                      @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</home>
@endsection
