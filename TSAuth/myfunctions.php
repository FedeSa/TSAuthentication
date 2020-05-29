<?php

function userLoggedIn() {
    if (isset($_SESSION['myuser'])) {
        return ($_SESSION['myuser']);
    } else { return false; }
}

function myRedirect($page="",$msg="") {
		
	  echo '<META HTTP-EQUIV="refresh" content="0;URL='.$page.'">';
    exit; // Necessario per evitare ulteriore processamento della pagina
}

function myDestroySession() {
    $_SESSION=array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time()-3600*24, $params["path"],$params["domain"], $params["secure"], $params["httponly"]);
    }
    session_unset(); // remove all session variables
    session_destroy(); // destroy session
	myRedirect("index.php","LOGGEDOUT");
}

function dbConnect() {
    $conn = mysqli_connect("localhost","root","","tsauth");
    if(mysqli_connect_error()){ 
        $_SESSION['msg']="DB connection error. <br>";
        myRedirect("index.php","DB connection error."); 
    }
    return $conn;
}

function myhttps(){
	  if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'){
        //Richiesta è stata fatta già su HTTPS
	  }else{
        // Redirect su HTTPS ed eventuale distruzione sessione e cookie relativo
        $redirect = 'https://' . $_SERVER['HTTP_HOST'] .$_SERVER['REQUEST_URI'];
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $redirect);
        exit();
    }
}

function cookieon(){
	if(!isset($_COOKIE['s252956utente'])){
		myDestroySession();
	}else{
		return htmlspecialchars($_COOKIE['s252956utente']);
	}
}
function set_cookie($utente){
		setcookie("s252956utente", $utente, time()+600, "/");
}

function myLOGOUT(){
	myDestroySession();
}

function myEmail_format($email){
	//CONTROLLO EMAIL
	$correct= false;
	$email = trim($email);//elimino spazi, "a capo" e altro alle estremità della stringa
	// controllo che ci sia una sola @ nella stringa
	if((count(explode( '@', $email )) - 1) == 1) {
		// la stringa rispetta il formato classico di una mail?
		if(preg_match( '/^[\w\.\-]+@\w+[\w\.\-]*?\.\w{1,4}$/', $email)) {
			$correct=true;
		}
		// controllo la presenza di ulteriori caratteri "pericolosi":
		if(strpos($email,';') || strpos($email,',') || strpos($email,' ') || strpos($email,'#') || strpos($email,'!') || strpos($email,'?')) {
			$correct=false;
		}
	}
	return $correct;
}
function myEmail_registered($email){ 
	$conn = dbConnect();
	$query = "SELECT * FROM utenti WHERE utente='$email' FOR UPDATE";
	if(! $risposta = mysqli_query($conn,$query)){
		$_SESSION['msg']="DB connection error - query not executed. <br>";
		myRedirect("index.php","Errore di collegamento al DB");
	}
	if (mysqli_num_rows($risposta)!= 0){ //l'utente è già registrato
		return true;
	}
	return false;
}

function myEncrypt($str){
	$ciphering = "AES-128-CTR";
	$iv_length = openssl_cipher_iv_length($ciphering);
	$options = 0;
	$encryption_iv = random_bytes($iv_length);
	$encryption_key = openssl_digest(php_uname(), 'MD5', TRUE);
	$encryption = openssl_encrypt($str, $ciphering, $encryption_key, $options, $encryption_iv); 

	if($encryption !=FALSE){
		return $encryption;
	}
	return false;
}

function register($email, $secr){ //registra user nel DB 
    $conn = dbConnect();
//    $toDB = base64_encode($secr);
    $sql = "INSERT INTO utenti(utente, password) VALUES('".$email."','".$secr."')";
    $users = mysqli_query($conn,$sql);
    if($users) {
        $_SESSION['msg']= "Registration completed. Welcome to your personal page.";
        $_SESSION['myuser']= $email;
        $_SESSION['log'] = TRUE;
		$_SESSION['first']=TRUE;
		unset($_SESSION['rimages']);
		unset($_SESSION['remail']);
        myRedirect("ppage.php","Registration ok");
    }else{
        $_SESSION['msg']= "Registration failed.<br> PLEASE RETRY to SIGN IN.<br>";
        unset($_SESSION['rimages']);
        unset($_SESSION['remail']);
        myRedirect("index.php","FAILURE");
    }
    mysqli_free_result($users);
    mysqli_close($conn);
}

function myAuthor($im){
	$conn = dbConnect();
	$sql="SELECT * FROM images WHERE image='$im'";
	if (!$ris = mysqli_query($conn,$sql)){
		myRedirect("index.php","QUERY FALLITA - myDirection");
	}
	
	if(($t=mysqli_num_rows($ris))>0){
		$riga = mysqli_fetch_array($ris);
	}else{
		$_SESSION['msg']="No author find.";
		mysqli_free_result($ris);
		mysqli_close($conn);
		myRedirect("index.php");
	}
	return $riga['author'];
	mysqli_free_result($ris);
	mysqli_close($conn);
	
}
function myDirection($im){
	$conn = dbConnect();
	$sql="SELECT * FROM images WHERE image='$im' FOR UPDATE";
	if (!$ris = mysqli_query($conn,$sql)){
		myRedirect("index.php","QUERY FALLITA - myDirection");
	}
	
	if(($t=mysqli_num_rows($ris))>0){
		$riga = mysqli_fetch_array($ris);
	}else{
		$_SESSION['msg']="STI GRANDISSIMI CAZZI AMARI ";
		mysqli_free_result($ris);
		mysqli_close($conn);
		myRedirect("index.php");
	}
	mysqli_free_result($ris);
	mysqli_close($conn);
	return $riga['arrow'];
}


function login($email, $err) {
	$EMAX= 3;
    $conn = dbConnect();
    $sql = "SELECT * FROM utenti WHERE utente='$email' FOR UPDATE";
    if(! $risposta = mysqli_query($conn,$sql)){ 
    	mysqli_free_result($risposta);
    	mysqli_close($conn);
        $_SESSION['msg']="QUERY NON ESEGUITA. ERRORE di collegamento al DB.<br>";
        myRedirect("index.php","Errore di collegamento al DB"); 
    }
    if (mysqli_num_rows($risposta)== 1 && $err < $EMAX) {
    		mysqli_free_result($risposta);
	        mysqli_close($conn);
	        $_SESSION['msg']="ERRORI COMMESSI: ".$err;
	        $_SESSION['myuser']= $email;
	        $_SESSION['log'] = TRUE;
			$_SESSION['first']=TRUE;
	        myRedirect("ppage.php");
	        exit(); 
    }else{
        $_SESSION['msg']="LOGIN FAILED.";
        unset($_SESSION['err']);
        myRedirect("index.php","ERROR");
        mysqli_free_result($risposta);
        mysqli_close($conn);
    }
}

function myImages($email){	//recupero le immagini associate all'utente
	$conn = dbConnect();
	$query = "SELECT * FROM utenti WHERE utente='$email'FOR UPDATE";
	if(!$risp = mysqli_query($conn,$query)){
		$_SESSION['msg']="QUERY NON ESEGUITA. ERRORE di collegamento al DB.<br>";
		myRedirect("index.php","Errore di collegamento al DB");
	}
	if (mysqli_num_rows($risp)== 1) { //decripto la stringa
		$pimages= mysqli_fetch_array($risp);
// 		$ciphering = "AES-128-CTR";
// 		$iv_length = openssl_cipher_iv_length($ciphering);
// 		$options = 0;
// 	//	$decryption_iv = random_bytes($iv_length);
// 		$decryption_key = openssl_digest(php_uname(), 'MD5', TRUE);
// 		$decryption = openssl_decrypt ($encrypted['password'], $ciphering, $decryption_key, $options, $encryption_iv);
// 		echo $decryption;
// 		if($decryption != FALSE){ //se si è riusciti a decriptare devo creare l'array di immagini
// 			$personal_images= unserialize($decryption);
// 		}

		$personal_images=explode( ',', $pimages['password']);
	//	$fromDB = unserialize(base64_decode($pimages['password'])); 
	//	$personal_images= unserialize($pimages['password']);
		mysqli_free_result($risp);
		mysqli_close($conn);
	}else{
		//devo unsettarre le SESSION?
		$_SESSION['msg']="ERROR - No secret registreted for this user. <br>";
		myRedirect("index.php","ERROR");
		mysqli_free_result($risp);
		mysqli_close($conn);
	}
	
	return $personal_images;
}
function myDisplayImage(){ //immagini during LOGIN
 	$n=count($_SESSION['logimages']); 
 	echo "logimages: ".$n."<br>";
 	echo "already displayed: ".count($_SESSION['already'])."<br>";
 	
 	$pic = $_SESSION['logimages'][array_rand($_SESSION['logimages'])];//ritorna un'immagine random dal vettore che contiene sia immagini dell'utente random
 	if(in_array($pic,$_SESSION['already'])){
 		$pic = $_SESSION['logimages'][array_rand($_SESSION['logimages'])];
 	}
 	$_SESSION['already'][]= $pic;
 	return $pic; 
}



function myPossible($from,$to){
    if($from >= $to){
        $_SESSION['msg']="PERCORSO IMPOSSIBILE. Riprova";
        myRedirect("prenotazione.php", "PERCORSO ERRATO");
    }
    return;
}

function myAlreadyIn($from,$to){
    $conn = dbConnect();
    $sql="SELECT * FROM percorsi WHERE partenze='$from' OR arrivi='$to' FOR UPDATE"; 
    if (!$ris = mysqli_query($conn,$sql))
        myRedirect("prenotazione.php","QUERY FALLITA - myAlreadyIn troll");
        if(($t=mysqli_num_rows($ris))>0){
            $_SESSION['msg']="Entrambe o una delle tue scelte sono gi&agrave; presenti nel DataBase, usa il menu a tendina.";
            myRedirect("prenotazione.php","RETRY");
        }
        return;
    mysqli_free_result($ris);
    mysqli_close($conn);
}

function mynewFirst($from,$to,$p,$riga){
    $conn = dbConnect();
 try{
    mysqli_autocommit($conn,false); 
    $sql="INSERT INTO percorsi(partenze,arrivi,totale) VALUES('$from','$to','$p')";
    if(!($ris = mysqli_query($conn,$sql)))
        throw new Exception("QUERY FALLITA - myResearchnew INSERT MIN ");
    $sql="INSERT INTO percorsi(partenze,arrivi,totale) VALUES('$to','".$riga['partenze']."','0')";
    if(!($ris = mysqli_query($conn,$sql)))
        throw new Exception("QUERY FALLITA - myResearchnew INSERT MIN ");
    
    $sql="INSERT INTO prenotazioni(utente,partenze,arrivi,passeggeri) VALUES('".$_SESSION['myuser']."','$from','$to','$p')";
    if(!($ris = mysqli_query($conn,$sql)))
       throw new Exception("QUERY FALLITA - myResearchnew INSERT MIN ");
   
    
    if (!mysqli_commit($conn)) {// per avere il corretto messaggio di errore
            throw Exception("Commit fallita");
     }
     //è andato tutto bene
     $_SESSION['msg']= "LA PRENOTAZIONE HA AVUTO SUCCESSO";
     mysqli_free_result($ris);
     mysqli_close($conn);
     myRedirect("ppage.php");
     
 }catch(Exception $e){
     mysqli_rollback($conn);
     $_SESSION['msg'] =$e->getMessage();
     myRedirect("prenotazione.php","prenota");
 }

}

function mynewLast($from,$to,$p,$riga){ //se sono due tratte staccate dall'attuale percorso
    $conn = dbConnect();
    try{
        mysqli_autocommit($conn,false);
        $sql="INSERT INTO percorsi(partenze,arrivi,totale) VALUES('$from','$to','$p')";
        if(!($ris = mysqli_query($conn,$sql)))
            throw new Exception("QUERY FALLITA - myResearchnew INSERT MIN ");
        $sql="INSERT INTO percorsi(partenze,arrivi,totale) VALUES('".$riga['arrivi']."','$from','0')";
        if(!($ris = mysqli_query($conn,$sql)))
            throw new Exception("QUERY FALLITA - myResearchnew INSERT MIN ");
        $sql="INSERT INTO prenotazioni(utente,partenze,arrivi,passeggeri) VALUES('".$_SESSION['myuser']."','$from','$to','$p')";
        if(!($ris = mysqli_query($conn,$sql)))
            throw new Exception("QUERY FALLITA - myResearchnew INSERT PRENOTAZIONI MIN");
        
                         
       if (!mysqli_commit($conn)) {// per avere il corretto messaggio di errore
            throw Exception("Commit fallita");
       }
      //è andato tutto bene
       $_SESSION['msg']= "LA PRENOTAZIONE HA AVUTO SUCCESSO";
       mysqli_free_result($ris);
       mysqli_close($conn);
       myRedirect("ppage.php");
                        
    }catch(Exception $e){
        mysqli_rollback($conn);
        $_SESSION['msg'] =$e->getMessage();
        myRedirect("prenotazione.php","prenota");
    }
    
}

function myResearch($from, $to,$p){   //controlla che nel tragitto indicato ci siano posti 
    $conn = dbConnect();   
    global $max;
  try{
    mysqli_autocommit($conn,false);
    $sql="SELECT * FROM percorsi WHERE partenze>='$from' AND partenze<'$to' FOR UPDATE";
    if (!$ris = mysqli_query($conn,$sql))
        throw new Exception("query fallita myResearch SELECT");
    if(($t=mysqli_num_rows($ris))>0){
           while($riga = mysqli_fetch_array($ris)){
               if(($riga['totale']+ $p)>$max){
                   $_SESSION['msg']="RICHIESTA NEGATA.<br>Non c'&egrave; posto per tutti.";
                   mysqli_close($conn);
                   myRedirect("prenotazione.php","FAILURE");
               }
           }//significa che non superano il massimo -> ok=true
           $sql="UPDATE percorsi SET totale=totale+'$p' WHERE partenze>='$from' AND partenze<'$to'"; 
           if(!$ris = mysqli_query($conn,$sql)){
               throw new Exception("QUERY FALLITA. myResearch UPDATE");
           }
           
           $sql="INSERT INTO prenotazioni(utente,partenze,arrivi,passeggeri) VALUES('".$_SESSION['myuser']."','$from','$to','$p')";
           if(!($ris = mysqli_query($conn,$sql)))
               throw new Exception("QUERY FALLITA - myResearchnew INSERT PRENOTAZIONI MIN");
           
           if (!mysqli_commit($conn)) {
               // per avere il corretto messaggio di errore
               throw Exception("Commit fallita myResearch");
           }
           //è andato tutto bene
           $_SESSION['msg']= "Record successfully updated.";
            myRedirect("ppage.php");
           
       }else{throw new Exception("Nessun risultato");}
  
 }catch(Exception $e){
      mysqli_rollback($conn);
      $_SESSION['msg'] =$e->getMessage();
      myRedirect("prenotazione.php","prenota");
 }
 mysqli_free_result($ris);  
 mysqli_close($conn);
}

function myResearchnew($from, $to,$p){ //valori intermedi
    $conn = dbConnect();
    global $max;
    $mini=0;$maxi=0;
    try{
        mysqli_autocommit($conn,false);  
        myAlreadyIn($from,$to);
       
            //CALCOLO IL MINIMO
           $query=" SELECT * FROM percorsi WHERE partenze IN(SELECT MIN(partenze)FROM percorsi) FOR UPDATE";
           if (!$ris = mysqli_query($conn,$query))
               throw new Exception("QUERY FALLITA - myResearchnew MIN");  
           $riga= mysqli_fetch_array($ris);//tupla che contiene il minimo AL-BB
           if($from < $riga['partenze']){ //se parte da prima della prima partenza salvatare   
               if($to< $riga['partenze']){ //stessa tupla da dividere es. AA-AK
                  mynewFirst($from,$to,$p,$riga);   
               }else{
                   $rowf['partenze']=$riga['arrivi']; //la tupla che contiene la from è AA-->AL             
                   $rowf['arrivi']=$riga['partenze'];   //per la insert dopo!   
                   $mini=1;
                   $sql="INSERT INTO percorsi(partenze,arrivi,totale) VALUES('$from','".$rowf['arrivi']."','$p')";
                   if(!($ris = mysqli_query($conn,$sql)))
                       throw new Exception("QUERY FALLITA - myResearchnew INSERT from");  
               }
           }else { 
               //CERCO LA TUPLA CHE CONTIENE LA FROM
               $query=" SELECT * FROM percorsi WHERE partenze<'$from' AND arrivi>'$from' FOR UPDATE";
               if (!$ris = mysqli_query($conn,$query))
                   throw new Exception("QUERY FALLITA - myResearchnew tupla from");
               $rowf= mysqli_fetch_array($ris); //dovrà essere splittato
               if(($rowf['totale']+$p)>$max )
                   throw new Exception("RICHIESTA NEGATA.<br>Non c'&egrave; posto per tutti.");
               
           }
           //CALCOLO IL MASSIMO
            $query=" SELECT * FROM percorsi WHERE arrivi IN (SELECT MAX(arrivi)FROM percorsi) FOR UPDATE"; 
            if (!$ris = mysqli_query($conn,$query))
                throw new Exception("QUERY FALLITA - myResearchnew MAX");
            $riga= mysqli_fetch_array($ris); //tupla che contiene il massimo FF-->KK
            if($to > $riga['arrivi']){ 
                if($from > $riga['arrivi']){
                    mynewLast($from,$to,$p,$riga);
                }else{
                    $maxi=1;
                    $rowt['partenze']=$riga['partenze'];
                    if(($riga['totale']+$p)>$max)
                        throw new Exception("RICHIESTA NEGATA.<br>Non c'&egrave; posto per tutti.");
                    $tot=$riga['total']+$p;
                    $sql="INSERT INTO percorsi(partenze,arrivi,totale) VALUES('".$riga['arrivi']."','$to','$tot')";
                    if(!($ris = mysqli_query($conn,$sql)))
                        throw new Exception("QUERY FALLITA - myResearchnew INSERT to");  
                }
               
            }
            else {
               //CERCO LA TUPLA CHE CONTIENE LA TO
              $query=" SELECT * FROM percorsi WHERE partenze<'$to' AND arrivi>'$to' FOR UPDATE";
              if (!$ris = mysqli_query($conn,$query))
                  throw new Exception("QUERY FALLITA -myResearchnew tupla to");
              $rowt= mysqli_fetch_array($ris); //percorso dovrà essere splittato  
              if(($rowt['totale']+$p)>$max)
                  throw new Exception("RICHIESTA NEGATA.<br>Non c'&egrave; posto per tutti.");
              
           }
             //CERCO I PERCORSI INTERMEDI  
               $query="SELECT * FROM percorsi WHERE partenze>='".$rowf['arrivi']."' AND partenze< '".$rowt['partenze']."' FOR UPDATE";
               if (!$ris = mysqli_query($conn,$query))
                   throw new Exception("QUERY FALLITA -myResearchnew prenotazioni tot");
   
              //CONTROLLO ECCEDENZA MAX 
               if(($t=mysqli_num_rows($ris))>0){
                  while($rowi= mysqli_fetch_array($ris)){
                   if(($rowi['totale']+$p)>$max ){
                       throw new Exception("RICHIESTA NEGATA.<br>Non c'&egrave; posto per tutti.");
                   }
                  }
               
                  //SE è andato tutto bene per i percorsi intemedi devo aggiornare il totale
                  $query="SELECT * FROM percorsi WHERE partenze>='".$rowf['arrivi']."' AND partenze< '".$rowt['partenze']."'FOR UPDATE";
                  if (!$ris = mysqli_query($conn,$query))
                      throw new Exception("QUERY FALLITA - stop intermedi");
                  while($rowi= mysqli_fetch_array($ris)){
                          $sql="UPDATE percorsi SET totale=totale+'$p' WHERE partenze='".$rowi['partenze']."' and arrivi='".$rowi['arrivi']."' ";
                          if(!$ris = mysqli_query($conn,$sql))
                              throw new Exception("QUERY FALLITA - UPDATE totale");
                  }
               }
               if(!$mini){
                  //se è andato tutto bene creo il nuovo percorso UTENTE e aggiorno PRENOTAZIONI
                   $sql="UPDATE percorsi SET arrivi='$from' WHERE partenze='".$rowf['partenze']."' ";
                   if(!$ris = mysqli_query($conn,$sql))
                       throw new Exception("QUERY FALLITA - myResearchnew UPDATE from");
                    $tot=$p+$rowf['totale'];
                    $sql="INSERT INTO percorsi(partenze,arrivi,totale) VALUES('$from','".$rowf['arrivi']."','$tot')";
                    if(!($ris = mysqli_query($conn,$sql)))
                       throw new Exception("QUERY FALLITA - myResearchnew INSERT from");
               }
               if(!$maxi){
                 //SPLIT RIGA TO  
                   $sql="UPDATE percorsi SET arrivi='$to',totale=totale+'$p' WHERE partenze='".$rowt['partenze']."' ";
                   if(!$ris = mysqli_query($conn,$sql))
                       throw new Exception("QUERY FALLITA - UPDATE from");
                   $sql="INSERT INTO percorsi(partenze,arrivi,totale) VALUES('$to','".$rowt['arrivi']."','".$rowt['totale']."')";
                   if(!($ris = mysqli_query($conn,$sql)))
                       throw new Exception("QUERY FALLITA - INSERT to");
               }
                   
             $sql="INSERT INTO prenotazioni(utente,partenze,arrivi,passeggeri) VALUES ('".$_SESSION['myuser']."','$from','$to','$p')";
             if(!($ris = mysqli_query($conn,$sql)))
                   throw new Exception("QUERY FALLITA - myResearchnew INSERT from nuovo utente");
     
               if (!mysqli_commit($conn)) {// per avere il corretto messaggio di errore
                throw Exception("Commit fallita");
               }
               //è andato tutto bene
               $_SESSION['msg']= "Record successfully updated.";
               
               mysqli_close($conn);
               myRedirect("ppage.php");


    }catch(Exception $e){
        mysqli_rollback($conn); 
        $_SESSION['msg'] =$e->getMessage();
        myRedirect("prenotazione.php","prenota");
    }
   
}

function myResearchfrom($from,$a, $p){ 
    $conn = dbConnect();
    global $max;
    $maxi=0;
    try{
        mysqli_autocommit($conn,false);
        myAlreadyIn(NULL, $a);
        
        $sql="SELECT * FROM percorsi WHERE partenze='$from' FOR UPDATE";
        if(!($ris = mysqli_query($conn,$sql)))
            throw new Exception("QUERY FALLITA - myResearchfrom ");  
        $row=mysqli_fetch_array($ris);//tupla di partenza
       
        if($row['totale']<= $max){
            //stessa tupla da dividere
            if($a < $row['arrivi']){
                $sql="UPDATE percorsi SET arrivi='$a',totale=totale+'$p' WHERE partenze='$from' AND arrivi='".$row['arrivi']."' ";
                if(!$ris = mysqli_query($conn,$sql))
                    throw new Exception("QUERY FALLITA - myResearchfrom UPDATE from");   
                $sql="INSERT INTO percorsi(partenze,arrivi,totale) VALUES('$a','".$row['arrivi']."','".$row['totale']."')";
                if(!($ris = mysqli_query($conn,$sql)))
                    throw new Exception("QUERY FALLITA - myResearchfromINSERT from");            
            }else{
                //MASSIMO
                $query=" SELECT * FROM percorsi WHERE arrivi IN (SELECT MAX(arrivi)FROM percorsi) FOR UPDATE";
                if (!$ris = mysqli_query($conn,$query))
                    throw new Exception("QUERY FALLITA - myResearchfromMAX");
                $riga=mysqli_fetch_array($ris);//contiene la tupla FF--KK
                if($a > $riga['arrivi']){
                    if(($riga['totale']+$p)>$max)
                        throw new Exception("RICHIESTA NEGATA.<br>Non c'&egrave; posto per tutti.");
                    $tot=$riga['total']+$p;
                    $sql="INSERT INTO percorsi(partenze,arrivi,totale) VALUES('".$riga['arrivi']."','$a','$tot')";
                    if(!($ris = mysqli_query($conn,$sql)))
                            throw new Exception("QUERY FALLITA - myResearchfrom INSERT to");
                    $maxi=1;
                }else{
                    //CERCO LA TUPLA CHE CONTIENE LA TO
                    $query=" SELECT * FROM percorsi WHERE partenze<'$a' AND arrivi>'$a' FOR UPDATE";
                    if (!$ris = mysqli_query($conn,$query))
                        throw new Exception("QUERY FALLITA -myResearchnew tupla to");
                    $rowt= mysqli_fetch_array($ris); //percorso dovrà essere splittato  
                }

                //CERCO I PERCORSI INTERMEDI
                $query="SELECT * FROM percorsi WHERE partenze>='".$row['arrivi']."' AND partenze< '".$rowt['partenze']."' FOR UPDATE";
                if (!$ris = mysqli_query($conn,$query))
                    throw new Exception("QUERY FALLITA -myResearchnew prenotazioni tot");
                //CONTROLLO ECCEDENZA MAX
                 while($rowi= mysqli_fetch_array($ris)){
                        if(($rowt['totale']+$p)>$max ||($rowi['totale']+$p)>$max ){
                            throw new Exception("RICHIESTA NEGATA.<br>Non c'&egrave; posto per tutti.");
                        }
                 }
                  //PERCORSI INTERMEDI NON DEVONO ESSERE SPLITTATI-update totale
                 $query="SELECT * FROM percorsi WHERE partenze>='".$row['arrivi']."' AND partenze< '".$rowt['partenze']."'FOR UPDATE";
                 if (!$ris = mysqli_query($conn,$query))
                        throw new Exception("QUERY FALLITA - stop intermedi");
                 while($rowi= mysqli_fetch_array($ris)){
                       $sql="UPDATE percorsi SET totale=totale+'$p' WHERE partenze>='".$row['arrivi']."' AND partenze< '".$rowt['partenze']."' ";
                       if(!$ris = mysqli_query($conn,$sql))
                          throw new Exception("QUERY FALLITA - UPDATE totale");
                 }
                 //UPDATE FROM
                 $sql="UPDATE percorsi SET totale=totale+'$p' WHERE partenze='$from' AND arrivi='".$row['arrivi']."' ";
                 if(!$ris = mysqli_query($conn,$sql))
                     throw new Exception("QUERY FALLITA - myResearchnew UPDATE from"); 
                 
                 //SPLIT RIGA TO
                 if(!$maxi){
                     $sql="UPDATE percorsi SET arrivi='$a',totale=totale+'$p' WHERE partenze='".$rowt['partenze']."' ";
                     if(!$ris = mysqli_query($conn,$sql))
                             throw new Exception("QUERY FALLITA - UPDATE from");
                     $sql="INSERT INTO percorsi(partenze,arrivi,totale) VALUES('$a','".$rowt['arrivi']."','".$rowt['totale']."')";
                     if(!($ris = mysqli_query($conn,$sql)))
                             throw new Exception("QUERY FALLITA - INSERT to");
                 }
                //INSERT IN PRENOTAZIONI
                 $sql="INSERT INTO prenotazioni(utente,partenze,arrivi,passeggeri) VALUES('".$_SESSION['myuser']."','$from','$a','$p')";
                 if(!($ris = mysqli_query($conn,$sql)))
                       throw new Exception("QUERY FALLITA - INSERT to pr");
             
                
            }
        }else{
            throw new Exception("RICHIESTA NEGATA.<br>Non c'&egrave; posto per tutti.");
        }
 
    if (!mysqli_commit($conn)) {// per avere il corretto messaggio di errore
        throw Exception("Commit fallita");
    }
    //è andato tutto bene
    $_SESSION['msg']= "Record successfully updated.";
   
    mysqli_close($conn);
    myRedirect("ppage.php");
    
    
}catch(Exception $e){
    mysqli_rollback($conn);
    $_SESSION['msg'] =$e->getMessage();
    myRedirect("prenotazione.php","prenota");
}

}

function myResearchto($da,$to, $p){
    $conn = dbConnect();
    global $max;
    $mini=0;
    try{
        mysqli_autocommit($conn,false);
        myAlreadyIn($da,NULL);
        
        $sql="SELECT * FROM percorsi WHERE arrivi='$to' FOR UPDATE";
        if(!($ris = mysqli_query($conn,$sql)))
            throw new Exception("QUERY FALLITA - myResearchfrom ");
        $row=mysqli_fetch_array($ris);//tupla di arrivo 
            
        if($row['totale']<= $max){
           //stessa tupla da dividere
           if($da > $row['partenze']){
                $sql="UPDATE percorsi SET arrivi='$da' WHERE arrivi='$to' AND partenze='".$row['partenze']."' ";
                if(!$ris = mysqli_query($conn,$sql))
                    throw new Exception("QUERY FALLITA - myResearchfrom UPDATE from");
                $tot=$row['totale']+$p;
                $sql="INSERT INTO percorsi(partenze,arrivi,totale) VALUES('$da','".$row['arrivi']."','$tot')";
                if(!($ris = mysqli_query($conn,$sql)))
                            throw new Exception("QUERY FALLITA - myResearchfromINSERT from");
           }else{
                    //MINIMO
                    $query=" SELECT * FROM percorsi WHERE partenze IN (SELECT MIN(partenze)FROM percorsi) FOR UPDATE";
                    if (!$ris = mysqli_query($conn,$query))
                        throw new Exception("QUERY FALLITA - myResearchfromMAX");
                    $riga=mysqli_fetch_array($ris);//contiene la tupla AL--BB
                    if($da < $riga['partenze']){
                            if(($riga['totale']+$p)>$max)
                                throw new Exception("RICHIESTA NEGATA.<br>Non c'&egrave; posto per tutti.");
                            $sql="INSERT INTO percorsi(partenze,arrivi,totale) VALUES(''$da,'".$riga['partenze']."','$p')";
                            if(!($ris = mysqli_query($conn,$sql)))
                                    throw new Exception("QUERY FALLITA - myResearchfrom INSERT to");
                            $mini=1;
                    }else{
                            //CERCO LA TUPLA CHE CONTIENE LA FROM
                            $query=" SELECT * FROM percorsi WHERE partenze<'$da' AND arrivi>'$da' FOR UPDATE";
                            if (!$ris = mysqli_query($conn,$query))
                                throw new Exception("QUERY FALLITA -myResearchfrom tupla to");
                            $rowf= mysqli_fetch_array($ris); //percorso dovrà essere splittato
                            if(($rowf['totale']+$p)>$max)
                                throw new Exception("RICHIESTA NEGATA.<br>Non c'&egrave; posto per tutti.");
                        }
                        
                        //CERCO I PERCORSI INTERMEDI
                        $query="SELECT * FROM percorsi WHERE partenze>='".$rowf['arrivi']."' AND partenze< '".$row['partenze']."' FOR UPDATE";
                        if (!$ris = mysqli_query($conn,$query))
                            throw new Exception("QUERY FALLITA -myResearchnew prenotazioni tot");
                            //CONTROLLO ECCEDENZA MAX
                        while($rowi= mysqli_fetch_array($ris)){
                              if(($rowi['totale']+$p) >$max ){
                                    throw new Exception("RICHIESTA NEGATA.<br>Non c'&egrave; posto per tutti.");
                              }
                        }
                            //PERCORSI INTERMEDI NON DEVONO ESSERE SPLITTATI-update totale
                            $query="SELECT * FROM percorsi WHERE partenze>='".$rowf['arrivi']."' AND partenze< '".$row['partenze']."'FOR UPDATE";
                            if (!$ris = mysqli_query($conn,$query))
                                throw new Exception("QUERY FALLITA - stop intermedi");
                            while($rowi= mysqli_fetch_array($ris)){
                                $sql="UPDATE percorsi SET totale=totale+'$p' WHERE partenze>='".$rowf['arrivi']."' AND partenze< '".$row['partenze']."' ";
                                if(!$ris = mysqli_query($conn,$sql))
                                    throw new Exception("QUERY FALLITA - UPDATE totale");
                            }
                            //UPDATE TO
                            $sql="UPDATE percorsi SET totale=totale+'$p' WHERE arrivi='$to' AND partenze='".$row['partenze']."' ";
                            if(!$ris = mysqli_query($conn,$sql))
                                throw new Exception("QUERY FALLITA - myResearchnew UPDATE from");
                                    
                            //SPLIT RIGA FROM
                            if(!$mini){
                                 $sql="UPDATE percorsi SET arrivi='$da',totale=totale+'$p' WHERE partenze='".$rowf['partenze']."' ";
                                 if(!$ris = mysqli_query($conn,$sql))
                                      throw new Exception("QUERY FALLITA - UPDATE from");
                                 $tot=$rowf['totale']+$p;
                                 $sql="INSERT INTO percorsi(partenze,arrivi,totale) VALUES('$da','".$rowf['arrivi']."','$tot')";
                                 if(!($ris = mysqli_query($conn,$sql)))
                                      throw new Exception("QUERY FALLITA - INSERT to");
                            }
                            //INSERT IN PRENOTAZIONI
                             $sql="INSERT INTO prenotazioni(utente,partenze,arrivi,passeggeri) VALUES('".$_SESSION['myuser']."','$da','$to','$p')";
                             if(!($ris = mysqli_query($conn,$sql)))
                                throw new Exception("QUERY FALLITA - INSERT to pr");
                                        
                                        
             }
            }else{
                throw new Exception("RICHIESTA NEGATA.<br>Non c'&egrave; posto per tutti.");
            }
            
            if (!mysqli_commit($conn)) {// per avere il corretto messaggio di errore
                throw Exception("Commit fallita");
            }
            //è andato tutto bene
            $_SESSION['msg']= "Record successfully updated.";
            mysqli_free_result($ris);
            mysqli_close($conn);
            myRedirect("ppage.php");
            
            
    }catch(Exception $e){
        mysqli_rollback($conn);
        $_SESSION['msg'] =$e->getMessage();
        myRedirect("prenotazione.php","prenota");
    }
    
}



function myDelete(){
    $conn=dbConnect();
 try{
    mysqli_autocommit($conn, false);
    $query="SELECT * FROM prenotazioni WHERE utente='".$_SESSION['myuser']."'  FOR UPDATE ";
    if(!$ris=mysqli_query($conn, $query))
        throw new Exception("QUERY FALLITA - myDelete from prenotazioni");
    $riga=mysqli_fetch_array($ris); //prenotazione utente

    if(($t=mysqli_num_rows($ris))>0){ 
        $query="SELECT * FROM percorsi WHERE partenze>='".$riga['partenze']."' AND partenze<'".$riga['arrivi']."' FOR UPDATE ";
        if(!$ris=mysqli_query($conn, $query))
            throw new Exception("QUERY FALLITA - myDelete from prenotazioni"); 
        while($riga1=mysqli_fetch_array($ris)){//elenco delle tuple di quell'utente
            $sql="UPDATE percorsi SET totale=totale-'".$riga['passeggeri']."' WHERE partenze>='".$riga['partenze']."' AND partenze<'".$riga['arrivi']."' ";
            if (!$ris = mysqli_query($conn,$sql))
                throw new Exception("QUERY FALLITA -  myDelete update");
        }
        //devo eliminare le entry nelle tabelle prenotazioni e percorsi
        $sql="DELETE FROM prenotazioni WHERE utente='".$_SESSION['myuser']."'";
        if (!$ris = mysqli_query($conn,$sql))
            throw new Exception("QUERY FALLITA -  myDelete delete");
    }else{
        throw new Exception("Nessuna prenotazione da eliminare.");
    }
        
        
     if (!mysqli_commit($conn)) {// per avere il corretto messaggio di errore
        throw Exception("  myDelete Commit fallita");
     }
     //è andato tutto bene
     $_SESSION['msg']= "PRENOTAZIONE ELIMINATA CON SUCCESSO.";
     mysqli_close($conn);
     myRedirect("ppage.php");
        
   
 }catch(Exception $e){
    mysqli_rollback($conn);
    $_SESSION['msg'] =$e->getMessage();
    myRedirect("ppage.php","ELIMINAZIONE");
 }
   

    
 }

?>