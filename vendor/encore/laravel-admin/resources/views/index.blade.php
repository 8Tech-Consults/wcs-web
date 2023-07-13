<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="renderer" content="webkit">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ Admin::title() }} @if ($header)
            | {{ $header }}
        @endif
    </title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="UWA - Wildlife offenders database">
    <meta name="keywords" content="UWA - Wildlife offenders database">
    <meta name="author" content="8Technologies">

    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- Favicon and Touch Icons -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ url('') }}/assets/logo.png">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ url('') }}/assets/logo.png">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ url('') }}/assets/logo.png">
    <link rel="manifest" href="{{ url('') }}/assets/public/favicon/site.webmanifest">
    <link rel="mask-icon" href="{{ url('') }}/assets/logo.png" color="#6366f1">
    <link rel="shortcut icon" href="{{ url('') }}/assets/logo.png">
    <meta name="msapplication-TileColor" content="#080032">
    <meta name="msapplication-config" content="{{ url('') }}/assets/public/favicon/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">

    {!! Admin::css() !!}

    @include('widgets.css')

    <script src="{{ Admin::jQuery() }}"></script>
    {!! Admin::headerJs() !!}
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->



</head>

<body class="hold-transition {{ config('admin.skin') }} {{ join(' ', config('admin.layout')) }}">

    @if ($alert = config('admin.top_alert'))
        <div style="text-align: center;padding: 5px;font-size: 12px;background-color: #ffffd5;color: #ff0000;">
            {!! $alert !!}
        </div>
    @endif

    <div class="wrapper">

        @include('admin::partials.header')

        @include('admin::partials.sidebar')

        <div class="content-wrapper" id="pjax-container">
            {!! Admin::style() !!}
            <div id="app">
                @yield('content')
            </div>
            {!! Admin::script() !!}
            {!! Admin::html() !!}
        </div>

        @include('admin::partials.footer')

    </div>

    <button id="totop" title="Go to top" style="display: none;"><i class="fa fa-chevron-up"></i></button>

    <script>
        function LA() {}
        LA.token = "{{ csrf_token() }}";
        LA.user = @json($_user_);
    </script>

    <!-- REQUIRED JS SCRIPTS -->
    {!! Admin::js() !!}

</body>

</html>
