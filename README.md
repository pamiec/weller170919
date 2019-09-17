# weller

Offene Fragen: 
Z. 83:
#  $backUrl = OOArticle::getArticleById(REX_ARTICLE_ID)->getUrl()."?action=prefill"; //#pm ??

war schon auskommentiert, untendrunter steht:


Z. 84:
   $backUrl = $_SERVER['REQUEST_URI']."?action=prefill";

Ich nehme an, die auskommentierte Zeile kann ich löschen, um Verwechslungen auszuschließen.


- - - - --  -- - - -- - -- - - -- - - - -- - - - -- - --

Z. 682 und 684:
$art = OOArticle::getArticleById("REX_ARTICLE_ID"); //Z.681
$parent = $art->getParent(); //Z. 682
$marke = str_replace(array("Fiat Professional"), array("Fiat"), $parent->getName()); //Z. 684

Da weiß ich noch nicht, worauf getParent zugreift und durch was ich ersetzen könnte, das die Funktion weiterhin noch funktioniert

- - - - --  - -- - -- - - -- - - -- - -- - - -- - - -- - -

Z. 708:

$verwaltungUrl = OOArticle::getArticleById(234)->getUrl();

Da weiß ich noch nicht wie ich das austauschen soll, $verwaltungsUrl wird später weiterverwendet z.b auf Z. 852:

<a href="<?php echo $verwaltungUrl.'?action=prefill'.'&marke='.$label.'&modell='.$shortmodell.'%&neufahrzeug='
.str_replace(0, 3, $_REQUEST["neufahrzeug"]).'&car_id='.$_REQUEST["satz_nummer"]; ?>"> 


__________________________________________________________________________________________


NEUE ÄNDERUNGEN:
Einfach im Texteditor nach "//#pm" suchen. Dort habe ich alle Änderungen im Dokument markiert und hingeschrieben was ich geändert habe.
Hier aber nochmal die Änderungen zusammengefasst:

Auf diversen Zeilen:
$this->getValue("article_id") -> $_REQUEST["article_id"]

Z. 681:
$art = $_REQUEST["article_id"]; -> $art = OOArticle::getArticleById("REX_ARTICLE_ID");

Z. 683:
$modell = $art->getName(); -> $modell = $_REQUEST["modell"];

Zeile: 745 bis 770
forschleife umgeschrieben für wordpress db


Z. 788:
$modell = $angebotData->getValue("modell"); -> $modell = $_REQUEST["modell"];


Z.811-833
$angebotData->getValue("KEY") -> $_REQUEST["KEY"];
bsp: $ez = $angebotData->getValue("ez"); -> $ez = $_REQUEST["ez"];

Z.853ff
$angebotData->getValue("neufahrzeug") -> $_REQUEST["neufahrzeug"] 
$angebotData->getValue("satz_nummer") -> $_REQUEST["satz_nummer"]



- - - - - -- - - - -- - -- - - -- - -- - - -- - -- - - ---- - -- - - -- - -- - - -- - -- 
- - - - - -- - - - -- - -- - - -- - -- - - -- - -- - - ---- - -- - - -- - -- - - -- - -- 
- - - - - -- - - - -- - -- - - -- - -- - - -- - -- - - ---- - -- - - -- - -- - - -- - -- 

ALTE ÄNDERUNGEN(nicht beachten):

Änderungen in der function.php:
__________________________________________________________________________________________

Als erstes habe ich die 'getValue Funktion von redaxo'?? durch ein request ausgetauscht:

$this->getValue("article_id")

$_REQUEST["article_id"]


__________________________________________________________________________________________

Als zweites habe ich die 'redaxo escape befehle'?? durch ein mysql befehl ausgetauscht

$sql->escape($_REQUEST["marke"]); 

mysql_escape_string($_REQUEST["marke"]);

__________________________________________________________________________________________

Dann hab ich noch ein Datenbankzugriff von redaxo auf mysql geändert und hab mich an den schon vorhanden funktionen orientiert:

                              .... // Oben im Dokument hinzugefügt
                              global $wpdb;
                              include(__dir__.'/fahrzeugsuche/db-access.php');
                              $wpdb = mysqli_connect($DB['SERVER'],$DB['USER'],$DB['PW'],$DB['TABLE']);
                              ....
                              //$sql = rex_sql::factory(); //ersetzt durch $wpdb ganz am Anfang Z.
                              $query = "SELECT 
                               `satz_nummer`
                           FROM `new_mobile_de`
                           WHERE `marke` = '".$marke."' 
                           AND `modell` LIKE '%".$modell."%' 
                           ORDER BY RAND() 
                           LIMIT 10";
                              $results = mysqli_query($wpdb, $query); //$sql->setQuery($query);

                              /*for($b=0; $b<$sql->getRows(); $b++){
                                            //Ersetzt durch unterere While-Schleife
                              array_push($randomSelectedOffers, $sql->getValue("satz_nummer"));
                              $sql->next();
                              }*/

                              while($rows = mysqli_fetch_array($results, MYSQLI_NUM)){
                                             $randomSelectedOffers[] = $rows;
                              }
