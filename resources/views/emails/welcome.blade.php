<!DOCTYPE html>
<html>

<head>
    <title>Welcome </title>
</head>

<body>
    <h2>Welcome to the LinkZZapp</h2>
    <br />
    <br />
    Please click on below link to verify your email account.
    <a href="{{route('register.verify-user', ['token' => $user->verifyUser->token])}}">Verify Email</a>
    Your temporary password is <?php echo $pw; ?>

</body>

</html>