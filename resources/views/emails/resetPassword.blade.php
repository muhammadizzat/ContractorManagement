<!DOCTYPE html>
<html>

<head>
    <title>Password Changed </title>
</head>

<body>
    <h2>Your password has been successfully changed</h2>
    <br />
    <br />
    Please click on the link and login with the temporary password to change your password.
    <a href="{{route('login')}}">Homepage</a>
    Your temporary password is <?php echo $pw; ?>

</body>

</html>