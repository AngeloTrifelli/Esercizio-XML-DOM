<?php
    require_once("connection.php");
    session_start();

    $xmlStringArtisti = "";
    foreach (file("../XML/artisti.xml") as $node){
        $xmlStringArtisti .= trim($node);
    }

    $docArtisti = new DOMDocument();
    $docArtisti->loadXML($xmlStringArtisti, LIBXML_NOENT);

    $listaArtisti = $docArtisti->documentElement->childNodes;

    $xpathArtisti = new DOMXPath($docArtisti);
    $linkImmagini = $xpathArtisti->query("//@linkImmagine");

    
    $xmlStringBiglietti = "";
    foreach (file("../XML/biglietti.xml") as $node){
        $xmlStringBiglietti .= trim($node);
    }

    $docBiglietti = new DOMDocument();
    $docBiglietti->loadXML($xmlStringBiglietti);

    $xpathBiglietti = new DOMXPath($docBiglietti);

    $tipoBiglietti = $xpathBiglietti->query("//@tipo");
    $prezzoBiglietti = $xpathBiglietti->query("//prezzo");

    $associazionePrezzo = array();          /*Questo array verrà usato dopo per assegnare correttamente il prezzo a ciascun tipo di biglietto*/

    for($i=0 ; $i < $tipoBiglietti->length ; $i++){
        $tipoBiglietto = $tipoBiglietti->item($i);
        $testoTipoBiglietto = $tipoBiglietto->textContent;

        $prezzoBiglietto = $prezzoBiglietti ->item($i);
        $testoPrezzoBiglietto = $prezzoBiglietto->textContent;

        $associazionePrezzo[$testoTipoBiglietto] = $testoPrezzoBiglietto;
    }


    $xmlStringUtenti = "";
    foreach(file("../XML/utenti.xml") as $node){
        $xmlStringUtenti .= trim($node);
    }

    $docUtenti = new DOMDocument();
    $docUtenti->loadXML($xmlStringUtenti);

    $listaPrenotazioni = $docUtenti->getElementsByTagName("prenotazione");    /*Verrà utilizzata dopo per modificare dinamicamente il numero di posti disponibili*/
    




    if(isset($_POST['logout'])){
        unset($_SESSION['emailUtente']);
        unset($_SESSION['passwordUtente']);
        header("Location: intro.php");
    }

?>




<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Lista artisti</title>
    <style>
    <?php  include "../CSS/band.css"  ?>
    </style>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" />
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat&family=Poppins:ital,wght@1,200&family=Ubuntu:wght@500&display=swap" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat&family=Oxygen:wght@300&family=Poppins:ital,wght@1,200&family=Ubuntu:wght@500&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500&display=swap" rel="stylesheet">
</head>

<body>
    <div class="navbar black shadow">

            <?php
                if(isset($_SESSION['emailUtente']) && isset($_SESSION['passwordUtente']) ){
            ?>
                <a href="./intro.php" class="navbar-item padding-larger button">HOME</a>
                <a href="./paginaUtente.php" class="navbar-item padding-larger button floatRight">IL TUO PROFILO</a>
                <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST">
                    <input type="submit" class="black navbar-item button logoutButton floatRight" name="logout" value="LOGOUT" />
                </form>
            <?php 
                }
                else{
            ?>
                <a href="./intro.php" class="navbar-item padding-large button">HOME</a>
                <a href="./login.php" class="navbar-item padding-large button floatRight">LOGIN</a>
                <a href="./registrazione.php" class="navbar-item padding-large button floatRight">REGISTRATI</a>
            <?php
                }
            ?>
           
        </div>

    
    <?php 
        for ($i=0 ; $i< $listaArtisti->length ; $i++){
            $artista = $listaArtisti->item($i);
            $idArtista = $artista->getAttribute("idArtista");
            $linkImmagine = $linkImmagini->item($i);
            $testoImmagine = $linkImmagine->textContent;
            
            
            $listaAssociazioni = $artista->getElementsByTagName("associazionebiglietto");
            $numAssociazioni = $listaAssociazioni->length;          //numero associazioni biglietto-artista 
            
            $temp = $artista->firstChild;       //nome artista
            $testoNome = $temp->textContent;

            $temp = $temp->nextSibling;     //descrizione artista
            $testoDescrizione = $temp->textContent;

            $temp = $temp->nextSibling;     //dataOra artista

            $data = $temp->firstChild;
            $ora = $temp ->lastChild;

            $temp2 = $data->firstChild;
            $testoGiorno = $temp2->textContent;

            $temp2 = $temp2->nextSibling;
            $testoMese = $temp2->textContent;

            $temp2 = $temp2->nextSibling;
            $testoAnno = $temp2->textContent;

            $testoOra = $ora->textContent;
    ?>
        <div class="band paragraph">
            <h1><?php echo $testoNome;?></h1>
            <p>
                <img class="immagine" src="<?php echo $testoImmagine;?>" alt="immagine non trovata!" align="middle">

            </p>
            <p class="articolo">
                <?php echo $testoDescrizione; ?>
            </p>
        </div>
        <div>
            <table class="biglietto" align="center">
                <tr>
                    <td class="data"> 
                        <?php    echo $testoGiorno."-".$testoMese."-".$testoAnno." ".$testoOra; ?>
                    </td>

                    <?php 
                        for($j=0 ; $j < $numAssociazioni ; $j++){
                            $temp = $temp->nextSibling;
                            $testoTipoBiglietto = $temp->firstChild->textContent;
                            $testoNumPosti = $temp->lastChild->textContent;
                            $idAssociazione = $temp->getAttribute("id");

                            for($k=0 ; $k<$listaPrenotazioni->length ; $k++){
                                $prenotazione = $listaPrenotazioni->item($k);
                                $testoArtistaPrenotazione = $prenotazione->firstChild->textContent;
                                $testoBigliettoPrenotazione = $prenotazione->lastChild->textContent;

                                if($testoArtistaPrenotazione == $testoNome && $testoBigliettoPrenotazione == $testoTipoBiglietto){
                                    $testoNumPosti -= 1;
                                }
                            }
                    ?>

                    <td>
                        <br />
                        <span class="ticket"> BIGLIETTO <?php echo $testoTipoBiglietto." ".$associazionePrezzo[$testoTipoBiglietto];?> &euro;</span>
                        <br />
                        <?php 
                            if($testoNumPosti!=0){       // Mostro il bottone solamente se ho dei posti disponibili
                        ?>
                            <form action="./controllaBottone.php" method="POST">
                                <input class="bottone" type="submit"  name="<?php echo $idArtista."-".$idAssociazione;?>" value="Acquista ora">    
                            </form>   
                        <?php
                            }
                        ?>

                        <br />
                        <span class="posti">Posti disponibili:<?php echo $testoNumPosti;?> </span>
    
                    </td>

                    <?php
                        }
                    ?>
    
                </tr>
            </table>
        </div>
    <?php
        }
    ?>
    
</body>

</html>
