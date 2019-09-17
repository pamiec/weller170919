# weller


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
