<?php
session_start(); //crea o recupera sessione
include('myfunctions.php');
$conn = dbConnect();
myhttps();

if (!isset($_SESSION['rimages'])) {
	$_SESSION['rimages']=array();
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
		$dirname = "Images/";
		$images = glob($dirname . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
		$randomimage = $images[array_rand($images)]; 
		echo '<img src="'.$randomimage.'" /><br/>';
		$img = explode("/", $randomimage);
		$im = explode(".",$img[1]);
		$image=$im[0];
		echo "By ".myAuthor($image)."<br>";
		
		$_SESSION['rimages'][]= $randomimage;
	
	?>
</div>
<div>
<form id="R" action="actionform.php" method="post" name="f" >
<div class="container">
    <button type="submit" name="Auth">LEFT</button>
    <button type="submit" name="Auth">UP</button>
    <button type="submit" name="Auth">RIGHT</button>
    <button type="submit" name="Auth">DOWN</button>
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