<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta Information -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', config('app.name'))</title>
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
<body>
<div id="spark-app" v-cloak>
    <!-- Navigation -->
@if (Auth::check())
@else
@endif

<!-- Main Content -->
@yield('content')

<!-- Application Level Modals -->
    @if (Auth::check())
        @include('spark::modals.notifications')
        @include('spark::modals.support')
        @include('spark::modals.session-expired')
    @endif
</div>

<!-- JavaScript -->
<script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
