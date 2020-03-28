<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta Information -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1"> -->
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, height=device-height" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Jemmson</title>

    <!-- Fonts -->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600' rel='stylesheet' type='text/css'>
    <script defer src="https://use.fontawesome.com/releases/v5.0.8/js/all.js"></script>


    <!-- CSS -->
    <link href="/css/sweetalert.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
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
    <main-header :user="user"></main-header>
<!-- Main Content -->
<transition name="fade">
    <!-- <router-view :user='user'></router-view> -->
    <v-app style="height: 5000px; margin-top: 75px" >

        <router-view :user='user'></router-view>
    </v-app>
</transition>
<div style="height: 56px;"></div>
<main-footer :user="user"></main-footer>

<!-- Application Level Modals -->
{{-- 
    @if (Auth::check())
    @include('spark::modals.notifications')x
    @include('spark::modals.support')
    @include('spark::modals.session-expired')

        @endif
        --}}
</div>

<!-- JavaScript -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBAQZB-zS1HVbyNe2JEk1IgNVl0Pm2xsno&libraries=places"></script>
{{--<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC2uX7JbkzcTpZ7Nb-_nu0CagS4lBSNdDw&libraries=places"></script>--}}
<script type="text/javascript" src="https://js.stripe.com/v3/"></script>
<script src="{{ mix('js/app.js') }}"></script>
<script src="/js/sweetalert.min.js"></script>

{{--<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC2uX7JbkzcTpZ7Nb-_nu0CagS4lBSNdDw&libraries=places"></script>--}}
{{--<script type="text/javascript" src="https://js.stripe.com/v3/"></script>--}}
{{--<script src="{{ mix('js/app.js') }}"></script>--}}
{{--<script src="/js/sweetalert.min.js"></script>--}}




<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-117973760-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-117973760-1');
</script>
<footer class="footer-spacing"></footer>
</body>
</html>
