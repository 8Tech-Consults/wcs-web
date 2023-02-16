<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>UWA Offenders database - 2 factor authentication</title>
</head>

<body>
    <p>Hello <b>{{ $u->name }}</b>, you have attempted to login to your UWA offenders Database Account. Please
        click on the following link or use secret code to to proceed to your account.</p>

    <p style="font-size: 30px;">LINK: <b><a
                href="{{ url('2fauth?secret=' . $u->code) }}">{{ url('2fauth?secret=' . $u->code) }}</a></b></p>
    <br>
    <h3>OR</h3>
    <br>
    <p style="font-size: 30px;">CODE: <b><code>{{ $u->code }}</code></b></p>

    <p>Thank you.</p>

</body>

</html>
