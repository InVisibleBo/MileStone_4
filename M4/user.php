<?php

	if (isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
		$email = $_COOKIE['email'];
        $password = $_COOKIE['password'];
        $mysqli = new mysqli("localhost","zshifour_zhongwu","307442570szw","ezcampus");
	    if (mysqli_connect_errno()) {
	        printf("Connect failed: %s\n", mysqli_connect_error());
	        exit();
	    }
        if ($stmt = $mysqli->prepare("SELECT activated,name,school FROM users WHERE email=? and password=?")) {
            $stmt->bind_param("ss",$email,$password);
            $stmt->execute();
            mysqli_stmt_store_result($stmt);
            $check_count = $stmt->num_rows;
            $stmt->bind_result($activated,$username,$school);
            while ($stmt->fetch()) {} // $activated,$username,$school are only valid after this
             setcookie('username',$username,time()+24*60*60*3);
            mysqli_stmt_free_result($stmt);
            $stmt->close();

        } else {
            printf("prepare error");
        }
        if ($check_count == 0) {
            header("login.php");
            exit();
        } else if ($activated == 0) {
        	echo "Please go to your email to activate the account!<br />";
        	echo "Click <a href='process.php'>here</a> to go back to log in page.";
        	exit();
        } else {
        	if (!isset($_GET['school']) || !isset($_GET['name'])) {
        		$append_url = "?name=".$username."&school=".$school;
            	header('Location: user.php'.$append_url);
            	exit();
        	}
        }

	} else {
		header("Location: login.php");
	}

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Welcome To Ezcampus!</title>
<link rel="stylesheet" type="text/css" href="css/user.css" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

</head>

<script>
	$(document).ready(function(){
	  $(".ezCategory").click(function(){
	    if($(this).hasClass("chosen")) {
	    	$(".unchosen").slideToggle();
	    }
	    if ($(this).hasClass("unchosen")) {
	  		$(".chosen").before($(this));
		  	$(".chosen").removeClass("chosen").addClass("unchosen");
		  	$(this).removeClass("unchosen").addClass("chosen");
		  	$(".unchosen").css("display","none");
	  	}

	  });
	 	
	});
</script>

<body>
	<div id="ezSellForm"></div>
	<div stlye="opacity:0.4;" id="ezPageTop1">
		<div id="ezPageTop1Container">
			<div id="ezPageTop1Logo"><img src="img/EZ.png" height="70" width="100" /></div>
			<div id="ezPageTop1Right">
				<ul id="ezPageTop1UL">
					<li class="ezPageTop1List" id="ezPageTop1Notification">
						<img src="lib/glyphicons/png/glyphicons_127_message_flag.png" />
					</li>
					<li class="ezPageTop1List" id="ezPageTop1Cart">
						<img src="lib/glyphicons/png/glyphicons_202_shopping_cart.png" />
					</li>
					<li class="ezPageTop1List" id="ezPageTop1Name">
						<?php echo $_COOKIE['username'] ?>
						
					</li>
					<li class="ezPageTop1List" id="ezPageTop1Logout">
						<button onclick="window.location='process.php';">Log out</button>
					</li>

				</ul>
			</div>
		</div>
	</div>
	<div id="ezPageTop2">
		<div id="ezPageTop2Container">
			<div id="ezPageTop2NavBar">
				<ul id="ezPageTop2UL">
					<li class="ezPageTop2List" id="ezPageTop2Trade">
						Trade
					</li>
					<li class="ezPageTop2List" id="ezPageTop2Courses">
						Courses
					</li>
					<li class="ezPageTop2List" id="ezPageTop2Events">
						Events
					</li>
				</ul>
			</div>
		</div>
	</div>
	<div id="ezPageMain">
		<div id="ezPageMainSearchSell">
			<div id="ezPageMainCategoryButton">
				<ul>
					<li id="textbook" class="ezCategory chosen"><button>textbooks</button></li>
					<li id="furniture" class="ezCategory unchosen"><button>furnitures</button></li>
					<li id="electronics" class="ezCategory unchosen"><button>Electronics</button></li>
				</ul>
			</div>
			<div id="ezPageMainSearchBar">
				<div id="ezPageMainSearchInput">
					<textarea placeholder="anything you want"></textarea>
				</div>
				<div id="ezPageMainSearchButton">
					<button>Search</button>
				</div>
			</div>
			<div id="ezPageMainSellButton">
				<button onclick="openSellForm()">Sell</button>
			</div>
		</div>
		<div id="ezPageMainContainer">
			<div id="ezPageMainLeftBar">
				<ul>
					<li>My Trade</li>
					<li>My Course</li>
					<li>My Events</li>
					<li></li>
				</ul>
			</div>
			<div id="ezPageMainMiddleContainer">
				<div class="ezPageMainMiddlePic">
					
				</div>
				<div class="ezPageMainMiddlePic">
					
				</div>
				<div class="ezPageMainMiddlePic">
					
				</div>
				<div class="ezPageMainMiddlePic">
					
				</div>
			</div>
			<div id="ezPageMainRightBar">
				<h3>Recently visited</h1>
			</div>
		</div>
	</div>
	<div id="ezPageButtom">
		
	</div>
</body>
</html>