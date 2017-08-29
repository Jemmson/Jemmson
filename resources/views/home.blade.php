@extends('spark::layouts.app')

@section('content')
    <home :user="user" inline-template>
        <div class="container">
            <!-- Application Dashboard -->
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <div class="panel panel-default">
                        <div class="panel-heading">Dashboard</div>
                        <div class="panel-body">
                            @include('spark::shared.errors')
                            @include('shared.success')
                            <ul>
                                <li><a href="/contractor/initiate-bid">initiate bid</a></li>
                                <li><a href="/contractor/bid-list">bid list</a></li>

                                @{{ upperName }}

                                <registration :name="Shawn"></registration>

                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </home>

@endsection
