<?php
    require_once("connection.php");


    session_start();
    
    if (isset($_SESSION['idArtista']) && isset($_SESSION['idAssociazione'])){
        $xmlString = "";
        foreach(file("../XML/artisti.xml") as $node){
            $xmlString .= trim($node);
        }

        $doc = new DOMDocument();
        $doc->loadXML($xmlString);

        $listaArtisti = $doc->documentElement->childNodes;

        for($i=0; $i < $listaArtisti->length ; $i++){
            $artista = $listaArtisti->item($i);
            $idArtista = $artista->getAttribute("idArtista");
            if ($idArtista == $_SESSION['idArtista']){
                $temp = $artista->firstChild;       //nome artista
                global $testoNome;
                $testoNome = $temp->textContent;

                $temp = $temp->nextSibling;     //descrizione artista
                global $testoDescrizione;
                $testoDescrizione = $temp->textContent;

                $temp = $temp->nextSibling;     //dataOra artista

                $data = $temp->firstChild;
                $ora = $temp ->lastChild;

                $temp2 = $data->firstChild;
                global $testoGiorno;
                $testoGiorno = $temp2->textContent;

                $temp2 = $temp2->nextSibling;
                global $testoMese;
                $testoMese = $temp2->textContent;

                $temp2 = $temp2->nextSibling;
                global $testoAnno;
                $testoAnno = $temp2->textContent;

                global $testoOra;
                $testoOra = $ora->textContent;
                

                $listaAssociazioni = $artista->getElementsByTagName("associazionebiglietto");
                $numAssociazioni = $listaAssociazioni->length;
                $trovato = "False";
                $j =0;
                while ($j<$numAssociazioni && $trovato == "False"){
                    $temp = $temp ->nextSibling;
                    $idAssociazione = $temp->getAttribute("id");
                    if($idAssociazione == $_SESSION['idAssociazione']){
                        $trovato = "True";
                    }
                    else{
                        $j++;
                    }
                }
                $testoBiglietto = $temp->firstChild->textContent;
                $testoPrezzo = $_SESSION['prezzoBiglietto'];
            }
        }
    }


    if(isset($_POST['annulla']) || isset($_POST['conferma'])){
        if(isset($_POST['annulla'])){
            unset($_SESSION['idArtista']);
            unset($_SESSION['idAssociazione']);
            unset($_SESSION['prezzoBiglietto']);
            header("Location: band.php");
        }
        else{
            $emailUtente = $_SESSION['emailUtente'];
            $passwordUtente = $_SESSION['passwordUtente'];

            $xmlStringUtenti = "";
            foreach(file("../XML/utenti.xml") as $node){
                $xmlStringUtenti .= trim($node);
            }
            $docUtenti = new DOMDocument();
            $docUtenti->loadXML($xmlStringUtenti);

            $listaUtenti = $docUtenti->documentElement->childNodes;
            $trovato = "False";
            $i=0;

            while($i < $listaUtenti->length && $trovato =="False"){
                $utente = $listaUtenti->item($i);
                $credenziali = $utente->getElementsByTagName("credenziali")->item(0);
                $testoEmail = $credenziali->firstChild->textContent;
                $testoPassword = $credenziali->lastChild->textContent;
                
                if($testoEmail == $emailUtente && $testoPassword == $passwordUtente){
                    $trovato = "True";
                }
            }
 
            $nuovaPrenotazione = $docUtenti->createElement("prenotazione");
            $utente->appendChild($nuovaPrenotazione);

            $nuovoTagArtista = $docUtenti->createElement("nomeArtista", $testoNome);
            $nuovoTagBiglietto = $docUtenti->createElement("tipoBiglietto", $testoBiglietto);

            $nuovaPrenotazione->appendChild($nuovoTagArtista);
            $nuovaPrenotazione->appendChild($nuovoTagBiglietto);

            
            unset($_SESSION['idArtista']);
            unset($_SESSION['idAssociazione']);
            unset($_SESSION['prezzoBiglietto']);

            $docUtenti->save("../XML/utenti.xml");
            header("Location: intro.php");   
        }
    }
    else{
        if(isset($_SESSION['accessoPermesso'])){
            unset($_SESSION['accessoPermesso']);
        }
        else{
            header("Location: band.php");
        }
    }
    
?>







<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">


<head>
    <title>Sapienza Musical Festival</title>

    <style>
        <?php include "../CSS/confermaPrenotazione.css" ?>
    </style>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato" />
</head>


<body>



<div class="containerImmagine"> 

<div class="containerBlur">

    <div class="containerCentrale">

        <h1>RIEPILOGO</h1>

        <div class="tabella">
            <div class="riga">
                <h3>ARTISTA:</h3>
                <h3><?php echo $testoNome;?></h3>
            </div>
            <div class="riga">
                <h3>TIPO BIGLIETTO:</h3>
                <h3><?php echo $testoBiglietto?></h3>
            </div>
            <div class="riga">
                <h3>DATA CONCERTO:</h3>
                <h3>
                    <?php echo $testoGiorno."-".$testoMese."-".$testoAnno; ?>
                </h3>
            </div>
            <div class="riga">
                <h3>ORARIO:</h3>
                <h3><?php echo $testoOra;?></h3>
            </div>
            <div class="riga">
                <h3>PREZZO:</h3>
                <h3><?php echo $testoPrezzo?>&euro;</h3>
            </div>
            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
                <div class="riga">
                    <input type="submit" class="annullaButton black button" name="annulla" value="ANNULLA" />
                    <input type="submit" class="confermaButton black button" name="conferma" value="CONFERMA PRENOTAZIONE" />
                </div>
            </form>
        </div>
            
    </div>

</div>
</div>






</body>

</html>