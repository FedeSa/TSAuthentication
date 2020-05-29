<?php
    session_start();
    include('myfunctions.php');
    $conn = dbConnect();
    myhttps();
    $max=4 ;//capienza minibus

	if(!$_SESSION['log']){
    $_SESSION['msg']="AUTORIZZAZIONE NEGATA. Fai il LOGIN.<BR>";
    myRedirect("index.php","FAILURE");
    exit();
}

	if(isset($_SESSION['first']) && $_SESSION['first']== TRUE) {
	   set_cookie($_SESSION['myuser']);
	   $_SESSION['first']= FALSE;
	} 
	$ul=userLoggedIn();
	if(!($uc=cookieon()) || $ul!=$uc ){ //cookie scaduto o non utente corretto
		$_SESSION['msg']="Cookie non settati oppure la sessione &egrave; scaduta (RILOGGATI).";
		myRedirect("index.php","cookies no");
	}
	set_cookie($_SESSION['myuser']);
	
	
    //se ha già fatto una prenotazione eliminala e ne potrai fare un'altra
   $query="SELECT * FROM prenotazioni WHERE utente='".$_SESSION['myuser']."' FOR UPDATE ";
   if(!$ris=mysqli_query($conn, $query)){
       $_SESSION['msg']="QUERY FALLITA - prenotazioni";
        myRedirect("prenotazione.php","FAILURE");
    }
    if(($t=mysqli_num_rows($ris))>0){
        $_SESSION['msg']="Hai gi&agrave; fatto una prenotazine, ELIMINALA e riprova.";
        myRedirect("ppage.php","FAILURE");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Prenotazioni minibus condiviso</title>
	 <meta charset="UTF-8">
	<link href="mystyle.css" rel=stylesheet type="text/css">
	<link href="mystyle1.css" rel=stylesheet type="text/css">
</head>
<body onload="javascript:parent.document.f.reset();">
<header class="heading-group">
	<h2>NOLEGGIO MINIBUS</h2>
	<p class="subtitle">	Per condividere il tuo viaggio.	</p>
</header>
<?php

//se ci sono messaggi da stampare li stampo
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
   // $msg = my_sanitize_string($msg);  leave only alphanumeric characters to avoid unwanted html and javascript code execution
    echo "<h3>$msg</h3>";
    unset($_SESSION['msg']);
}
?>
<div>
<form action="actionform.php" method="post" name="f">
  <div class="container">
  	<label for="passengers"><b>#Persone</b></label>
    <input type="number" placeholder="Enter #passengers" name="passengers" required min="1" max=$max> <br> 
  	<label for="from"><b>Partenza</b></label>
    <select name="from">
    <option value="" selected disabled>-- Scegli --</option>
      <?php 
      $sql="SELECT partenze FROM percorsi ORDER BY partenze FOR UPDATE";
      if ($ris = mysqli_query($conn,$sql)){
          $rowcount=mysqli_num_rows($ris);
          if($rowcount>0){
              while ($riga = mysqli_fetch_array($ris, MYSQLI_ASSOC)) {
                  echo "<option>{$riga['partenze']}</option>";
              }
          } else{
              echo "<option>Altro..</option>";
          }
       mysqli_free_result($ris);
      }else {
          echo 'DB connection problems.';
          myRedirect("ppage.php","DB is not responding");
      }
      ?>
     </select>
     <br>
	 <label for="to"><b>Arrivo&ensp;&ensp;&thinsp;</b></label>
     <select name="to">
     <option value="" selected disabled>-- Scegli --</option>
      <?php 
          $sql="SELECT arrivi FROM percorsi ORDER BY partenze FOR UPDATE";
          if ($ris = mysqli_query($conn,$sql)){
              $rowcount=mysqli_num_rows($ris);
              if($rowcount>0){
                  while ($riga = mysqli_fetch_array($ris, MYSQLI_ASSOC)) {
                      echo "<option>{$riga['arrivi']}</option>";
                  }
              } else{echo "<option>Altro..</option>";}
           mysqli_free_result($ris);
          }else {
              echo 'Problemi di collegamento col DB.';
              myRedirect("ppage.php","DB non risponde");
              }
       ?>
    </select>
    <br><br>
    Altrimenti inserisci qui le tue informazioni:<br>
    <label for="da"><b>Partenza</b></label> <!--defines a label for an input element. -->
    <input title="Fai ATTENZIONE a non inserire caratteri speciali o spazi" pattern="/\w/"type="text" placeholder="Enter Start" name="da"> <br>
    <label for="a"><b>Arrivo&ensp;&ensp;&thinsp;</b></label>
    <input type="text" placeholder="Enter Destination" name="a"> <br>
    
   <h3>ATTENZIONE! Qui sopra scrivi solo se hai nuove tratte da inserire!</h3>
    <button type="submit" name="Fine">Fine</button><br>
    <button type="reset" class="cancelbtn">Cancel</button>
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