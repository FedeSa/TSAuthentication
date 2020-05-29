<?php
    session_start(); //crea o recupera sessione
    include('myfunctions.php');
    $conn = dbConnect();
    myhttps();
    
    if (!isset($_SESSION['logimages'])) { //contiene tutte le immagini per il login
    	$_SESSION['logimages']=array();
    }else{
    	$_SESSION['msg']="Something went wrong. <br>Please, retry.";
    	unset($_SESSION['logimages']);
    	unset($_SESSION['already']);
    	unset($_SESSION['image']);
    	unset($_SESSION['err']);
    	myRedirect("index.php");
    }
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>TS authentication method</title>
	<meta charset="UTF-8">
	<link href="mystyle1.css" rel=stylesheet type="text/css">
	<script type='text/javascript'>
window.__lo_site_id = 216823;

	(function() {
		var wa = document.createElement('script'); wa.type = 'text/javascript'; wa.async = true;
		wa.src = 'https://d10lpsik1i8c69.cloudfront.net/w.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(wa, s);
	  })();
	</script>
</head>
<body onload="javascript:parent.document.f.reset();">
<header class="heading-group">
	<h2>TS AUTHENTICATION</h2>
	<p class="subtitle">	A new graphical authentication method.	</p>
</header>
<div id="error">
<?php
//se ci sono messaggi da stampare li stampo
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    echo "<h2>$msg</h2>";
    unset($_SESSION['msg']);
}
?>
</div>
<div>
<form name= "f" action="actionform.php" method="post">
  <div class="container">
    <label for="uname"><b>Username</b></label>
    <input type="email" placeholder="example@domain.com" name="uname" required> <br>
    <h2>Start the login process!</h2>
    <button type="submit" title='Click to start the authentication' name="Login">LOGIN</button>
  </div>
  <div class="container" >
    <button type="reset" class="cancelbtn">Cancel</button>
    <button class="previous" onclick="window.location.href='index.php'">Back</button>
  </div>
</form>
</div>

<noscript>
   <h2> Sorry: Your browser does not support or has
    disabled javascript</h2>
</noscript>
</body>
</head>         
</html>