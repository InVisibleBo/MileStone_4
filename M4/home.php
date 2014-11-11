<?php
    $mysqli = new mysqli("localhost","zshifour_zhongwu","307442570szw","ezcampus");
        if (mysqli_connect_errno()) {
            printf("Connect failed: %s\n", mysqli_connect_error());
            exit();
        }
    if (isset($_POST['username'])) {
    	echo "username taken!";
    	exit();
    }
    if (isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
        $email = $_COOKIE['email'];
        $password = $_COOKIE['password'];

        if ($stmt = $mysqli->prepare("SELECT activated,name FROM users WHERE email=? and password=?")) {
            $stmt->bind_param("ss",$email,$password);
            $stmt->execute();
            setcookie('username',$username,time()+24*60*60*3);
            mysqli_stmt_store_result($stmt);
            $check_count = $stmt->num_rows;
            $stmt->bind_result($activated,$username);
            while ($stmt->fetch()) {}
            mysqli_stmt_free_result($stmt);
            $stmt->close();
        } else {
            printf("prepare error");
        }
        if ($check_count == 1 && $activated == 1) {
            header('Location: welcome.php');
            exit;
        }
        else {
            setcookie('email',$email,time()-1000);
            setcookie('password',$password,time()-1000);
            setcookie('username',$username,time()-1000);
        }
    }
    if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password1']) && isset($_POST['password2']) && isset($_POST['school'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password1'];
        $school = $_POST['school'];
        $hash = md5(rand(0,1000));

        if ($stmt = $mysqli->prepare("SELECT id FROM users WHERE name=?")) {
            $stmt->bind_param("s",$username);
            $stmt->execute();
            mysqli_stmt_store_result($stmt);
            $user_count = $stmt->num_rows;
            mysqli_stmt_free_result($stmt);
            $stmt->close();
        } else {
            printf("prepare error 1");
        }

        if ($stmt = $mysqli->prepare("SELECT id FROM users where email=?")) {
            $stmt->bind_param("s",$email);
            $stmt->execute();
            mysqli_stmt_store_result($stmt);
            $email_count = $stmt->num_rows;
            mysqli_stmt_free_result($stmt);
            $stmt->close();
        } else {
            printf("prepare error 2");
        }



        
        if ($user_count < 1 && $email_count < 1) {
            if ($stmt = $mysqli->prepare("INSERT INTO users (name,email,password,school,activated,hash) VALUES (?,?,?,?,0,?)")) {
                $stmt->bind_param("sssss",$username,$email,$password,$school,$hash);
                $stmt->execute();
                setcookie('email',$email,time()+24*60*60*3);
                setcookie('password',$password,time()+24*60*60*3);
                setcookie('username',$username,time()+24*60*60*3);

                echo "You are successfully registered! Please go to $email to activate your account!";
                $to = $email;
                $subject = 'Signup | Verification';
                $message = "

                Thanks for signing up! Your account has been created.

                -------------------
                Username: $username
                Password: $password
                -------------------

                Please click this link to activate your account:
                http://www.ezcampus.org/confirmation.php?email=$email&password=$password&hash=$hash
                ";

                $header = 'From:nonreply@ezcampus.org' . "\r\n";
                mail($to,$subject,$message,$headers);
            } else {
                echo "Sorry the registration is unsuccessful.";
            }
        } else if ($user_count < 1) {
            echo "The email is already registered!";
        } else if ($email_count < 1) {
            echo "The username name is taken!";
        } else {
            echo "email and username are both taken!";
        }
    }
    mysqli_close($mysqli);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Welcome To Ezcampus!</title>
<link rel="stylesheet" type="text/css" href="css/style.css" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

</head>
<script>
    function validateForm() {
        var username = $("[name='username']").val();
        var email = $("[name='email']").val();
        var pass1 = $("[name='password1']").val();
        var pass2 = $("[name='password2']").val();
        var school = $("#school_list option:selected").text();
        
        if (username == "" || email == "" || pass1 == "" || pass2 == "") {
            alert("Please fill all the blanks");
            return false;
        } else if (school == "Choose your school...") {
            alert("Please choose your school.");
            return false;
        }

        var username_format=/^[a-zA-Z]{1}[a-zA-Z0-9]{2,14}$/;
        if (username.length < 3 || username.length > 15) {
            alert("username can only be 5-15 characters");
            return false;
        } else if (!username.match(username_format)) {
            alert("Please use the correct format for username");
            return false;
        }

        if (pass1.length < 8) {
            alert("password should be at least 8 characters");
            return false;
        } else if (pass1 != pass2) {
            alert("Passwords must match");
            return false;
        }



        var UR_email = /.+@.+\.rochester+\.edu$/;
        var NYU_email = /.+@.+\.nyu+\.edu$/;
        if (!email.match(UR_email) && !email.match(NYU_email)) {
            alert("Please enter a valid school email address.");
            return false;
        }


    }

    function checkusername() {
    	var u = $('[name=username]').val();
    	$("#usernameHint").html("Checking...");
    	$.ajax({
    		type:"POST",
    		url:"index.php",
    		data: {username:u},
    		dataType: "text"}).done(function(msg) {
    			$("#usernameHint").html(msg);
    		}).fail(function(msg) {
    			alert("ERROR");
    		});
    }

</script>

<body>
    <div id="ezcontainer">
        <div id="ezwrapper">
            <div id="ezwrappertop">
                <div id="ezwrappertopleft">
                    <h3>Sign Up</h3>
                </div>
                <div id="requirementHint">
                    <p id="ezmissing"></p>
                </div>
                <div id="ezwrappertopright">
                    <h3 style="float:right;"><a href="login.php" id="loginButton">Log In &raquo;</a></h3>
                </div>
            </div>
            <div id="ezwrappermain">
                <form name = "registration" action="index.php" method="post" onsubmit="return validateForm()">
                    <div class="ezfield">
                        <div class="ezfieldleft">
                            <h3 style="float:right;">Username:</h3> 
                        </div>
                        <div class="ezfieldright">
                            <input onblur="checkusername()" style="height:35px;width:200px;" type="text" name="username" placeholder="Type your name here.">
                            <span id="usernameHint"></span>
                        </div>
                    </div>
                    <div class="ezfield">
                        <div class="ezfieldleft">
                            <h3 style="float:right;">Email:</h3> 
                        </div>
                        <div class="ezfieldright">
                            <input style="height:35px;width:200px;" type="text" name="email" placeholder="Enter your email address.">
                            <span id="emailHint"></span>
                        </div>
                    </div>
                    <div class="ezfield">
                        <div class="ezfieldleft">
                            <h3 style="float:right;">Password:</h3> 
                        </div>
                        <div class="ezfieldright">
                            <input style="height:35px;width:200px;" type="password" name="password1">
                            <span id="password1Hint"></span>
                        </div>
                    </div>
                    <div class="ezfield">
                        <div class="ezfieldleft">
                            <h3 style="float:right;">Password again:</h3> 
                        </div>
                        <div class="ezfieldright">
                            <input style="height:35px;width:200px;" type="password" name="password2">
                            <span id="password2Hint"></span>
                        </div>
                    </div>
                    <div class="ezfield">
                        <div class="ezfieldleft">
                            <h3 style="float:right;">School:</h3> 
                        </div>
                        <div class="ezfieldright">
                            <select style="height:35px;width:200px;" id="school_list" name="school">
                                <option selected>Choose your school...</option>
                                <option value="University of Rochester">University of Rochester</option>
                                <option value="New York University">New York University</option>
                            </select>
                            <span id="schoolHint"></span>
                        </div>
                    </div>
                    <div style="width:500px;height:50px;margin-left:75px;margin-top:20px;display:inline-block;">
                        <button style="height:50px;width:100px;margin-left:180px;" type="submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>