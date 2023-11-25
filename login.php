<!DOCTYPE html>
<html>

<head>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
	<title>Login</title>
	<link rel="stylesheet" type="text/css" href="css/login.css">
</head>

<body>
	<nav class="navbar navbar-light bg-light mw-100 px-5">
		<a class="navbar-brand" href="index.php">
			<!-- <img src="" width="30" height="30" alt=""> -->
			<h1>Auctionin</h1>
		</a>

	</nav>
	<div class="header">
		<h2>Login</h2>
	</div>

	<form method="POST" action="login_proses.php">
		<div class="input-group">
			<label>Username</label>
			<input type="text" name="uname">
		</div>
		<div class="input-group">
			<label>Password</label>
			<input type="password" name="upass">
		</div>
		<button type="submit" class="btn" name="login_user">Login</button>
		<p>
			Not yet a member? <a href="register.php">Sign up</a>
		</p>
	</form>
</body>

</html>