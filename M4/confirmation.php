<?php

	$mysqli = new mysqli("localhost","zshifour_zhongwu","307442570szw","ezcampus");
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}

	if (isset($_GET['email']) && isset($_GET['password']) && isset($_GET['hash'])) {
		$email = mysql_escape_string($_GET['email']);
		$password=mysql_escape_string($_GET['password']);
		$hash = mysql_escape_string($_GET['hash']);

		if ($stmt = $mysqli->prepare("SELECT hash FROM users WHERE email=? and password=?")) {
			$stmt->bind_param("ss",$email,$password);
			$stmt->execute();
			$stmt->bind_result($d_hash);
			while ($stmt->fetch()) {}
			$stmt->close();
		} else {
			printf("prepare error");
		}
		if ($hash == $d_hash) {
			if ($stmt = $mysqli->prepare("UPDATE users SET activated = 1 WHERE email=? and password=? and hash=?")) {
				$stmt->bind_param("sss",$email,$password,$hash);
				$stmt->execute();
				echo "You are successfully activated!";
				echo "Click <a href='login.php'>here</a> to log in!";
			} else {
				echo "prepare error";
			}
		} else {
			echo "unknown error";
		}

	}
?>