@extends('layouts.master')

@section('content')

    <!--
    things this page needs
-->

    <h1>{{ Auth::user()->name }}</h1>

    @if(Auth::user()->usertype == 'contractor')
        <h1>I am a contractor</h1>

        <form action="/home" method="post">

            {{ csrf_field() }}

            <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">

            <!-- Company Name -->
            <div class="form-group">
                <label class="col-md-4 control-label">Company Name</label>
                <div class="col-md-6">
                    <input type="text" class="form-control" name="company_name" autofocus>
                </div>
            </div>

            <!-- Phone Number -->
            <div class="form-group">
                <label class="col-md-4 control-label">Phone Number</label>
                <div class="col-md-6">
                    <input type="tel" class="form-control" name="phone_number" autofocus>
                </div>
            </div>

            <!-- Address Line 1 -->
            <div class="form-group">
                <label class="col-md-4 control-label">Address Line 1</label>
                <div class="col-md-6">
                    <input type="text" class="form-control" name="address_line_1" autofocus>
                </div>
            </div>

            <!-- Address Line 2 -->
            <div class="form-group">
                <label class="col-md-4 control-label">Address Line 2</label>
                <div class="col-md-6">
                    <input type="text" class="form-control" name="address_line_2" autofocus>
                </div>
            </div>

            <!-- City -->
            <div class="form-group">
                <label class="col-md-4 control-label">City</label>
                <div class="col-md-6">
                    <input type="text" class="form-control" name="city" autofocus>
                </div>
            </div>

            <!-- State -->
            <div class="form-group">
                <label class="col-md-4 control-label">State</label>
                <div class="col-md-6">
                    <input type="text" class="form-control" name="state" autofocus>
                </div>
            </div>

            <!-- Zip Code -->
            <div class="form-group">
                <label class="col-md-4 control-label">ZipCode</label>
                <div class="col-md-6">
                    <input type="text" class="form-control" name="zip" autofocus>
                </div>
            </div>

            <!-- upload company logo -->
            <div class="form-group">
                <label class="control-label">Please upload a company logo</label>
                <br>
                <input id="input-1" name="file_name" type="file" class="file">
            </div>

            <h3>Preferred Method of Contact</h3>
            <div class="preferred_contact" style="border: solid thin black">
                <div class="preferred_contact_input" style="margin-top: 2rem; margin-bottom: 2rem; margin-left: 2rem">
                    <!-- preferred method of contact -->
                    <div class="form-group">
                        <label class="col-md-4 control-label">Email</label>

                        <div class="col-md-6">
                            <input type="checkbox" name="email_contact">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label">Phone Call</label>

                        <div class="col-md-6">
                            <input type="checkbox" name="phone_contact">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label">SMS Text</label>

                        <div class="col-md-6">
                            <input type="checkbox" name="sms_text">
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" name="submit" class="btn btn-default btn-primary" style="margin-top: 1rem">Submit
            </button>

        </form>

    @elseif (Auth::user()->usertype == 'customer')
        <h1>I am a customer</h1>
    @endif

@endsection
