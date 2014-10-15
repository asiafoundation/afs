<!DOCTYPE html>
<html id="newhome" lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <!-- Site Properities -->
    <title>{{ Setting::meta_data('general', 'name')->value }} - {{ Setting::meta_data('general', 'tag_line')->value }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Loading Bootstrap -->
    <link rel="stylesheet" type="text/css" href="{{ Theme::asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ Theme::asset('css/style.css') }}">

    <script src="{{ Theme::asset('javascript/jquery-1.7.2.min.js') }}"></script>
    <script src="{{ Theme::asset('javascript/modernizr-2.6.2.min.js') }}"></script>
  </head>

  <body>
  @yield('content')

  <footer>
    <div class="container center">
      <div class="col-md-12">
        <a href="#"><img src="{{ Theme::asset('img/logo-footer.png') }}"></a>
        <p>Survey Q Copyright 2014. All rights reserved.</p>
      </div>
    </div>
  </footer>
</body>
</html>