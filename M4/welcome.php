<?php
	function set_user_cookie() {
		$mysqli = new mysqli("localhost","zshifour_zhongwu","307442570szw","ezcampus");
		if (mysqli_connect_errno()) {
			printf("Connect failed: %s\n", mysqli_connect_error());
			exit();
		}
		if (isset($_COOKIE['email']) && isset($_COOKIE['password'])) {

			if ($stmt = $mysqli->prepare("SELECT name FROM users WHERE email=? and password=?")) {
				$stmt->bind_param("ss",$_COOKIE['email'],$_COOKIE['password']);
				$stmt->execute();
				mysqli_stmt_store_result($stmt);
				$check_count = $stmt->num_rows;
				$stmt->bind_result($username);
				while ($stmt->fetch()) {}
				mysqli_stmt_free_result($stmt);
				$stmt->close();
			} else {
				printf("prepare error");
			}
			if ($check_count == 1) {
				setcookie('username',$username,time()+24*60*60*3);
				printf($username);
			} else {
				header("Location: login.php");
			}
		}
		else {
			header("Location: login.php");
		}
		mysqli_close($mysqli);
		return null;
	}
?>

<html>
	<head>
		<title>Welcome to ezcampus!</title>
	</head>
	<body>
		Welcome, <?php echo set_user_cookie(); ?> !

		<form action="process.php" method="post">
			<button type="submit">Log out</button>
		</form>
	</body>
</html>
