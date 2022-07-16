<?php
    require_once("connection.php");

    $trovato = "True";

    if (isset($_POST['accedi'])){
        if ($_POST['email']!="" && $_POST['password']!=""){
            
            $xmlString = "";

            foreach( file("../XML/utenti.xml") as $node){
                $xmlString .= trim($node);
            }

            $doc = new DOMDocument();
            $doc->loadXML($xmlString);

            $listaUtenti = $doc->documentElement->childNodes;

            for($i=0 ; $i < $listaUtenti->length ; $i++){
                $utente = $listaUtenti->item($i);
                $elemCredenziali = $utente->getElementsByTagName("credenziali")->item(0);
                
                $testoEmail = $elemCredenziali->firstChild->textContent;
                $testoPassword = $elemCredenziali->lastChild->textContent;
                
                if ($testoEmail == $_POST['email'] && $testoPassword == $_POST['password']){
                    $trovato = "True";

                    session_start();
                    $_SESSION['emailUtente']=$_POST['email'];
                    $_SESSION['passwordUtente']=$_POST['password'];
                    header("Location: intro.php");
                    exit();
                }              
            }
            $trovato = "False";
        }
    }
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<title>Login Sapienza Musical Festival</title>

<style>
  <?php include "../CSS/login.css" ?>
</style>
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato" />
</head>

<body>
<div class="top">
<div class="navbar black shadow">
          <a href="./intro.php" class="navbar-item padding-large button">TORNA ALLA PAGINA INIZIALE</a>
        </div>
</div>

<div class="containerImmagine">
        <div class="containerBlur">
        <div class="containerCentrale">
            <h1>LOGIN</h1>
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" >

                <div class="riga">
                  
                  <?php
                        if(isset($_POST['email'])){
                            echo "<input class=\"textInput\" type=\"text\" name=\"email\" value=\"{$_POST['email']}\" placeholder=\"Inserisci l'email\"   >";
                        }
                        else{
                            echo "<input class=\"textInput\" type=\"text\" name=\"email\" placeholder=\"Inserisci l'email\" >";     
                        }
                        if(isset($_POST['accedi']) && $_POST['email']==""){
                            echo "
                                <p class=\"errorLabel\">Inserire l'email!</p> 
                            ";
                        }
                    ?>
                 
                    <input class="textInput" type="password" name="password" placeholder="Inserisci la password" />
                        <?php 
                            if(isset($_POST['accedi']) && ($_POST['password']=="") && !( $_POST['email']=="")){
                                echo "
                                    <p class=\"errorLabel\">Inserire la password!</p> 
                                ";
                            }
                        ?>

                    <input class="bottone" type="submit" name="accedi" value="Accedi">

                    <?php 
                        if($trovato == "False"){
                            echo "
                                <p class=\"errorLabel\">Email e/o password errati!</p>
                            ";
                        }
                    ?>

                </div>

                <div id="registrazione">
                    <a  href="./registrazione.php">Non sei ancora iscritto? Clicca qui!</a>
                </div>
                
                </form>
        </div>

         </div>

    </div>

</body>
</html>
