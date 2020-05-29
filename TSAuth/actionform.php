<?php
    session_start(); //crea o recupera sessione
    include('myfunctions.php');
    $conn = dbConnect();
    myhttps();
    $MAX=5; //no. images
    $LMAX=8;
	
if (!empty($_POST)) {
    
    if(isset($_POST['Signin'])){	
		$email =$_REQUEST['uemail'];
		$valid=myEmail_format($email);
		
		if($valid){
			$email = htmlspecialchars($email);
			$_SESSION['remail']= $email;
		}else{
			$_SESSION['msg'] = "Invalid email format. <br> PLEASE CONTROL your e-mail and reinsert."; 
			myRedirect("registrazione.php");
		}
		// verifico che l'email non sia gia nel db
		$email = mysqli_real_escape_string($conn,$_POST['uemail']); // L'e-mail dell'utente - Escapes special characters in a string for use in an SQL statement
		$present= myEmail_registered($email);
        if ($present){ //l'utente è già registrato
        	$_SESSION['msg']="<p><span>The e-mail <i> " . $email. "</i>  is already registered! <br> Please LOGIN.</span></p>";
            myRedirect("index.php","LOGIN");
        }else { // Se invece non è presente procediamo con la registrazione
        	myRedirect("auth.php");
        }

    }elseif(isset($_POST['Auth'])) {
    	if (count($_SESSION['rimages'])< $MAX){
    		myRedirect("images.php");
    	}else {
    		$email=$_SESSION['remail'];
    		$seq= implode(",", $_SESSION['rimages']);//sequenza delle immagini
       		//$secr= htmlspecialchars($secret);
    	echo $seq;
    		register($email, $seq);
       		//encrypt password using OpenSSl Encryption method
//        		$encryption = myencrypt($secr);
//        		if($encryption){
//        			register($email, $encryption);
//        		}else{
//        			$_SESSION['msg']="ERROR - Something went wrong. <br> Sorry, TRY AGAIN.";
//        			myRedirect("index.php");
//        		}
    	}
    	
    }elseif(isset($_POST['Login'])) {

		$email=$_REQUEST['uname'];
		/*controllo formato email */
		$valid=myEmail_format($email);		
		if($valid){
			$email = htmlspecialchars($email);
			$_SESSION['pname']= $email;
		}else{
			$_SESSION['msg'] = "Invalid email format. <br> PLEASE CONTROL your e-mail and reinsert.";
			myRedirect("login.php");
		}
		
        $email = mysqli_real_escape_string($conn,$email);
        //controllo che l'email sia registrata
        if(!myEmail_registered($email)){
         	$_SESSION['msg']="<p><span>The e-mail <i> " . $email. "</i>  is not registered! <br> Please SIGN IN first.</span></p>";
         	unset($_SESSION['logimages']);
         	unset($_SESSION['pname']);
         	myRedirect("index.php");
        }else{
          	$_SESSION['logimages']= myImages($_SESSION['pname']); //array con le immagini dell'utente
           	echo $_SESSION['logimages'];
           	for($i=0; $i< ($LMAX-$MAX); $i++){
           		$dirname = "Images/";
           		$images = glob($dirname . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
           		$randomimage = $images[array_rand($images)];
            	//se non è già in personal images, aggiungo
            	if(!in_array($randomimage, $_SESSION['logimages'])){
            		echo "AGGIUNGO ".$randomimage."<br>";
            		$_SESSION['logimages'][]= $randomimage;
            	}
            }
           	myRedirect("login_images.php");
		}
		
	}elseif(isset($_POST['LoginAuth'])) {
    	if (($t=count($_SESSION['already'])) < $LMAX){ //se non sono state displayed tutte le immagini	
    		/*controllo che l'ingresso sia corretto*/
    		$dir = myDirection($_SESSION['image']);
    		if($dir!= $_POST['LoginAuth']){
    			$_SESSION['err']++;
    			echo $_SESSION['err'];
    		}  
    			myRedirect("login_images.php");
		}else {
			$dir = myDirection($_SESSION['image']);
			if($dir!= $_POST['LoginAuth']){
				$_SESSION['err']++;
				echo $_SESSION['err'];
			}  
   // 		echo "CAINO";
    		unset($_SESSION['logimages']);
    		unset($_SESSION['already']);
    		unset($_SESSION['image']);
    		login($_SESSION['pname'], $_SESSION['err']);
    	}
    	
    	
    }elseif(isset($_POST['Logout'])){
    	
        myLOGOUT();
		
    }elseif(isset($_POST['Prenota'])){
        myRedirect("prenotazione.php","Prenota");
        
    }elseif(isset($_POST['Indietro'])){
        myRedirect("index.php");
    
    }elseif(isset($_POST['Fine'])){
        
       
        if(isset($_POST['from'])&& isset($_POST['to']) && $_POST['da']=="" && $_POST['a']=="" ){ 
            $from = mysqli_real_escape_string($conn,$_POST['from']);
            $to = mysqli_real_escape_string($conn,$_POST['to']); 
            $p = mysqli_real_escape_string($conn,$_POST['passengers']); 
            myPossible($from,$to);
            myResearch($from, $to,$p);
          
        }elseif(isset($_POST['da'])&& isset($_POST['a']) && !isset($_REQUEST['from']) && !isset($_REQUEST['to'])){//tutte nuove
		
           $da=$_REQUEST['da'];
           $a=$_REQUEST['a'];
		   if(preg_match('/\W/',$da)){
			   $_SESSION['msg']="Inserisci caratteri validi.";
			   myRedirect("prenotazione.php");
		   }
		   if(preg_match('/\W/',$a)){
			   $_SESSION['msg']="Inserisci caratteri validi.";
			   myRedirect("prenotazione.php");
		   }
           $da=strtoupper($da);
           $a=strtoupper($a); 
                $from = mysqli_real_escape_string($conn,$da);
                $to = mysqli_real_escape_string($conn,$a);
               // $p=$_REQUEST['passengers'];
                $p = mysqli_real_escape_string($conn,$_POST['passengers']);
                myPossible($from,$to);
                myResearchnew($from, $to,$p); 
              
                
        }elseif(isset($_REQUEST['from']) && isset($_REQUEST['a']) && $_REQUEST['da']=="" && !isset($_POST['to'])){
				$a=$_REQUEST['a'];
			
			   if(preg_match('/\W/',$a)){
				   $_SESSION['msg']="Inserisci caratteri validi.";
				   myRedirect("prenotazione.php");
			   }
                $from = mysqli_real_escape_string($conn,$_POST['from']);
                $a = mysqli_real_escape_string($conn,$_POST['a']);
                $a=$_REQUEST['a'];
                $a=strtoupper($a); 
                $p = mysqli_real_escape_string($conn,$_POST['passengers']);
                myPossible($from,$a);
                myResearchfrom($from,$a, $p);
                
        }elseif(isset($_POST['da'])&& isset($_POST['to']) && !isset($_POST['from']) && $_POST['a']==""){
					$da=$_REQUEST['a'];
				
				   if(preg_match('/\W/',$da)){
					   $_SESSION['msg']="Inserisci caratteri validi.";
					   myRedirect("prenotazione.php");
				   }
                    $da = mysqli_real_escape_string($conn,$_POST['da']);
                    $da=strtoupper($da);
                    $to = mysqli_real_escape_string($conn,$_POST['to']);
                    $p = mysqli_real_escape_string($conn,$_POST['passengers']);
                    myPossible($da,$to);
                    myResearchto($da,$to, $p);
                    
               
        }else{ //tutti i campi inseriti
            $_SESSION['msg']="NON inserire percorsi in tutti i campi.";
            myRedirect("prenotazione.php","FAILURE");
        }
      
    }elseif(isset($_POST['Elimina'])){
        myDelete();
    }

}//fine empty

?>
