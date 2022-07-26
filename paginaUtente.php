<?php 
    require_once("connection.php");
    
    session_start();
    if(!isset($_SESSION['emailUtente']) || !isset($_SESSION['passwordUtente'])){
            header("Location: login.php");
            exit();
    }


$xmlStringUtenti = "";
foreach(file("../XML/utenti.xml") as $node){
    $xmlStringUtenti .= trim($node);
}
    $docUtenti = new DOMDocument();
    $docUtenti->loadXML($xmlStringUtenti);

    $listaUtenti= $docUtenti->documentElement->childNodes;


    $xmlStringArtisti = "";
    foreach(file("../XML/artisti.xml") as $node){
        $xmlStringArtisti .= trim($node);
    }
        $docArtisti = new DOMDocument();
        $docArtisti->loadXML($xmlStringArtisti); 
        
        $listaArtisti = $docArtisti->documentElement->childNodes;
?>



<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Profilo utente</title>

<style>
  <?php include "../CSS/paginaUtente.css" ?>
</style>
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
<div class="top">
<div class="navbar black shadow">
          <a href="./intro.php" class="navbar-item padding-large button">HOME</a>
          <a href="./band.php" class="navbar-item padding-large button">BAND</a> 
        </div>
</div>
<?php
$i=0;
$trovato="False";
while($i < $listaUtenti->length && $trovato=="False"){
        $utente= $listaUtenti->item($i);
        $elemCredenziali=$utente->getElementsByTagName("credenziali")->item(0);
        
        $testoEmailUtente=$elemCredenziali->firstChild->textContent;
        $testoPasswordUtente=$elemCredenziali->lastChild->textContent;
        if(($testoEmailUtente == ($_SESSION['emailUtente'])) && ($testoPasswordUtente == ($_SESSION['passwordUtente']))){
            
            $codFiscUtente = $utente->getAttribute("codFisc");

                $temp = $utente->firstChild;       //nome utente
                $testoNome = $temp->textContent;

                    
                $testoCognome = $utente->getElementsByTagName("cognome")->item(0)->textContent;  //cognome utente

                $elemDataDiNascita = $utente->getElementsByTagName("dataDiNascita")->item(0);  //dataDiNascita utente

                $temp2 = $elemDataDiNascita->firstChild;  //giornoNascita utente
                $testoGiorno= $temp2->textContent;

                $temp2 = $temp2->nextSibling;  //meseNascita utente
                $testoMese = $temp2->textContent;

                $temp2 = $temp2->nextSibling;  //annoNascita utente
                $testoAnno = $temp2->textContent;

                $elemIndirizzo=$utente->getElementsByTagName("indirizzoDomicilio")->item(0); //indirizzoDiDomicilio
                $testoIndirizzo="";
                $testoCivico="";

               if(isset($elemIndirizzo)){
                $testoIndirizzo= $elemIndirizzo->firstChild->textContent;  //via utente
                $temp= $elemIndirizzo->firstChild;
                $temp=$temp->nextSibling;
                if(isset($temp)){
                $testoCivico = $elemIndirizzo->lastChild->textContent; //civico utente
                }
               }
                $listaPrenotazioni = $utente->getElementsByTagName("prenotazione");
                $numPrenotazioni= $listaPrenotazioni->length;

                $testoNomeArtista= array();
                $testoTipoBiglietto= array();


                for($j=0; $j<$numPrenotazioni; $j++){

                    $elemPrenotazione=$utente->getElementsByTagName("prenotazione")->item($j); //prenotazione utente
                      
                    $testoNomeArtista[$j]= $elemPrenotazione->firstChild->textContent; //artista associato alla prenotazione j-esima dell'utente

                    $testoTipoBiglietto[$j] = $elemPrenotazione->lastChild->textContent; //tipoBiglietto associato alla prenotazione j-esima dell'utente

                   

                }
                $trovato="True";
        }
        else{
            $i++;
        }
    }
?>

         <h1>Ciao  <?php echo $testoNome; ?> <i class="fa-solid fa-hand"></i></h1>
         
         <div class="mainItem">
            <div>

            <h3 class="firstTitle">IL TUO PROFILO:</h3>
                 <ul>
                   <li><strong>Nome:</strong><?php echo $testoNome; ?></li>
                   <li><strong>Cognome:</strong><?php echo $testoCognome; ?></li>
                   <li><strong>Codice Fiscale:</strong><?php echo $codFiscUtente; ?></li>
                   <li><strong>Data di nascita:</strong><?php echo $testoAnno."-".$testoMese."-".$testoGiorno; ?></li>
                   <li><strong>Indirizzo di domicilio:</strong><?php echo $testoIndirizzo." ".$testoCivico; ?></li>
                   <li><strong>Email:</strong><?php echo $testoEmailUtente; ?></li>
                   
                </ul>
            </div>
            
        </div>

        <hr>


       

        <div class="mainItem">
            <h3 class="secondTitle"><i class="fa-solid fa-calendar"></i> Le tue prenotazioni:</h3>
            <?php
            
            for($i=0;$i<$numPrenotazioni; $i++)
            {
?>
            <div>
                <table class="biglietto" align="center">
                    <tr>
                        <?php 

                        $testoGiornoConcerto= array();
                        $testoMeseConcerto= array();
                        $testoAnnoConcerto= array();
                        $testoOraConcerto= array();

                         for($j=0; $j< $listaArtisti->length ; $j++){
                            $artista= $listaArtisti->item($j);
                            $aux=$artista->firstChild;
                                if($testoNomeArtista[$i]  == ($aux->textContent)){
                                    $aux=$aux->nextSibling;
                                    $aux=$aux->nextSibling;
        
                                    $data = $aux->firstChild;
                                    $ora = $aux ->lastChild;
                        
                                    $aux2 = $data->firstChild;
                                    $testoGiornoConcerto[$i] = $aux2->textContent;

                                    $aux2 = $aux2->nextSibling;
                                    $testoMeseConcerto[$i] = $aux2->textContent;
                        
                                    $aux2 = $aux2->nextSibling;
                                    $testoAnnoConcerto[$i] = $aux2->textContent;
                        
                                    $testoOraConcerto[$i] = $ora->textContent;
                                }
                            }
?>        
                            <td>Data:<br /><?php 
                             echo $testoGiornoConcerto[$i]."/".$testoMeseConcerto[$i]."/".$testoAnnoConcerto[$i]." ".$testoOraConcerto[$i];
                             ?></td>

                            <td>Artista:<br /><?php echo $testoNomeArtista[$i]; ?></td>

                            <td>Tipologia biglietto:<br /><?php echo $testoTipoBiglietto[$i] ; ?></td>
                        

                    </tr>
                
                </table>
                <br />
                <?php
            }
            ?>
                <div class="guitar">
                <i class="fa-solid fa-guitar"></i>
                </div>
                
            </div>
        </div>



</body>

</html>
