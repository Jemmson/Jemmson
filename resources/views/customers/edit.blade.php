@extends('spark::layouts.app')

@section('content')
<home :user="user" inline-template>
    <div class="container">
        <!-- Application Dashboard -->
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">{{ __('headings.customer.edit.main') }}</div>
                    @include('spark::shared.errors')
                    <div class="panel-body">

                      <form class="form-horizontal" role="form" method="POST" action="/customer/{{$data['customer']->id}}">
                          {{ csrf_field() }}
                          <input type="hidden" name="_method" value="PUT">
                          <input type="hidden" name="job_id" value="{{$data['job_id']}}">
                          <!-- E-Address Line 1 -->
                          <div class="form-group">
                              <label class="col-md-4 control-label">Address Line 1</label>

                              <div class="col-md-6">
                                  <input type="test" class="form-control" name="address_line_1" value="{{ $data['customer']->address_line_1 }}" autofocus>
                              </div>
                          </div>

                          <!-- Address Line 2 -->
                          <div class="form-group">
                              <label class="col-md-4 control-label">Address Line 2</label>

                              <div class="col-md-6">
                                  <input type="test" class="form-control" name="address_line_2" value="{{ $data['customer']->address_line_2 }}">
                              </div>
                          </div>

                          <!-- City -->
                          <div class="form-group">
                              <label class="col-md-4 control-label">City</label>

                              <div class="col-md-6">
                                  <input type="text" class="form-control" name="city" value="{{ $data['customer']->city }}">
                              </div>
                          </div>

                          <!-- State -->
                          <div class="form-group">
                              <label class="col-md-4 control-label">State</label>

                              <div class="col-md-6">
                                  <input type="text" class="form-control" name="state" value="{{ $data['customer']->state }}">
                              </div>
                          </div>

                          <!-- zip -->
                          <div class="form-group">
                              <label class="col-md-4 control-label">Zip</label>

                              <div class="col-md-6">
                                  <input type="text" class="form-control" name="zip" value="{{ $data['customer']->zip }}">
                              </div>
                          </div>

                          <!-- preferred_method_of_contact -->
                          <div class="form-group">
                              <label class="col-md-4 control-label">preferred_method_of_contact</label>

                              <div class="col-md-6">
                                  <input type="text" class="form-control" name="preferred_method_of_contact" value="{{ $data['customer']->preferred_method_of_contact }}">
                              </div>
                          </div>

                          <!-- sms text -->
                          <div class="form-group">
                              <div class="col-md-6 col-md-offset-4">
                                  <div class="checkbox">
                                      <label>
                                          <input type="checkbox" name="sms_text"> Opt into SMS notifications
                                      </label>
                                  </div>
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
