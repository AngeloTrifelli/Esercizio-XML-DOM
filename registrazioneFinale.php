<?php
    
    require_once("./connection.php");
    $emailErr = "False";

    

    session_start();

    
   
    

    if (!isset($_POST['registrati'])){
        if (!isset($_SESSION['accessoPermesso'])){     
            header('Location: registrazione.php');
        }
        else{
            unset($_SESSION['accessoPermesso']);       
        }
    }
    else{    
        if($_POST['email']!="" && $_POST['password']!="" && $_POST['confermaPassword']!="" && $_POST['password']==$_POST['confermaPassword']){

            $email = $_POST['email'];
            $password = $_POST['password'];

            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                $emailErr = "True";
            }
            else{
                $_SESSION['email'] = $_POST['email'];   //Imposto questa variabile di sessione in modo tale da permettere di effettuare il controllo dentro registrazioneCompletata.php
                $anno = mb_substr($_SESSION['dataNascita'], 0 , 4);
                $mese = mb_substr($_SESSION['dataNascita'], 5 , 2);
                $giorno = mb_substr($_SESSION['dataNascita'], 8 , 2);
                  
                $xmlStringUtenti = "";
                foreach(file("../XML/utenti.xml") as $node){
                    $xmlStringUtenti .= trim($node);
                }

                $docUtenti = new DOMDocument();
                $docUtenti->loadXML($xmlStringUtenti);

                $nuovoUtente = $docUtenti->createElement("utente");
                $nuovoUtente->setAttribute("codFisc", $_SESSION['codFisc']);
                $listaUtenti = $docUtenti->documentElement;
                $listaUtenti->appendChild($nuovoUtente);

                $nuovoNome = $docUtenti->createElement("nome", $_SESSION['nome']);
                $nuovoUtente->appendChild($nuovoNome);

                $nuovoCognome = $docUtenti->createElement("cognome", $_SESSION['cognome']);
                $nuovoUtente->appendChild($nuovoCognome);

                $nuovaDataNascita = $docUtenti->createElement("dataDiNascita");
                $nuovoUtente->appendChild($nuovaDataNascita);

                $nuovoGiornoNascita = $docUtenti->createElement("giorno", $giorno);
                $nuovaDataNascita->appendChild($nuovoGiornoNascita);

                $nuovoMeseNascita = $docUtenti->createElement("mese", $mese);
                $nuovaDataNascita->appendChild($nuovoMeseNascita);
                
                $nuovoAnnoNascita = $docUtenti->createElement("anno", $anno);
                $nuovaDataNascita->appendChild($nuovoAnnoNascita);

                if(isset($_SESSION['domicilio'])){
                    $nuovoIndirizzoDomicilio = $docUtenti->createElement("indirizzoDomicilio");
                    $nuovoUtente->appendChild($nuovoIndirizzoDomicilio);

                    $nuovaVia = $docUtenti->createElement("via" , $_SESSION['domicilio']);
                    $nuovoIndirizzoDomicilio->appendChild($nuovaVia);

                    if(isset($_SESSION['numCiv'])){
                        $nuovoCivico = $docUtenti->createElement("civico", $_SESSION['numCiv']);
                        $nuovoIndirizzoDomicilio->appendChild($nuovoCivico);
                    }
                }

                $nuoveCredenziali = $docUtenti->createElement("credenziali");
                $nuovoUtente->appendChild($nuoveCredenziali);

                $nuovaEmail = $docUtenti->createElement("email", $email);
                $nuoveCredenziali->appendChild($nuovaEmail);

                $nuovaPassword = $docUtenti->createElement("password", $password);
                $nuoveCredenziali->appendChild($nuovaPassword);

                $docUtenti->save("../XML/utenti.xml");

                header('Location: registrazioneCompletata.php');
                exit();
            } 
        }
    }
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">


<head>
    <title>Sapienza Musical Festival</title>

    <style>
        <?php include "../CSS/registrazioneFinale.css"   ?>
    </style>
</head>


<body>

<div class="top">
        <div class="navbar black shadow">
            <a href="./registrazione.php" class="navbar-item padding-large button">TORNA INDIETRO</a> 
        </div>
</div>








<div class="containerImmagine"> 

<div class="containerBlur">

    <div class="containerCentrale">

        <h1>REGISTRAZIONE UTENTE</h1>

        <div class="tabella">
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" >

                <div class="zonaInput">
                    <?php
                        if(isset($_POST['email'])){
                            echo "<input class=\"textInput\" type=\"text\" name=\"email\" value=\"{$_POST['email']}\" placeholder=\"Inserisci l'email\"   >";
                        }
                        else{
                            echo "<input class=\"textInput\" type=\"text\" name=\"email\" placeholder=\"Inserisci l'email\" >";     
                        }
                        if(isset($_POST['registrati']) && $_POST['email']==""){
                            echo "
                                <p class=\"errorLabel\">Inserire l'email!</p> 
                            ";
                        }
                        else{
                            if($emailErr=="True"){
                                echo "
                                    <p class=\"errorLabel\">Formato di email non valido!</p>
                                ";
                            }
                        }
                    ?>
                    <input class="textInput" type="password" name="password" placeholder="Inserisci la password" />
                    <?php 
                        if(isset($_POST['registrati']) && $_POST['password']==""){
                            echo "
                                <p class=\"errorLabel\">Inserire la password!</p> 
                            ";
                        }

                    ?>
                    <input class="textInput" type="password" name="confermaPassword" placeholder="Conferma password" />
                    <?php 
                        if(isset($_POST['registrati']) && $_POST['password']!="" && $_POST['password']!=$_POST['confermaPassword']){
                            echo "
                            <p class=\"errorLabel\">Le password inserite non corrispondono!</p> 
                            ";
                        }
                    ?>

                    
                    <input type="submit" class="continuaButton black button" name="registrati" value="Registrati" />
                    

                </div>

                

               

            </form>

        </div>
            
    </div>

</div>
</div>






</body>

</html>