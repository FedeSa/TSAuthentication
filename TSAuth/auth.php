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
<div>
  <div class="container">
   	<h2>Ready to start the authentication process!</h2>
   	<p>Find the arrow.</p>
    <button type="submit" title='Click to start the training session' onclick="window.location.href='images.php'">START!</button>
  </div>
  <div class="container" >
    <button type="reset" class="cancelbtn">Cancel</button>
    <button class="previous" onclick="window.location.href='index.php'">Back</button>
  </div>
</div>
		
<noscript>
   <h2> Sorry: Your browser does not support or has
    disabled javascript</h2>
</noscript>
</body>
</head>         
</html>