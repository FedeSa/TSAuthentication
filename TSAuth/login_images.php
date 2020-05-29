<?php
	session_start(); //crea o recupera sessione
	include('myfunctions.php');
	$conn = dbConnect();
	myhttps();

	if (!isset($_SESSION['logimages'])) { //contiene tutte le immagini per il login
		$_SESSION['msg']="YOU FOLK. RETRY!";
		myRedirect("index.php");
	}
	if (!isset($_SESSION['already'])) { //contiene tutte le immagini già displayed
		$_SESSION['already']=array();
	}
	if (!isset($_SESSION['err'])) { //contiene tutte le direzioni segnate dall'utente
		$_SESSION['err']=0;
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>TS authentication method</title>
	 <meta charset="UTF-8">
	<link href="mystyle2.css" rel=stylesheet type="text/css">
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

<div class="container">
	<?php 	
	echo "CURRENT ".count($_SESSION['logimages'])."<br>";
		$ima= myDisplayImage();
		echo '<br><img src="'.$ima.'" /><br/>';	
		$img = explode("/", $ima);
		$im = explode(".",$img[1]);
		$_SESSION['image']=$im[0];
		echo "By ".myAuthor($_SESSION['image'])."<br>";
	?>
</div>
<div>
<form id="R" action="actionform.php" method="post" name="f" >
<div class="container">
    <button type="submit" name="LoginAuth" value="left">LEFT</button>
    <button type="submit" name="LoginAuth" value="up">UP</button>
    <button type="submit" name="LoginAuth" value="right">RIGHT</button>
    <button type="submit" name="LoginAuth" value="down">DOWN</button>
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