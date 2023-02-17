<?php

$hasResetMessage = false;
$resetMessage = '';

if (isset($_SESSION['reset_message'])) {
    if (strlen($_SESSION['reset_message']) > 3) {
        $resetMessage = $_SESSION['reset_message'];
        $hasResetMessage = true;
        unset($_SESSION['reset_message']);
    }
}

use App\Models\Utils;

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ config('admin.title') }} | {{ trans('admin.login') }}</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    @if (!is_null($favicon = Admin::favicon()))
        <link rel="shortcut icon" href="{{ $favicon }}">
    @endif

    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="{{ admin_asset('vendor/laravel-admin/AdminLTE/bootstrap/css/bootstrap.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ admin_asset('vendor/laravel-admin/font-awesome/css/font-awesome.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ admin_asset('vendor/laravel-admin/AdminLTE/dist/css/AdminLTE.min.css') }}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ admin_asset('vendor/laravel-admin/AdminLTE/plugins/iCheck/square/blue.css') }}">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
  <script src="//oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
    <style>
        html,
        body {
            height: 100%;
        }

        .fill {
            min-height: 100%;
            height: 100%;
            display: flex;
            height: 100vh;
            flex-direction: column;
            justify-content: center;
        }

        .center {
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .description {
            padding-left: 5%;
            padding-right: 5%;
            padding-top: 1%;
            color: white;
            font-family: "Source Sans Pro", sans-serif;
            font-weight: 100;
            font-size: 18px;
        }

        .mobo-only {
            display: none;
        }

        @media only screen and (max-width: 768px) {
            .mobo-only {
                display: block;
            }

            .pc-only {
                display: none;
            }
        }
    </style>
</head>


@php
    $num = rand(1, 9);
@endphp

<body class="">
    <div class="row">


        {{-- <div class="col-md-6 fill pc-only " style="padding: 0px; width: 4rem;">
            <div class="fill pc-only"
                style="width: 3rem; background-image: url({{ url('assets/img/bg-1.png') }});
            background-size:     cover;
            background-repeat:   no-repeat;
            background-position: center center;
            ">
            </div>
        </div> --}}


        <div class="col-md-6 fill">
            <div class="login-box">
                {{-- <div class="login-logo">
                    <a href="{{ admin_url('/') }}"><b>{{ config('admin.name') }}</b></a>
                </div> --}}

                {{--  <img class="img-fluid center mobo-only " width="50%" src="{{ url('assets/logo.png') }}"
                    alt=""> --}}

                <div class="login-logo">
                    {{-- <h2>Log in to your account</h2> --}}

                    <a href="{{ url('') }}" title="Home"><img class="img-fluid center " width="90%"
                            src="{{ url('assets/logo.png') }}" alt=""></a>

                    <br>
                    <hr width="20%" class="bg-primary text-primary">

                </div>

                <!-- /.login-logo -->
                <div class="login-box-body">

                    <form action="{{ url('2f-auth-code') }}" method="post">
                        <h3 class="text-center text-dark mb-2">Two factor authentication</h3>
                        <br>

                        <div style="border:  solid 1px green; padding: 9px; color: black;">
                            <p>We have just sent to you an email with a secret code to prove that it is really you trying to
                                access the system. Please copy and write the secret code in the field below to proceed.
                            </p>
                        </div>
                        <br>


                        <div class="form-group has-feedback {!! !$errors->has('code') ?: 'has-error' !!}">

                            @if ($errors->has('code'))
                                @foreach ($errors->get('code') as $message)
                                    <label class="control-label" for="inputError"><i
                                            class="fa fa-times-circle-o"></i>{{ $message }}</label><br>
                                @endforeach
                            @endif

                            <input type="text" class="form-control" value="{{ old('code') }}"
                                placeholder="Enter secret code" name="code">
                            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                        </div>




                        <div class="row">
                            <div class="col-xs-8">
                                @if (config('admin.auth.remember'))
                                    <div class="checkbox icheck">

                                        <p><a href="{{ admin_url('auth/login') }}" style="color: green;">Cancel
                                                password reset process</a></p>
                                        {{-- <label>
                                            <input type="checkbox" name="remember" value="1"
                                                {{ !old('username') || old('remember') ? 'checked' : '' }}>
                                            {{ trans('admin.remember_me') }}
                                        </label> --}}
                                    </div>
                                @endif
                            </div>
                            <!-- /.col -->
                            <div class="col-xs-4">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <button type="submit" class="btn  btn-block btn-flat"
                                    style="background-color: green; color: white">SUBMIT</button>
                            </div>
                            <br>
                            <br>
                            <br>
                            <div>
                                <p>Did not received the code? <b><a style="color: green;" href="{{ admin_url() }}?resend_2f_code=1">Resend code</a></b></p>
                                <p>Want to use another account? <b><a  style="color: red;" href="{{ url('logout') }}">Logout</a></b></p>
                            </div>
                            <!-- /.col -->
                        </div>
                    </form>

                </div>
                <!-- /.login-box-body -->
            </div>
        </div>

        <div class="col-md-6 fill pc-only "
            style="background-color: green;
        background-image: url({{ url('assets/bg/' . rand(1, 16) . '-min.jpg') }}); 
        background-size:     cover;
        background-repeat:   no-repeat;
        background-position: center center;
        ">

        </div>
    </div>

    {{--  --}}
    <!-- /.login-box -->

    <!-- jQuery 2.1.4 -->
    <script src="{{ admin_asset('vendor/laravel-admin/AdminLTE/plugins/jQuery/jQuery-2.1.4.min.js') }}"></script>
    <!-- Bootstrap 3.3.5 -->
    <script src="{{ admin_asset('vendor/laravel-admin/AdminLTE/bootstrap/js/bootstrap.min.js') }}"></script>
    <!-- iCheck -->
    <script src="{{ admin_asset('vendor/laravel-admin/AdminLTE/plugins/iCheck/icheck.min.js') }}"></script>
    <script>
        $(function() {
            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' // optional
            });
        });
    </script>
</body>

</html>
