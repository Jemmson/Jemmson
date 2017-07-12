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
                      @for ($i=0; $i < 10; $i++)
                        <div><label>Bid #{{$i}} &nbsp</label><button class="btn info-btn" type="button" name="button">Pending</button></div>
                        <br>
                      @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>
</home>
@endsection
