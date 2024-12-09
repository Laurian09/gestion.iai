<!DOCTYPE html>
<html>
<head>
	<title>HOME System de gestion de L'IAI</title>
	<link rel="stylesheet" type="text/css" href="stylelogin.css">
</head>
<body>
	<header>
		<nav>
			<h1>System de gestion de IAI</h1>
			<ul id="navli">
				<li><a class="homeblack" href="index.html">HOME</a></li>
				<li><a class="homeblack" href="connperso.php">perso_log</a></li>
				<li><a class="homered" href="connadmin.php">Chef_Log</a></li>	
			</ul>
		</nav>
	</header>
	<div class="divider"></div>

	<div class="loginbox">
    <img src="admin.png" class="avatar">
        <h1>Login Here</h1>
        <form action="list.php" method="POST">
            <p>Email</p>
            <input type="text" name="mailuid" placeholder="Enter Email Address" required pattern="^[A-Za-z]+@{1}[A-Za-z]+\.{1}[A-Za-z]{2,}$">
            <p>Password</p>
            <input type="password" name="pwd" placeholder="Enter Password" required="required" min="9">
            <input type="submit" name="login-submit" value="Login">
			<!-- <p>Raison</p> 
            <input type="text" name="raison" placeholder="Enter"> -->
            


        </form>
        
    </div>
</body>
</html>