<?php
   session_start(); //crea o recupera sessione
   include('myfunctions.php');
   $conn = dbConnect();
   myhttps();
   
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>TS authentication method</title>
	 <meta charset="UTF-8">
	<link href="mystyle.css" rel=stylesheet type="text/css">
	<script type='text/javascript'>
window.__lo_site_id = 216823;

	(function() {
		var wa = document.createElement('script'); wa.type = 'text/javascript'; wa.async = true;
		wa.src = 'https://d10lpsik1i8c69.cloudfront.net/w.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(wa, s);
	  })();
	</script>
</head>
<body>
<header class="heading-group">
	<h2>TS AUTHENTICATION</h2>
	<p class="subtitle">	A new graphical authentication method.	</p>
</header>

	 <div class="vertical-menu">
	  <a href="index.php" class="active">Home</a>
	  <a href="login.php">LOGIN</a>
	  <a href="registrazione.php">SIGN IN</a>
	 </div> 
<div id="error">
<?php
if(isset($_REQUEST['cookieno'])){
	 echo "<h2>No cookies set.</h2>";
}

//se ci sono messaggi da stampare li stampo
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    echo "<h2>$msg</h2>";
    unset($_SESSION['msg']);
}

?>
</div>

<div id="home">
<h2> TRY IT NOW!</h2>
<p>The new authentication method that allows you to store your secret in complete safety.</p>
<p>Click on LOGIN if you are already registered. Otherwise SIGN IN!</p>
</div>

<noscript>
   <h2> Sorry: Your browser does not support or has
    disabled javascript</h2>
</noscript>
</body>
</head>         
</html>