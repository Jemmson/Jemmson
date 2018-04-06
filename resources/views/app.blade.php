<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta Information -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', config('app.name'))</title>

    <!-- Fonts -->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600' rel='stylesheet' type='text/css'>
    <script defer src="https://use.fontawesome.com/releases/v5.0.8/js/all.js"></script>


    <!-- CSS -->
    <link href="/css/sweetalert.css" rel="stylesheet">
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">

    <!-- Scripts -->
@yield('scripts', '')

<!-- Global Spark Object -->
    <script>
      window.Spark = <?php echo json_encode(array_merge(
          Spark::scriptVariables(), []
      )); ?>;
    </script>
</head>
<body class="with-navbar">
<div id="spark-app" v-cloak>
    <!-- Navigation -->
@if (Auth::check())
    @include('spark::nav.user')
@else
    @include('spark::nav.guest')
@endif

<!-- Main Content -->
<transition name="fade">
    <router-view :user='user'></router-view>
</transition>


<!-- Application Level Modals -->
    @if (Auth::check())
        @include('spark::modals.notifications')
        @include('spark::modals.support')
        @include('spark::modals.session-expired')
        <feedback>
        </feedback>
    @endif
</div>

<!-- JavaScript -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC2uX7JbkzcTpZ7Nb-_nu0CagS4lBSNdDw&libraries=places"></script>
<script type="text/javascript" src="https://js.stripe.com/v3/"></script>
<script src="{{ mix('js/app.js') }}"></script>
<script src="/js/sweetalert.min.js"></script>

<footer class="footer-spacing"></footer>


</body>
</html>