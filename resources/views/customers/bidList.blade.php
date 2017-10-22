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
                      <table class="table">
                        <tr>
                          <td>Job Name</td>
                          <td>Price</td>
                          <td>Completion Date</td>
                          <td>Status</td>
                          <td></td>
                        </tr>
                        <tbody>
                          @if ($jobs)
                            @foreach ($jobs as $key => $job)
                              <tr>
                                <td>{{ $job['job_name'] }}</td>
                                <td>${{ $job['bid_price']}} </td>
                                <td>{{ $job['completed_bid_date']}}</td>
                                <td>
                                  <select class="" onChange="onBidStatusChange({{$job['id']}})">
                                    <option value="completed">Completed</option>
                                    <option value="initiated">Initiated</option>
                                  </select>
                                </td>
                                <td><a href="/job/{{ $job['id'] }}/edit" class="btn btn-primary">Edit</a></td>
                              </tr>
                            @endforeach
                          @endif
                        </tbody>
                      </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</home>
@endsection
