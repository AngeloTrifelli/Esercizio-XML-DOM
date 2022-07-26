<?php 
    require_once("connection.php");

    $duplicato = "False";

    $patternCodFisc = "/^[a-zA-Z]{6}[0-9]{2}[a-zA-Z][0-9]{2}[a-zA-Z][0-9]{3}[a-zA-Z]$/";        //Il codice fiscale viene considerato valido solamente se rispetta questo pattern

    if (isset($_POST['continua'])){
        if ($_POST['nome']!="" && $_POST['cognome']!=""  && $_POST['codFisc']!="" && $_POST['dataNascita']!="" && (($_POST['domicilio']== "" && $_POST['numCiv'] == "") || ($_POST['domicilio']!= "" ))){

            if(preg_match($patternCodFisc, $_POST['codFisc'])){
                $xmlStringUtenti = "";
                foreach(file("../XML/utenti.xml") as $node){
                    $xmlStringUtenti .= trim($node);
                }

                $docUtenti = new DOMDocument();
                $docUtenti->loadXML($xmlStringUtenti);

                $listaUtenti = $docUtenti->documentElement->childNodes;
                $i = 0;
                while($i < $listaUtenti->length && $duplicato == "False"){          //Controllo se l'utente è già registrato
                    $utente = $listaUtenti->item($i);
                    $codFiscale = $utente->getAttribute("codFisc");
                    if($codFiscale == $_POST['codFisc']){
                        $duplicato = "True";
                    }
                    else{
                        $i++;
                    }
                }

                if ($duplicato == "False"){
                    session_start();
                    $_SESSION['nome'] = $_POST['nome'];
                    $_SESSION['cognome'] = $_POST['cognome'];
                    $_SESSION['codFisc'] = $_POST['codFisc'];
                    $_SESSION['dataNascita'] = $_POST['dataNascita'];
                
                    if($_POST['domicilio']!= ""){
                        $_SESSION['domicilio'] = $_POST['domicilio'];
                        if($_POST['numCiv']!=""){
                            $_SESSION['numCiv'] = $_POST['numCiv'];
                        }
                    }

                    $_SESSION['accessoPermesso'] = "True";
                    header('Location: registrazioneFinale.php');
                    exit();
                }
            }
        }
    }
?>








<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">


<head>
    <title>Sapienza Musical Festival</title>

    <style>
        <?php include "../CSS/registrazione.css" ?>
    </style>


    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@500&display=swap" />
</head>


<body>
    <div class="top">
        <div class="navbar black shadow">
            <a href="./intro.php" class="navbar-item padding-large button">HOME</a> 
            <a href="#" class="navbar-item padding-large button">BAND</a> 
            <a href="#" id=loginButton class="navbar-item padding-large button">LOGIN</a>
     
        </div>
    </div>



    
    <div class="containerImmagine"> 

        <div class="containerBlur">

            <div class="containerCentrale">

                <h1>REGISTRAZIONE UTENTE</h1>
        
                <div class="tabella">
                    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">

                        <div class="riga">
                            <div class=containerColumn>
                                <?php 
                                    if(isset($_POST['nome'])){
                                        echo "<input class=\"textInput\" type=\"text\" name=\"nome\" value=\"{$_POST['nome']}\" placeholder=\"Inserisci il nome *\"   >";
                                    }
                                    else{
                                        echo "<input class=\"textInput\" type=\"text\" name=\"nome\" placeholder=\"Inserisci il nome *\" >";     
                                    }
                                    if(isset($_POST['continua']) && $_POST['nome']==""){
                                        echo "
                                            <p class=\"errorLabel\">Inserire il nome!</p> 
                                        ";
                                    }
                                ?>
                            </div>
                            
                            <div class="containerColumn">
                                <?php 
                                    if(isset($_POST['cognome'])){
                                        echo "<input class=\"textInput\" type=\"text\" name=\"cognome\" value=\"{$_POST['cognome']}\" placeholder=\"Inserisci il cognome *\" >";
                                    }
                                    else{
                                        echo "<input class=\"textInput\" type=\"text\" name=\"cognome\" placeholder=\"Inserisci il cognome *\" >";     
                                    }
                                    if(isset($_POST['continua']) && $_POST['cognome']==""){
                                        echo "
                                            <p class=\"errorLabel\">Inserire il cognome!</p> 
                                        ";
                                    }
                                ?>
                            </div>
                            
                        </div>

                        <div class="riga">
                            <div class="containerColumn">                               
                                <?php 
                                    if(isset($_POST['codFisc'])){
                                        echo "<input class=\"textInput\" id=\"codiceFiscale\" type=\"text\" name=\"codFisc\" value=\"{$_POST['codFisc']}\" placeholder=\"Inserisci il codice fiscale *\"   >";
                                    }
                                    else{
                                        echo "<input class=\"textInput\" id=\"codiceFiscale\" type=\"text\" name=\"codFisc\" placeholder=\"Inserisci il codice fiscale *\" >";     
                                    }
                                    if(isset($_POST['continua']) && $_POST['codFisc']==""){
                                        echo "
                                            <p class=\"errorLabel\">Inserire il codice fiscale!</p> 
                                        ";
                                    }
                                    elseif (isset($_POST['continua']) && (!preg_match($patternCodFisc, $_POST['codFisc'])) ){
                                        echo "
                                            <p class=\"errorLabel\">Il codice fiscale inserito non è valido!</p>
                                        ";
                                    }
                                ?>
                            </div>
                            

                            <div class=containerData>
                                <h3> Inserisci la data di nascita: *</h3>
                                <?php 
                                    if(isset($_POST['dataNascita'])){
                                        echo "<input class=\"dateInput\" type=\"date\" name=\"dataNascita\" value=\"{$_POST['dataNascita']}\" >";
                                    }
                                    else{
                                        echo "<input class=\"dateInput\" type=\"date\" name=\"dataNascita\">";     
                                    }
                                    if(isset($_POST['continua']) && $_POST['dataNascita']==""){
                                        echo "
                                            <p class=\"errorLabel\">Inserire la data di nascita!</p> 
                                        ";
                                    }
                                ?>
                            </div>
                            
                        
                        </div>  

                        <div class="riga">
                            <div class="containerColumn">
                                <?php 
                                    if(isset($_POST['domicilio'])){
                                        echo "<input class=\"textInput\" type=\"text\" name=\"domicilio\" value=\"{$_POST['domicilio']}\" placeholder=\"Inserisci la via\" >";
                                    }
                                    else{
                                        echo "<input class=\"textInput\" type=\"text\" name=\"domicilio\" placeholder=\"Inserisci la via\" >";     
                                    }
                                    if(isset($_POST['continua']) && ( $_POST['domicilio']=="" && $_POST['numCiv']!="")){
                                        echo "
                                            <p class=\"errorLabel\">Dati mancanti!</p> 
                                        ";
                                    }
                                ?>
                            </div>
                            
                            <?php
                                if(isset($_POST['numCiv'])){
                                    echo "<input id=\"numeroCivicoInput\" type=\"number\" name=\"numCiv\" value=\"{$_POST['numCiv']}\" placeholder=\"N°\"   >";
                                }
                                else{
                                    echo "<input id=\"numeroCivicoInput\" type=\"number\" name=\"numCiv\" placeholder=\"N°\" >";     
                                }
                            ?>
                        </div>

                        <div class="riga">
                            <input type="submit" class="continuaButton black button" name="continua" value="Continua">
                        </div>

                        <?php 
                            if ($duplicato == "True"){
                            echo "
                                <div class\"riga\">
                                <p class=\"errorLabel\">L'utente inserito è gia registrato!</p>
                                </div>
                            ";
                            }
                        ?>

                        <div class="riga">
                            <strong>* campi obbligatori</strong>
                        </div>


                    </form>

                </div>
                    
            </div>

        </div>
    </div>
    


    
 
   





</body>





</html>

