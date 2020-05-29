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
<div>
<form id="R" action="actionform.php" method="post" name="f" >
  <div class="container">
    <label for="uemail"><b>E-mail&ensp;&ensp;&ensp;&thinsp;</b></label> <!--defines a label for an input element. -->
    <input type="email" placeholder="example@domain.com" name="uemail" required> <br>
   	<h2>Start the registration process!</h2>
    <button title='Click to start the registration' name="Signin">SIGN IN</button>
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