<?php
    session_start();
    $xmlString = "";
    $doc = new DOMDocument();
    foreach (file("../XML/artisti.xml") as $node){
        $xmlString .= trim($node);
    }

    $doc = new DOMDocument();
    $doc->loadXML($xmlString);

    $listaArtisti = $doc->documentElement->childNodes;

    for ($i=0 ; $i<$listaArtisti->length ; $i++){
        $artista = $listaArtisti->item($i);
        $idArtista = $artista->getAttribute("idArtista");
        $listaAssociazioni = $artista->getElementsByTagName("associazionebiglietto");
        $numAssociazioni = $listaAssociazioni->length;

        for($j=0 ; $j < $numAssociazioni ; $j++){
            $associazione = $listaAssociazioni->item($j);
            $idAssociazione = $associazione->getAttribute("id");
            if(isset($_POST[$idArtista."-".$idAssociazione])){

                if(isset($_SESSION['emailUtente']) && isset($_SESSION['passwordUtente']) ){

                    $testoBiglietto = $associazione->firstChild->textContent;
                    
                    $xmlStringBiglietti = "";
                    foreach(file("../XML/biglietti.xml") as $node){
                        $xmlStringBiglietti .= trim($node);
                    }

                    $docBiglietti = new DOMDocument();
                    $docBiglietti->loadXML($xmlStringBiglietti);

                    $listaBiglietti = $docBiglietti->documentElement->childNodes;
                    $k=0;
                    $trovato = "False";
                    $testoPrezzo = 0;
                    while($k<$listaBiglietti->length && $trovato == "False"){
                        $biglietto = $listaBiglietti->item($k);
                        $tipoBiglietto = $biglietto->getAttribute("tipo");
                        if($tipoBiglietto == $testoBiglietto){
                            $testoPrezzo = $biglietto->firstChild->textContent;
                            $trovato = "True";
                        }
                        else{
                            $k++;
                        }
                    }
                    $_SESSION['idArtista'] = $idArtista;
                    $_SESSION['idAssociazione'] = $idAssociazione;
                    $_SESSION['prezzoBiglietto'] = $testoPrezzo;
                    $_SESSION['accessoPermesso'] = True;
                    header("Location: confermaPrenotazione.php");
                    }
                    else{
                        header("Location: login.php");
                    }
            }
        }
    }