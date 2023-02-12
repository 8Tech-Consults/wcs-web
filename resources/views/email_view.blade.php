<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CEHURD APP - Password reset</title>
</head>

<body>
    <p>Hello <b>{{ $u->name }}</b>, you have requested for a password reset for your UWA offenders Database Account. Please use
        the following secret code to reset your password.</p>

    <p style="font-size: 30px;">CODE: <b><code>{{ $u->code }}</code></b></p>

    <p>Thank you.</p>

</body>

</html>
