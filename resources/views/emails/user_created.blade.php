<!DOCTYPE html>
<html>
<head>
    <title>Account Details</title>
</head>
<body>
<h1>Welcome, {{ $user->name }}</h1>
<p>Your account has been created. Here are your login details:</p>

<ul>
    <li>Email: {{ $user->email }}</li>
    <li>Password: {{ $password }}</li>
</ul>

<p>Please log in and change your password as soon as possible.</p>
</body>
</html>
