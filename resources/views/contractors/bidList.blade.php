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
                          <td>Tasks Completed</td>
                          <td></td>
                        </tr>
                        <tbody>
                          @if ($bids)
                            @foreach ($bids as $key => $bid)
                              <tr>
                                <td>{{ $bid['jobName'] }}</td>
                                <td>${{ $bid['price']}} </td>
                                <td>{{ $bid['completionDate']}}</td>
                                <td><label class="label label-{{ $styles->getBidStatusLabelColor($bid['status']) }}">{{ $bid['status']}}</label></td>
                                <td>{{ $bid['tasksCompleted'] }}/{{ $bid['totalTasks'] }}</td>
                                <td><a href="/bid/{{ $bid['id'] }}" class="btn btn-primary">Edit</a></td>
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
