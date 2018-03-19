@extends('spark::layouts.app')

@section('content')
    <?php
        use App\Location;
    ?>
    @if(Auth::user() && empty(Location::select()->where("user_id","=",Auth::user()->id)->get()->first()->id))
        <div class="container">
            <further-info :user="user">
            </further-info>
        </div>
    @else
        <?php
            return back();
        ?>
    @endif
@endsection