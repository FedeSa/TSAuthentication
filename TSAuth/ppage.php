<?php
session_start();
include('myfunctions.php');
$max=4;
$conn=dbConnect();
myhttps();

if(!$_SESSION['log']){
    $_SESSION['msg']="AUTHORIZATION DENIED. Please LOGIN. <br>";
    myRedirect("index.php","FAILURE");
    exit();
}

if(isset($_SESSION['first']) && $_SESSION['first']== TRUE) {
   set_cookie($_SESSION['myuser']);
   $_SESSION['first']= FALSE;
   myRedirect("ppage.php");
} 

$ul=userLoggedIn();
if(!($uc=cookieon()) || $ul!=$uc ){ //cookie scaduto o utente non associato al cookie 
	myRedirect("index.php?cookieno=1","cookies no");
}
set_cookie($_SESSION['myuser']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>TS authentication method</title>
	 <meta charset="UTF-8">
	<link href="mystyle.css" rel=stylesheet type="text/css">
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
<body>
<header class="heading-group">
	<h2>TS AUTHENTICATION</h2>
	<p class="subtitle">	A new graphical authentication method.	</p>
</header>

<div class="inner"><h2>	&emsp;WELCOME, <?php echo $_SESSION['myuser'] ?></h2></div>
<div>
<form class="vertical-menu" action="actionform.php" method="post" name="f">
    <button type="submit" name="Prenota">Prenota viaggio</button>
    <button type="submit" name="Elimina">Elimina viaggio</button>
    <button type="submit" name="Logout">LOGOUT</button>
</form>
</div>
<?php
//se ci sono messaggi da stampare li stampo
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    echo "<h3>$msg</h3>";
    unset($_SESSION['msg']);
}else{
    echo "<h3>Il minibus può portartare al massimo $max persone</h3>";
}
?>
<?php 
try{
	mysqli_autocommit($conn,false);  
    $sql="SELECT * FROM percorsi FOR UPDATE";
    if(!($ris = mysqli_query($conn,$sql)))
        throw new Exception("QUERY FALLITA- SELECT percorsi");
        $n=mysqli_num_rows($ris);
        if($n > 0){
            $i=1;
            $sql="SELECT * FROM prenotazioni WHERE utente='".$_SESSION['myuser']."' FOR UPDATE";
            if(!($ris2= mysqli_query($conn,$sql)))
                throw new Exception("QUERY FALLITA-  prenotazioni");
            $account=mysqli_fetch_array($ris2);
                echo "<div id=\"timeline\"><h2 id=\"path\">Tragitto attuale</h2><ol class=\"timeline-list\">";
                while ($riga = mysqli_fetch_array($ris, MYSQLI_ASSOC)) {
                    echo "<li><div class=\"content\"><div class=\"inner\"><span class=\"stop\"><span class=\"num\">{$i}<sup>st</sup></span><span class=\"halt\">STOP</span></span>";
                    //ROSSO
                    if(($account['partenze']==$riga['partenze']) && ($account['arrivi']==$riga['arrivi'])){
                        echo "<h2><font color=\"red\">{$riga['partenze']}</font> → <font color=\"red\">{$riga['arrivi']}</font></h2><p>{$riga['totale']} passeggeri";
                    }elseif($account['partenze']==$riga['partenze'] && $account['arrivi']!==$riga['arrivi']){
                        echo "<h2><font color=\"red\">{$riga['partenze']}</font> → {$riga['arrivi']}</h2><p>{$riga['totale']} passeggeri";
                    }elseif($account['partenze']!==$riga['partenze'] && $account['arrivi']==$riga['arrivi']){
                        echo "<h2>{$riga['partenze']} → <font color=\"red\">{$riga['arrivi']}</font></h2><p>{$riga['totale']} passeggeri";
                    }else{
                        echo "<h2>{$riga['partenze']} → {$riga['arrivi']}</h2><p>{$riga['totale']} passeggeri";
                    }
                
                    $sql="SELECT * FROM prenotazioni WHERE partenze<='".$riga['partenze']."' AND arrivi>='".$riga['arrivi']."' FOR UPDATE";
                    if(!($ris1= mysqli_query($conn,$sql)))
                        throw new Exception("QUERY FALLITA-  prenotazioni");
                    while($users = mysqli_fetch_array($ris1)){
                        echo  "<br>    &emsp;{$users['utente']} ({$users['passeggeri']})";   
                    }
                    echo "</p></div></div></li>";
                    $i++;
                }
                echo "</ol></div>";
        }else{ echo "<div id=\"timeline\"><h2 id=\"path\">Nessun percorso ancora inserito.</h2><ol class=\"timeline-list\">";}
        
		if (!mysqli_commit($conn)) {// per avere il corretto messaggio di errore
                throw Exception("Commit fallita");
        }
	
}catch(Exception $e){
    mysqli_rollback($conn);
    $_SESSION['msg'] =$e->getMessage();
    myRedirect("login.php","prenota");
}
?>

<noscript>
   <h2> Sorry: Your browser does not support or has
    disabled javascript</h2>
</noscript>
</body>
</head>         
</html>