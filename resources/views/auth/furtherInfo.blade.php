@extends('spark::layouts.app')

@section('content')

    <!--
    things this page needs
-->

    <div class="container">
        <h1 class="text-center">{{ Auth::user()->name }}</h1>
         @include('spark::shared.errors')

        @if(Auth::user()->usertype == 'contractor')

            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Register Your Company</div>
                    <div class="panel-body">
                        <form class="form-horizontal" action="/home" method="post" role="form">

                            {{ csrf_field() }}

                            <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">

                            <!-- Company Name -->
                            <div class="form-group">
                                <label class="col-md-3 control-label">Company Name</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="company_name" autofocus>
                                </div>
                            </div>

                            <!-- Phone Number -->
                            <div class="form-group">
                                <label class="col-md-3 control-label">Phone Number</label>
                                <div class="col-md-8">
                                    <input type="tel" class="form-control" name="phone_number" autofocus>
                                </div>
                            </div>

                            <!-- Address Line 1 -->
                            <div class="form-group">
                                <label class="col-md-3 control-label">Address Line 1</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="address_line_1" autofocus>
                                </div>
                            </div>

                            <!-- Address Line 2 -->
                            <div class="form-group">
                                <label class="col-md-3 control-label">Address Line 2</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="address_line_2" autofocus>
                                </div>
                            </div>

                            <!-- City -->
                            <div class="form-group">
                                <label class="col-md-3 control-label">City</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="city" autofocus>
                                </div>
                            </div>

                            <!-- State -->
                            <div class="form-group">
                                <label class="col-md-3 control-label">State</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="state" autofocus>
                                </div>
                            </div>

                            <!-- Zip Code -->
                            <div class="form-group">
                                <label class="col-md-3 control-label">ZipCode</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="zip" autofocus>
                                </div>
                            </div>

                            @if($password_updated === 0)
                                <h3>Update Password</h3>
                                <div class="update_password" style="border: solid thin black">
                                    <div class="update_password_inputs"
                                         style="margin-top: 2rem; margin-bottom: 2rem; margin-left: 2rem">
                                        <!-- Update password -->
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">Password</label>

                                            <div class="col-md-8">
                                                <input type="password" name="password">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-3 control-label">Confirm Password</label>

                                            <div class="col-md-8">
                                                <input type="password" name="password_confirmation">
                                            </div>
                                        </div>
                                    </div>
                                </div>  
                            @endif

                            <div class="col-md-2"></div>
                            <div class="col-md-8">
                                <!-- upload company logo -->
                                <div class="form-group" style="margin-left: auto; margin-right: auto">
                                    <label class="control-label">Please upload a company logo</label>
                                    <br>
                                    <input id="input-1" name="file_name" type="file" class="file btn btn-primary">
                                </div>
                                <h3>Preferred Method of Contact</h3>
                                <div class="preferred_contact" style="border: solid thin black">
                                    <div class="preferred_contact_input"
                                         style="margin-top: 2rem; margin-bottom: 2rem; margin-left: 2rem">
                                        <!-- preferred method of contact -->
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">Email</label>

                                            <div class="col-md-8">
                                                <input type="checkbox" name="email_contact">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-3 control-label">Phone Call</label>

                                            <div class="col-md-8">
                                                <input type="checkbox" name="phone_contact">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-3 control-label">SMS Text</label>

                                            <div class="col-md-8">
                                                <input type="checkbox" name="sms_text">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" name="submit" class="btn btn-default btn-primary"
                                        style="margin-top: 1rem">
                                    Submit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
    </div>
    @elseif (Auth::user()->usertype == 'customer')
        <div class="container">

            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Please add some additional information</div>
                    <div class="panel-body">
                        <form class="form-horizontal" action="/home" method="post" role="form">

                            {{ csrf_field() }}

                            <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">

                            <!-- Phone Number -->
                            <div class="form-group">
                                <label class="col-md-3 control-label">Phone Number</label>
                                <div class="col-md-8">
                                    <input type="tel" class="form-control" name="phone_number" autofocus>
                                </div>
                            </div>

                            <!-- Address Line 1 -->
                            <div class="form-group">
                                <label class="col-md-3 control-label">Address Line 1</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="address_line_1" autofocus>
                                </div>
                            </div>

                            <!-- Address Line 2 -->
                            <div class="form-group">
                                <label class="col-md-3 control-label">Address Line 2</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="address_line_2" autofocus>
                                </div>
                            </div>

                            <!-- City -->
                            <div class="form-group">
                                <label class="col-md-3 control-label">City</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="city" autofocus>
                                </div>
                            </div>

                            <!-- State -->
                            <div class="form-group">
                                <label class="col-md-3 control-label">State</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="state" autofocus>
                                </div>
                            </div>

                            <!-- Zip Code -->
                            <div class="form-group">
                                <label class="col-md-3 control-label">ZipCode</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="zip" autofocus>
                                </div>
                            </div>

                            <!-- Notes -->
                            <div class="form-group">
                                <label class="col-md-3 control-label">Any Special Notes</label>
                                <div class="col-md-8">
                                    <textarea name="notes" id="notes" cols="30" rows="10" class="form-control"
                                              autofocus></textarea>
                                </div>
                            </div>

                            @if($password_updated === 0)
                            <h3>Update Password</h3>
                            <div class="update_password" style="border: solid thin black">
                                <div class="update_password_inputs"
                                     style="margin-top: 2rem; margin-bottom: 2rem; margin-left: 2rem">
                                    <!-- Update password -->
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Password</label>

                                        <div class="col-md-8">
                                            <input type="password" name="password">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Confirm Password</label>

                                        <div class="col-md-8">
                                            <input type="password" name="password_confirmation">
                                        </div>
                                    </div>
                                </div>
                            </div>  
                            @endif   

                            <h3>Preferred Method of Contact</h3>
                            <div class="preferred_contact" style="border: solid thin black">
                                <div class="preferred_contact_input"
                                     style="margin-top: 2rem; margin-bottom: 2rem; margin-left: 2rem">
                                    <!-- preferred method of contact -->
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Email</label>

                                        <div class="col-md-8">
                                            <input type="checkbox" name="email_method_of_contact">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Phone Call</label>

                                        <div class="col-md-8">
                                            <input type="checkbox" name="phone_method_of_contact">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 control-label">SMS Text</label>

                                        <div class="col-md-8">
                                            <input type="checkbox" name="sms_method_of_contact">
                                        </div>
                                    </div>
                                </div>
                            </div>
                       
                            <button type="submit" name="submit" class="btn btn-default btn-primary"
                                    style="margin-top: 1rem">Submit
                            </button>

                        </form>
                    </div>
                </div>
            </div>
        </div>

    @endif

@endsection
