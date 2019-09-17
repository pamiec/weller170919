<?php

/*
* Add your own functions here. You can also copy some of the theme functions into this file. 
* Wordpress will use those functions instead of the original functions then.
*/


add_shortcode('fahrzeugsuche', 'fahrzeugsuche');

function fahrzeugsuche()
{
	#echo 'Shortcode für Fahrzeugsuche';

	include(__dir__."/fahrzeugsuche/fs-functions.php"); 
	include(__dir__."/fahrzeugsuche/js/fs-functions.php"); 

	$totalOffers = getTotalCount();
	echo '<pre>';
	print_r($_REQUEST);
	echo '</pre>';

	$marken = getOptionsInt('marke');
	#$model = getOptionsInt($_REQUEST["modell"]);

	echo 'Marke: '.$marke;
	#echo 'Model: '.$model;

	echo '<pre>';
	print_r($marken);
	echo '</pre>';

	echo $_REQUEST["marke"];

	  // Pager
	global $wpdb;
	include(__dir__.'/fahrzeugsuche/db-access.php');
	$wpdb = mysqli_connect($DB['SERVER'],$DB['USER'],$DB['PW'],$DB['TABLE']);


// Muss überarbeitet werden
  if($_REQUEST["marke"] && $_REQUEST["marke"] != 0)	
    $partsChanged["marke"] = $_REQUEST["marke"]; #$sql->escape($_REQUEST["marke"]); # mysql_escape_string($_REQUEST["marke"]);
  if($_REQUEST["modell"] && $_REQUEST["modell"] != 0)
    $partsChanged["modell"] = $_REQUEST["modell"]; 
/*  if((array_key_exists("neuwagen", $_REQUEST) && $_REQUEST["neuwagen"] == 1) && (array_key_exists("gebrauchtwagen", $_REQUEST) && $_REQUEST["gebrauchtwagen"] == 1))
    $partsChanged["neufahrzeug"] = "2";
  elseif((array_key_exists("neuwagen", $_REQUEST) && $_REQUEST["neuwagen"] == 1)  || ($_REQUEST["article_id"] == 11 && (!array_key_exists("changed", $_POST) || !$_POST["changed"])))
    $partsChanged["neufahrzeug"] = "1";
  elseif((array_key_exists("gebrauchtwagen", $_REQUEST) && $_REQUEST["gebrauchtwagen"] == 1)  || ($_REQUEST["article_id"] == 19 && (!array_key_exists("changed", $_POST) || !$_POST["changed"])))
    $partsChanged["neufahrzeug"] = "3";
  else
    $partsChanged["neufahrzeug"] = "2";


  if(array_key_exists("neufahrzeug", $_REQUEST) && mysql_escape_string($_REQUEST["neufahrzeug"]))
    $partsChanged["neufahrzeug"] = mysql_escape_string($_REQUEST["neufahrzeug"]);

  if(array_key_exists("kraftstoffart", $_REQUEST) && mysql_escape_string($_REQUEST["kraftstoffart"]))
    $partsChanged["kraftstoffart"] = mysql_escape_string($_REQUEST["kraftstoffart"]);

  if(array_key_exists("kilometer", $_REQUEST) && mysql_escape_string($_REQUEST["kilometer"]))
    $partsChanged["kilometer"] = mysql_escape_string($_REQUEST["kilometer"]);

  if(array_key_exists("ez", $_REQUEST) && mysql_escape_string($_REQUEST["ez"]))  
    $partsChanged["ez"] = mysql_escape_string($_REQUEST["ez"]);

  if(array_key_exists("getriebeart", $_REQUEST) && mysql_escape_string($_REQUEST["getriebeart"]))  
    $partsChanged["getriebeart"] = mysql_escape_string($_REQUEST["getriebeart"]);

  if(array_key_exists("kategorie", $_REQUEST) && mysql_escape_string($_REQUEST["kategorie"]))
    $partsChanged["kategorie"] = mysql_escape_string($_REQUEST["kategorie"]);

  if(array_key_exists("preis", $_REQUEST) && mysql_escape_string($_REQUEST["preis"]))
    $partsChanged["preis"] = mysql_escape_string($_REQUEST["preis"]);

  if(array_key_exists("fulltext", $_REQUEST) && mysql_escape_string($_REQUEST["fulltext"]))
    $partsChanged["fulltext"] = mysql_escape_string(str_replace(",", "|KOMMA|", $_REQUEST["fulltext"]));

  if(array_key_exists("standort", $_REQUEST) && mysql_escape_string($_REQUEST["standort"]))
    $partsChanged["standort"] = mysql_escape_string($_REQUEST["standort"]);
*/
#  $backUrl = OOArticle::getArticleById(REX_ARTICLE_ID)->getUrl()."?action=prefill";
   $backUrl = $_SERVER['REQUEST_URI']."?action=prefill";

  if(isset($partsChanged) && is_array($partsChanged)){
    foreach ($partsChanged as $partName => $partValue) {
      if($partValue != ""){
        $backUrl .= "&".$partName."=".$partValue;
      }
    }
  }
  /*
	  session_start();
	  $_SESSION["backUrl"] = $backUrl;
	  if(array_key_exists("changed", $_POST) && $_POST["changed"]){
	    $where = mysql_escape_string($_POST["action"]);
	    $changed = $_POST["changed"];
	  }elseif(array_key_exists("action", $_GET) && $_GET["action"]){
	    if(array_key_exists("marke", $_REQUEST) && mysql_escape_string($_REQUEST["marke"]))
	      $partsChanged["marke"] = mysql_escape_string($_REQUEST["marke"]);  
	    if(array_key_exists("modell", $_REQUEST) && mysql_escape_string($_REQUEST["modell"]))
	      $partsChanged["modell"] = (mysql_escape_string($_REQUEST["modell"]) ? mysql_escape_string($_REQUEST["modell"]) : "");
	    if((array_key_exists("neuwagen", $_REQUEST) && $_REQUEST["neuwagen"] == 1) && (array_key_exists("gebrauchtwagen", $_REQUEST) && $_REQUEST["gebrauchtwagen"] == 1)){
	      $partsChanged["neufahrzeug"] = "2";
	    }elseif((array_key_exists("neuwagen", $_REQUEST) && $_REQUEST["neuwagen"] == 1) || (array_key_exists("neufahrzeug", $_REQUEST) && mysql_escape_string($_REQUEST["neufahrzeug"]) == "1") || $this->getValue("article_id") == 11){
	      $partsChanged["neufahrzeug"] = "1";
	    }elseif((array_key_exists("gebrauchtwagen", $_REQUEST) && $_REQUEST["gebrauchtwagen"] == 1) || (array_key_exists("neufahrzeug", $_REQUEST) && mysql_escape_string($_REQUEST["neufahrzeug"]) == "3") || $this->getValue("article_id") == 19){
	      $partsChanged["neufahrzeug"] = "3";
	    }else{
	      $partsChanged["neufahrzeug"] = "2";
	    }
	    if(array_key_exists("neufahrzeug", $_REQUEST) && mysql_escape_string($_REQUEST["neufahrzeug"]))
	      $partsChanged["neufahrzeug"] = mysql_escape_string($_REQUEST["neufahrzeug"]);
	    if(array_key_exists("kraftstoffart", $_REQUEST) && mysql_escape_string($_REQUEST["kraftstoffart"]))
	      $partsChanged["kraftstoffart"] = mysql_escape_string($_REQUEST["kraftstoffart"]);
	    if(array_key_exists("kilometer", $_REQUEST) && mysql_escape_string($_REQUEST["kilometer"]))
	      $partsChanged["kilometer"] = mysql_escape_string($_REQUEST["kilometer"]);
	    if(array_key_exists("ez", $_REQUEST) && mysql_escape_string($_REQUEST["ez"]))
	      $partsChanged["ez"] = mysql_escape_string($_REQUEST["ez"]);
	    if(array_key_exists("getriebeart", $_REQUEST) && mysql_escape_string($_REQUEST["getriebeart"]))
	      $partsChanged["getriebeart"] = mysql_escape_string($_REQUEST["getriebeart"]);
	    if(array_key_exists("kategorie", $_REQUEST) && mysql_escape_string($_REQUEST["kategorie"]))
	      $partsChanged["kategorie"] = mysql_escape_string($_REQUEST["kategorie"]);
	    if(array_key_exists("preis", $_REQUEST) && mysql_escape_string($_REQUEST["preis"]))
	      $partsChanged["preis"] = mysql_escape_string($_REQUEST["preis"]);
	    if(array_key_exists("fulltext", $_REQUEST) && mysql_escape_string($_REQUEST["fulltext"]))
	      $partsChanged["fulltext"] = mysql_escape_string(str_replace(",", "|KOMMA|", $_REQUEST["fulltext"]));
	    if(array_key_exists("standort", $_REQUEST) && mysql_escape_string($_REQUEST["standort"]))
	      $partsChanged["standort"] = mysql_escape_string($_REQUEST["standort"]);
	    if(isset($partsChanged) && is_array($partsChanged)){
	      $changed = "";
	      foreach ($partsChanged as $partName => $partValue) {
	        if($partValue != ""){
	          $changed .= $partName."_".$partValue.",";
	        }
	      }
	    }
	    if(isset($changed))
	      $whereRaw = getWhereRaw($changed);
	    else
	      $whereRaw = getWhereRaw("");
	    $where = getWhere($whereRaw, "");
	  }elseif($this->getValue("article_id") == 11 || $this->getValue("article_id") == 19){
	    if($this->getValue("article_id") == 11)
	      $neufahrzeug = 1;
	    else
	      $neufahrzeug = 3;
	    $changed = "neufahrzeug_".$neufahrzeug.",".$_POST["changed"];
	    $whereRaw = getWhereRaw($changed);
	    $where = getWhere($whereRaw, "");
	  }elseif ($this->getValue("article_id") == 27) {
	    $changed = "prekategorie_('Transporter'),".$_POST["changed"];
	    $whereRaw = getWhereRaw($changed);
	    $where = getWhere($whereRaw, "");
	  }
	  if(array_key_exists("car_id", $_GET) && $_GET["car_id"]){
	    $directOffer = "style='display:none;'";
	  }
	  if(!$_POST && (!array_key_exists("car_id", $_GET) || !$_GET["car_id"]) && (!array_key_exists("action", $_GET) || $_GET["action"] != "prefill") && ($this->getValue("article_id") != 1 && $this->getValue("article_id") != 11 && $this->getValue("article_id") != 19 && $this->getValue("article_id") != 27)){
	    $where = "";
	  }
	  $amount = 20;
	  if(array_key_exists("page", $_GET) && $_GET["page"]){
	    $page = (mysql_escape_string($_GET["page"])-1);
	  }else{
	    $page = 0;
	  }

	  $results = getResults((isset($where) ? $where : ""), $page, $amount, (array_key_exists("sort", $_REQUEST) && $_REQUEST["sort"] ? $_REQUEST["sort"] : "sorttime-desc"));
	  $pagerCount = getPagerCount((isset($where) ? $where : ""), $amount);
*/

	  $i = 1;
#	  if ($this->getValue("article_id") != 1){
	    echo '<div class="fs-controller-box col-md-9">';
	      echo '<ul id="fs-pager-top" class="pagination notopmargin toppager fs-pager" '.(isset($directOffer) ? $directOffer : '').'>';
	        echo '<li class="'.($page == 0 ? "disabled" : "").' page-before"><a class="" href="#" rel="nofollow">«</a></li>';
	        if(array_key_exists("page", $_GET) && $_GET["page"]){
	          $visiblePages = array();
	          for($arrpage=($_GET["page"]-2); $arrpage<=($_GET["page"]+2); $arrpage++) {
	            array_push($visiblePages, $arrpage);
	          }
	          if($_GET["page"] == 1 && $pagerCount >= 4)
	            array_push($visiblePages, 4);
	          if($_GET["page"] <= 2 && $pagerCount >= 5)
	            array_push($visiblePages, 5);
	          if($_GET["page"] >= ($pagerCount-1))
	            array_push($visiblePages, $pagerCount-4);
	          if($_GET["page"] == $pagerCount)
	            array_push($visiblePages, $pagerCount-3);
	        }
	        while ($i <= $pagerCount) {
	          $first = ($i == ($page+1) ? "selected" : "");
	          $sel = $i-1;     
	          if(array_key_exists("page", $_GET) && $_GET["page"]){
	            if(!in_array($i, $visiblePages))
	              $hidden = "style='display: none;'";
	            else
	              $hidden = "";
	          }else{
	            $hidden = ($i > 5 ? "style='display: none;'": "");
	          }
	          if(array_key_exists("page", $_GET) && $_GET["page"]){
	            $pagerUrl = str_replace("page=".$_GET["page"], "page=".$sel, $_SERVER["REQUEST_URI"]);
	          }else{
	            $pagerUrl = $_SERVER["REQUEST_URI"].'&page='.$sel;
	          }    
	          echo '<li title="Seite-'.$i.'" '.$hidden.' class="switch-'.$sel.' '.$first.' normalswitch"><a href="'.$pagerUrl.'">'.$i.'</a></li>';
	          $i++;
	        }
	        if($pagerCount == 1){
	          $preDisabled = "disabled";
	        }
	        echo '<li class="'.(isset($preDisabled) ? $preDisabled : '').' page-after"><a href="#" rel="nofollow">»</a></li>';
	      echo '</ul>';

	      echo '<div class="fs-sort-box">';
	        echo '<select class="form-control" '.(isset($directOffer) ? $directOffer : '').'>';
	          $options = [
	            "0" => "Ohne Sortierung",
	            "sortprice-asc" => "Preis niedrigster zuerst",
	            "sortprice-desc" => "Preis höchster zuerst",
	            "sortkilometer-asc" => "KM-Stand, niedrigster zuerst",
	            "sortkilometer-desc" => "KM-Stand, höchster zuerst",
	            "sortleistung-asc" => "Leistung, niedrigste zuerst",
	            "sortleistung-desc" => "Leistung, höchste zuerst",
	            "sorttime-desc" => "Neu eingestellte zuerst"
	          ];
	          $selected = ((array_key_exists("sort", $_REQUEST) && $_REQUEST["sort"]) ? $_REQUEST["sort"] : "sorttime-desc");
	          foreach ($options as $value => $text) {
	            echo '<option value="'.$value.'" '.($selected == $value ? 'selected="selected"' : '').'>'.$text.'</option>';
	          }
	        echo '</select>';
	      echo '</div>';
	    echo '</div>';
#	  }

	  // Headline
#	  if ($this->getValue("article_id") != 1){
	  echo '<h1>Fahrzeugsuche</h1>';
#	  }

	  // Selektor

#	  if($this->getValue("article_id") != 1){
	    echo "<div class='row'>";
	      echo "<div class='col-md-3'>";
#	  }
	  ?>
	  <script src="./js/fs-functions.js" type="text/javascript"></script>
	  <div class="button filter-toggle-button">
	    <span class="button-text filter-toggle">Filter anzeigen</span>
	  </div>

	  <div id="fahrzeugsuche"<?php #if( $this->getValue("article_id") != 1 ){ echo ' class="small-search"'; } ?>>
	    <div class="col-md-12 fs-inner">
	      <span id="filter-reset" class="reset" style="display:none;">Filter zurücksetzten</span>
	      <form id="fs-form" action="<?php #echo ($this->getValue("article_id") != 1 ? strtok($_SERVER['REQUEST_URI'], '?') : "/fahrzeugverwaltung-uebersicht.html") ?>" method="POST" class="fs-form"> 
	        <div class="fs-start">
	          <fieldset> 
	            <label for="check1">
	              <?php #$checked1 = ((array_key_exists("neuwagen", $_POST) && $_POST["neuwagen"]) || (array_key_exists("neufahrzeug", $_GET) && $_GET["neufahrzeug"] == 1) || ($this->getValue("article_id") == 11 && (!array_key_exists("changed", $_POST) || !$_POST["changed"]) && (!array_key_exists("neufahrzeug", $_GET) || !$_GET["neufahrzeug"])) ? "checked=''" : ""); ?>
	              <input type="checkbox" name="neuwagen" value="1" id="check1" <?php #echo $checked1 ?> >
	              Neuwagen/Tageszulassung
	            </label> 
	            <label for="check2">
	              <?php #$checked2 = ((array_key_exists("gebrauchtwagen", $_POST) && $_POST["gebrauchtwagen"]) || (array_key_exists("neufahrzeug", $_GET) && $_GET["neufahrzeug"] == "3") || ($this->getValue("article_id") == 19 && (!array_key_exists("changed", $_POST) || !$_POST["changed"]) && (!array_key_exists("neufahrzeug", $_GET) || !$_GET["neufahrzeug"])) ? "checked=''" : ""); ?>
	               <input type="checkbox" name="gebrauchtwagen" value="1" id="check2" <?php #echo $checked2 ?> >
	               Gebrauchtwagen
	            </label> 
	          </fieldset> 
	          <label for="marke">
	            <select name="marke" id="marke">
	              <option value="0" disabled selected hidden>Marke</option>
	              <option value="0">Beliebig</option>
	                <?php $options = getOptionsInt("marke"); 
	                  foreach ($options as $option){
	                    $selected = (array_key_exists("marke", $_REQUEST) && $option[0] == $_REQUEST["marke"] ? "selected='selected'" : "");
	                    echo "<option value='".$option[0]."' " . $selected . ">".$option[0]."</option>";
	                  }
	                ?>
	            </select>
	          </label>
	          <label for="modell">
	            <select name="modell" id="modell" disabled="disabled">
	              <option value="0" disabled selected hidden>Modell</option>
	              <option value="0">Beliebig</option>
	            </select>
	          </label>
	          <label for="select3">          
	            <select name="kilometer" id="select3">
	              <option value="0" disabled selected hidden>Kilometer bis</option>
	              <option value="0">Beliebig</option>
	              <?php  
	                $options = array("<1.000", "5.000", "10.000", "20.000", "30.000", "40.000", "50.000", "60.000", "100.000");
	                foreach ($options as $option) {
	                  $selected = (array_key_exists("kilometer", $_REQUEST) && $option[0] == $_REQUEST["kilometer"] ? "selected='selected'" : "");
	                  echo "<option value='".$option."' ".$selected.">".$option."</option>";
	                }
	              ?>
	            </select>
	          </label>
	          <label for="kraftstoffart" class="last">  
	            <select name="kraftstoffart" id="kraftstoffart">
	              <option value="0" disabled selected hidden>Kraftstoffart</option>
	              <?php 

	              $options = array(
	                0 => "Beliebig",
	                1 => "Benzin",
	                2 => "Diesel",
	                4 => "Gas",
	                6 => "Elektro",
	                7 => "Hybrid",
	              );

	              foreach ($options as $optionKey => $optionVal) {
	                $selected = (array_key_exists("kraftstoffart", $_REQUEST) && $optionKey == $_REQUEST["kraftstoffart"] ? "selected='selected'" : "");
	                echo '<option value="' . $optionKey . '" ' . $selected . '>' . $optionVal . '</option>';
	              }
	              ?>
	            </select>
	          </label>
	          <label for="select5">
	            <select name="preis" id="select5">
	              <option value="0" disabled selected hidden>Preis bis</option>
	              <option value="0">Beliebig</option>
	              <?php  
	                $options = array("5.000", "8.000", "10.000", "15.000", "20.000", "30.000", "40.000", "50.000", "60.000");
	                foreach ($options as $option) {
	                  $selected = (array_key_exists("preis", $_REQUEST) && $option == $_REQUEST["preis"] ? "selected='selected'" : "");
	                  echo "<option value='".$option."' ".$selected.">".$option."</option>";
	                }
	              ?>
	            </select>
	          </label>
	          <label for="ez">
	            <select name="ez" id="ez">
	              <option value="0" disabled selected hidden>Erstzulassung ab</option>
	              <option value="0">Beliebig</option>
	              <?php  
	                $options = getOptionsInt("ez"); 
	                foreach ($options as $option){
	                  $selected = (array_key_exists("ez", $_REQUEST) && $option[0] == $_REQUEST["ez"] ? "selected='selected'" : "");
	                  echo "<option value='".$option[0]."' ".$selected.">".$option[0]."</option>";
	                }
	              ?>          
	            </select>
	          </label>
	          <label for="getriebeart">
	            <select name="getriebeart" id="getriebeart">
	              <option value="0" disabled selected hidden>Getriebe</option>
	              <option value="0">Beliebig</option>
	                <?php 
	                  $options = getOptionsInt("getriebeart"); 
	                  foreach ($options as $option){
	                    $selected = (array_key_exists("getriebeart", $_REQUEST) && $option[0] == $_REQUEST["getriebeart"] ? "selected='selected'" : "");
	                    echo "<option value='".$option[0]."' ".$selected.">".getGetriebeArt($option[0])."</option>";
	                  }
	                ?>
	            </select>
	          </label>
	          <label for="kategorie" class="last">          
	            <select name="kategorie" id="kategorie">
	              <option value="0" disabled selected hidden>Fahrzeugtyp</option>
	              <option value="0">Beliebig</option>
	                <?php 
	                  $options = getOptionsInt("kategorie"); 
	                  foreach ($options as $option){
	                    $selected = (array_key_exists("kategorie", $_REQUEST) && $option[0] == $_REQUEST["kategorie"] ? "selected='selected'" : "");
	                    echo "<option value='".$option[0]."' ".$selected.">".$option[0]."</option>";
	                  }
	                ?>
	            </select>
	          </label>
	          <label for="standort">          
	            <select name="standort" id="standort">
	              <option value="0" disabled selected hidden>Standort</option>
	              <option value="0">Beliebig</option>
	                <?php 
	                  $options = getOptionsInt("standort"); 
	                  $standortLabelMap = [
	                    492 => "Berliner Straße in Bietigheim",
	                    3188 => "Geisinger Straße in Bietigheim",
	                    3189 => "Marbacher Straße in Ludwigsburg",
	                    3190 => "Schorndorfer Straße in Ludwigsburg"
	                  ];
	                  foreach ($options as $option){
	                    if(isset($standortLabelMap[$option])){
	                      $selected = (array_key_exists("standort", $_REQUEST) && $option[0] == $_REQUEST["standort"] ? "selected='selected'" : "");
	                      echo "<option value='".$option[0]."' ".$selected.">".$standortLabelMap[$option[0]]."</option>";
	                    }                  
	                  }
	                ?>
	            </select>
	          </label>
	          <div class="col-md-12 fs-fulltext-block">            
	            <input placeholder="Freie Ausstattungssuche" type="text" name="fulltext" id="fulltext" class="fs-fulltext-searchfield" value="<?php echo (array_key_exists("fulltext", $_REQUEST) && $option == $_REQUEST["fulltext"] ? str_replace('|KOMMA|', ',', $_REQUEST['fulltext']) : '') ?>" />
	          </div>
	        </div>
 

	        
	        <div class="fs-end <?php #echo ($this->getValue("article_id") == 19 || $this->getValue("article_id") == 11 ? "double-search" : "") ?>">
	          <input id="fsearch-submit" rel="nofollow" class="button fsearch" type="submit" value="Suchen" />
	          <input id="fsearch-where" type="hidden" value="sent" name="action">
	          <input id="fsearch-changed" type="hidden" value="<?php echo (isset($changed) ? $changed : ''); ?>" name="changed">
	        </div>
	      </form>
	    </div>
	  </div>
	  <?php 
/*
	  if( $this->getValue("article_id") != 1 ){ ?>
	    <div class="fs-resell-btn">
	      <a target="_blank" href="<?php echo rex_getUrl(211, $REX['CUR_CLANG']); ?>" class="fs-resell-btn-text button">Wir kaufen Ihr Auto<span class="icon arrow"></span></a>
	    </div>
	  <?php } 
*/
/*	  ?>
	  <script type="text/javascript">changedItems = {};</script>

	  <?php
	  // Ausgabe Übersicht
	  if((array_key_exists("action", $_POST) && $_POST["action"]) || (array_key_exists("action", $_GET) && $_GET["action"]) || (array_key_exists("changed", $_POST) && $_POST["changed"]) || (!$_POST && (!array_key_exists("car_id", $_GET) || !$_GET["car_id"]) && $this->getValue("article_id") != 1) || (array_key_exists("fulltextIdentifier", $_POST) && $_POST["fulltextIdentifier"])){
	    if((!array_key_exists("fulltextIdentifier", $_POST) || !$_POST["fulltextIdentifier"])){ 
	      if(array_key_exists("action", $_GET) && $_GET["action"]){
	        if(array_key_exists("marke", $_REQUEST) && mysql_escape_string($_REQUEST["marke"]))
	          $partsChanged["marke"] = mysql_escape_string($_REQUEST["marke"]);
	        if(array_key_exists("modell", $_REQUEST) && mysql_escape_string($_REQUEST["modell"]))
	          $partsChanged["modell"] = (mysql_escape_string($_REQUEST["modell"]) ? mysql_escape_string($_REQUEST["modell"]) : "");
	        if((array_key_exists("neuwagen", $_REQUEST) && $_REQUEST["neuwagen"] == 1) && (array_key_exists("gebrauchtwagen", $_REQUEST) && $_REQUEST["gebrauchtwagen"] == 1)){
	          $partsChanged["neufahrzeug"] = "2";
	        }elseif((array_key_exists("neuwagen", $_REQUEST) && $_REQUEST["neuwagen"] == 1) || (array_key_exists("neufahrzeug", $_REQUEST) && mysql_escape_string($_REQUEST["neufahrzeug"]) == "1") || $this->getValue("article_id") == 11){
	          $partsChanged["neufahrzeug"] = "1";
	        }elseif((array_key_exists("gebrauchtwagen", $_REQUEST) && $_REQUEST["gebrauchtwagen"] == 1) || (array_key_exists("neufahrzeug", $_REQUEST) && mysql_escape_string($_REQUEST["neufahrzeug"]) == "3") || $this->getValue("article_id") == 19){
	          $partsChanged["neufahrzeug"] = "3";
	        }else{
	          $partsChanged["neufahrzeug"] = "2";
	        }
	        if(array_key_exists("neufahrzeug", $_REQUEST) && mysql_escape_string($_REQUEST["neufahrzeug"]))
	          $partsChanged["neufahrzeug"] = mysql_escape_string($_REQUEST["neufahrzeug"]);
	        if(array_key_exists("kraftstoffart", $_REQUEST) && mysql_escape_string($_REQUEST["kraftstoffart"]))
	          $partsChanged["kraftstoffart"] = mysql_escape_string($_REQUEST["kraftstoffart"]);
	        if(array_key_exists("kilometer", $_REQUEST) && mysql_escape_string($_REQUEST["kilometer"]))
	          $partsChanged["kilometer"] = mysql_escape_string($_REQUEST["kilometer"]);
	        if(array_key_exists("ez", $_REQUEST) && mysql_escape_string($_REQUEST["ez"]))
	          $partsChanged["ez"] = mysql_escape_string($_REQUEST["ez"]);
	        if(array_key_exists("getriebeart", $_REQUEST) && mysql_escape_string($_REQUEST["getriebeart"]))
	          $partsChanged["getriebeart"] = mysql_escape_string($_REQUEST["getriebeart"]);
	        if(array_key_exists("kategorie", $_REQUEST) && mysql_escape_string($_REQUEST["kategorie"]))
	          $partsChanged["kategorie"] = mysql_escape_string($_REQUEST["kategorie"]);
	        if(array_key_exists("preis", $_REQUEST) && mysql_escape_string($_REQUEST["preis"]))
	          $partsChanged["preis"] = mysql_escape_string($_REQUEST["preis"]);
	        if(array_key_exists("fulltext", $_REQUEST) && mysql_escape_string($_REQUEST["fulltext"]))
	          $partsChanged["fulltext"] = mysql_escape_string(str_replace(",", "|KOMMA|", $_REQUEST["fulltext"]));
	        if(array_key_exists("standort", $_REQUEST) && mysql_escape_string($_REQUEST["standort"]))
	          $partsChanged["standort"] = mysql_escape_string($_REQUEST["standort"]);
	        if(isset($partsChanged) && is_array($partsChanged)){
	          $changed = "";
	          foreach ($partsChanged as $partName => $partValue) {
	            if($partValue != ""){
	              $changed .= $partName."_".$partValue.",";
	            }
	          }
	        }
	      }elseif($this->getValue("article_id") == 11 || $this->getValue("article_id") == 19){
	        if($this->getValue("article_id") == 11)
	          $neufahrzeug = 1;
	        else
	          $neufahrzeug = 3;
	        $changed = "neufahrzeug_".$neufahrzeug.",".$_POST["changed"];
	        $whereRaw = getWhereRaw($changed);
	        $where = getWhere($whereRaw, "");
	      }elseif ($this->getValue("article_id") == 27) {
	        $changed = "prekategorie_('Transporter'),".$_POST["changed"];
	        $whereRaw = getWhereRaw($changed);
	        $where = getWhere($whereRaw, "");
	      }else{
	        $changed = (array_key_exists("changed", $_POST) ? $_POST["changed"] : '');
	      }
	      if($changed){  
	    ?>
	    <script type="text/javascript">
	      jQuery("#filter-reset").show();
	      changed = "<?php echo $changed; ?>";
	      preFillForm(changed);
	    </script>
	    <?php
	      }
	    }else{
	      if($this->getValue("article_id") == 11 || $this->getValue("article_id") == 19){
	        if($this->getValue("article_id") == 11){
	          $pageneufahrzeug = 1;
	        }elseif($this->getValue("article_id") == 19){
	          $pageneufahrzeug = 3;
	        }
	        $where = getFullTextWhereParameter($_POST["fulltext"], $pageneufahrzeug);
	      }else{
	        $where = getFullTextWhereParameter($_POST["fulltext"], false);
	      }
	    }
	  ?> 
	  </div>
	  <div class="col-md-9">
	  <?php

	  echo '<div class="article-list uebersicht fahrzeugverwaltung" '.(isset($directOffer) ? $directOffer : '').'>';
	    echo getResultsHtml($results, $page);
	  echo '</div>';
	  echo "<script type='text/javascript'>";
	  echo    "jQuery('.fs-overview-page').hide();";
	  echo "</script>";
	  if(array_key_exists("car_id", $_GET) && $_GET["car_id"]){
	    echo getOfferHtml($_GET["car_id"]);
	  }
	  $i = 1;

	  echo '<div class="fs-controller-box bottom">';
	    echo '<ul id="fs-pager-bottom" class="pagination notopmargin bottompager fs-pager" '.(isset($directOffer) ? $directOffer : '').'>';
	      echo '<li class="'.($page == 0 ? "disabled" : "").' page-before"><a class="" href="#" rel="nofollow">«</a></li>';
	        if(array_key_exists("page", $_GET) && $_GET["page"]){
	          $visiblePages = array();
	          for($arrpage=($_GET["page"]-2); $arrpage<=($_GET["page"]+2); $arrpage++) {
	            array_push($visiblePages, $arrpage);
	          }
	          if($_GET["page"] == 1 && $pagerCount >= 4)
	            array_push($visiblePages, 4);
	          if($_GET["page"] <= 2 && $pagerCount >= 5)
	            array_push($visiblePages, 5);
	          if($_GET["page"] >= ($pagerCount-1))
	            array_push($visiblePages, $pagerCount-4);
	          if($_GET["page"] == $pagerCount)
	            array_push($visiblePages, $pagerCount-3);
	        }
	        while ($i <= $pagerCount) {
	          $first = ($i == ($page+1) ? "selected" : "");
	          $sel = $i-1;     
	          if(array_key_exists("page", $_GET) && $_GET["page"]){
	            if(!in_array($i, $visiblePages))
	              $hidden = "style='display: none;'";
	            else
	              $hidden = "";
	          }else{
	            $hidden = ($i > 5 ? "style='display: none;'": "");
	          }
	          if(array_key_exists("page", $_GET) && $_GET["page"]){
	            $pagerUrl = str_replace("page=".$_GET["page"], "page=".$sel, $_SERVER["REQUEST_URI"]);
	          }else{
	            $pagerUrl = $_SERVER["REQUEST_URI"].'&page='.$sel;
	          }    
	          echo '<li title="Seite-'.$i.'" '.$hidden.' class="switch-'.$sel.' '.$first.' normalswitch"><a href="'.$pagerUrl.'">'.$i.'</a></li>';
	          $i++;
	        }
	        if($pagerCount == 1){
	          $preDisabled = "disabled";
	        }
	        echo '<li class="'.(isset($preDisabled) ? $preDisabled : '').' page-after"><a href="#" rel="nofollow">»</a></li>';
	    echo '</ul>';

	    echo '<div class="import-date">';
	          $datum = date("d.m.Y");
	          $path = './files/newcars/';

	            if ($handle = opendir($path)) {
	                
	                //Verzeichnis durchlauf 
	                while(false !== ($file = readdir($handle))) {
	                    if($file != "." && $file != "..") {

	                        $filelastmodified = date("d.m.Y", filemtime($path . $file));
	                          echo "<span> Datenbestand von: $filelastmodified </span>";
	                        
	                        break;
	                    }else{
	                        continue;
	                    }
	                }
	            }
	      echo "</div>";
	    echo '<div class="fs-sort-box">';
	      echo '<select class="form-control" '.(isset($directOffer) ? $directOffer : '').'>';
	        $options = [
	          "0" => "Ohne Sortierung",
	          "sortprice-asc" => "Preis niedrigster zuerst",
	          "sortprice-desc" => "Preis höchster zuerst",
	          "sortkilometer-asc" => "KM-Stand, niedrigster zuerst",
	          "sortkilometer-desc" => "KM-Stand, höchster zuerst",
	          "sortleistung-asc" => "Leistung, niedrigste zuerst",
	          "sortleistung-desc" => "Leistung, höchste zuerst",
	          "sorttime-desc" => "Neu eingestellte zuerst"
	        ];
	        $selected = ((array_key_exists("sort", $_REQUEST) && $_REQUEST["sort"]) ? $_REQUEST["sort"] : "sorttime-desc");
	        foreach ($options as $value => $text) {
	          echo '<option value="'.$value.'" '.($selected == $value ? 'selected="selected"' : '').'>'.$text.'</option>';
	        }
	      echo '</select>';
	    echo '</div>';
	  echo '</div>';
	  ?>
	  <script type="text/javascript">
	    var pageBefore;
	    maxpages = "<?php echo ($pagerCount-1); ?>";
	    where = "<?php echo $where; ?>";
	    amount = "<?php echo $amount; ?>";
	    pagerUse(maxpages, where, amount);
	    sortUse(where, amount);
	    backUrl = '<?php echo $backUrl; ?>';
	    switchToDetail(backUrl);
	    jQuery(window).on('popstate', function(e){
	      window.location.reload();
	    });
	  </script>
	  </div>
	  </div>
	  <?php } ?>
	  <script type="text/javascript">
	    totalOffers = "<?php echo $totalOffers; ?>";
	    <?php  
	      if((array_key_exists("gebrauchtwagen", $_POST) && $_POST["gebrauchtwagen"]) && (array_key_exists("neuwagen", $_POST) && $_POST["neuwagen"])){
	        echo "neuwagen = 1;";
	        echo "gebrauchtwagen = 1;";
	        echo "neufahrzeug = 2;";
	      }elseif((array_key_exists("gebrauchtwagen", $_POST) && $_POST["gebrauchtwagen"]) || ($this->getValue("article_id") == 19 && (!array_key_exists("changed", $_POST) || !$_POST["changed"]) && (!array_key_exists("action", $_GET) || !$_GET["action"])) || (array_key_exists("neufahrzeug", $_REQUEST) && $_REQUEST["neufahrzeug"] == "3")){
	        echo "neufahrzeug = 3;";
	        echo "neuwagen = 0;";
	        echo "gebrauchtwagen = 1;";
	      }elseif((array_key_exists("neuwagen", $_POST) && $_POST["neuwagen"]) || ($this->getValue("article_id") == 11 && (!array_key_exists("changed", $_POST) || !$_POST["changed"]) && (!array_key_exists("action", $_GET) || !$_GET["action"])) || (array_key_exists("neufahrzeug", $_REQUEST) && $_REQUEST["neufahrzeug"] == "1")){
	        echo "neufahrzeug = 1;";
	        echo "neuwagen = 1;";
	        echo "gebrauchtwagen = 0;";
	      }else{
	        echo "neufahrzeug = 0;";
	        echo "neuwagen = 0;";
	        echo "gebrauchtwagen = 0;";
	      }
	      if($this->getValue("article_id") == 11){
	        $neufahrzeug = 1;
	      }elseif($this->getValue("article_id") == 19){
	        $neufahrzeug = 0;
	      }else{
	        $neufahrzeug = false;
	      }
	    ?>
	    changed = "<?php echo (isset($changed) ? $changed : ''); ?>";
	    checkInForm(changed, totalOffers, neufahrzeug, neuwagen, gebrauchtwagen);
	  </script>
	  <?php if($this->getValue("article_id") != 1){ ?>
	    <script type="text/javascript">
	      jQuery(".filter-toggle").on('click', function(event) {
	        jQuery("#fahrzeugsuche").slideToggle("slow");
	        jQuery(this).toggleClass('active');
	        if(jQuery(this).text() === "Filter anzeigen"){
	          jQuery(this).text("Filter ausblenden");
	        }else{
	          jQuery(this).text("Filter anzeigen");
	        }
	      });
	    </script>
	  <?php } 
	}*/
}

add_shortcode('fahrzeugangebote', 'fahrzeugangebote');

function fahrzeugangebote()
{
/*	
	require_once(__dir__."/fahrzeugsuche/fs-functions.php"); 
	$i = 0;
	if($this->getValue("article_id") == 1){                     
	    $totalCount = getTotalCount();
	    $randomSelectedOffers = range(1, $totalCount);
	    shuffle($randomSelectedOffers);
	    array_splice($randomSelectedOffers, 200, $totalCount);
	}else{
	    $randomSelectedOffers = array();
	    $art = OOArticle::getArticleById("REX_ARTICLE_ID");
	    $parent = $art->getParent();
	    $modell = $art->getName();
	    $marke = str_replace(array("Fiat Professional"), array("Fiat"), $parent->getName());
	    //$sql = rex_sql::factory(); //ersetzt durch $wpdb
	    $query = "SELECT 
	                `satz_nummer`
	            FROM `new_mobile_de`
	            WHERE `marke` = '".$marke."' 
	            AND `modell` LIKE '%".$modell."%' 
	            ORDER BY RAND() 
	            LIMIT 10";
	  	$results = mysqli_query($wpdb, $query); //$sql->setQuery($query);
	    *//*for($b=0; $b<$sql->getRows(); $b++){
	        array_push($randomSelectedOffers, $sql->getValue("satz_nummer"));
	        $sql->next();
	    }*//*
	  	while($rows = mysqli_fetch_array($results, MYSQLI_NUM)){
	  		$randomSelectedOffers[] = $rows;
	  	}

	    shuffle($randomSelectedOffers);
	}

	if(count($randomSelectedOffers) > 0){
	?>
	    <div id="adt">
	        <div id="oc-clients-full" class="owl-carousel">
	        <?php
	            $angebotData = rex_sql::factory();
	            $verwaltungUrl = OOArticle::getArticleById(234)->getUrl();
	            /**
	            *  Angepasster Select nach günstigstem preis pro Modell
	            */
/*
	            if(strstr($_SERVER["REQUEST_URI"], '/neuwagen') == true){
	               $angebotData->setQuery("SELECT * 
	                        FROM `new_mobile_de`
	                        WHERE `satz_nummer` IN (".implode(',', $randomSelectedOffers).")
	                        LIMIT 10");
	            }else{
	         $angebotData->setQuery("SELECT * 
	                                 FROM new_mobile_de ct 
	                                    JOIN (
	                                            SELECT satz_nummer, kategorie, marke, MIN(preis) as preis
	                                            FROM new_mobile_de
	                                            GROUP BY marke, kategorie
	                                        ) ctp
	                                    ON ct.kategorie = ctp.kategorie AND ct.marke = ctp.marke AND ct.preis = ctp.preis
	                                 ORDER BY RAND()
	                                    LIMIT 10");

	            }
	            
	               #Ursprünglicher Select:
	            
	            for($angebotCounter=0; $angebotCounter<$angebotData->getRows(); $angebotCounter++){
	                # Laden der Artikeldaten 
	                
	                $label = $angebotData->getValue("marke");
	                $label = $label[0].strtolower(substr($label, 1));
	                $image = "/files/newcars/".$angebotData->getValue("interne_nummer")."_01.jpg";


	                // #Filter Image by Size(Only Show Car Pictures in Slider)
	                // if(filesize("/home/www/p429803/html".$image) == 39031 || filesize("/home/www/p429803/html".$image) == 160413 || filesize("/home/www/p429803/html".$image) == 132457 || filesize("/home/www/p429803/html".$image) == 39381 || filesize("/home/www/p429803/html".$image) == 136087 || filesize("/home/www/p429803/html".$image) == 133106 || filesize("/home/www/p429803/html".$image) == 38844 || filesize("/home/www/p429803/html".$image) == 160413 || filesize("/home/www/p429803/html".$image) == 132219 || filesize("/home/www/p429803/html".$image) == 132983 || filesize("/home/www/p429803/html".$image) == 39382 || filesize("/home/www/p429803/html".$image) == 136087 || filesize("/home/www/p429803/html".$image) == 39013){
	                //  continue;
	                
	                // }


	                // if($_SERVER["REMOTE_ADDR"] == "217.86.140.30") {

	                //     echo filesize("/home/www/p429803/html".$image) . ' Byte';
	                // }


	                if(!file_exists("/home/www/p429803/html".$image)){
	                    $image = "/files/platzhalter.jpg";
	                }

	                    $image = "index.php?rex_img_type=weller_home_offers&rex_img_file=" . str_replace("/files/", "", $image);                    
	                #}

	                $modell = $angebotData->getValue("modell");
	                $shortmodell = explode(" ", $modell);
	                $shortmodell = $shortmodell[0];
	                $shortmodellStrL = strlen($shortmodell);
	                $longmodell = substr($modell, ($shortmodellStrL+1));
	                $fullname = $label.' '.$shortmodell;
	                # Auswählen des passenden img Tags
	                switch ($label) {
	                    case 'Volvo':
	                        $AngebotIcon = '<img class="angebot-'.$label.'-icon" src="img/volvo.png" alt="'.$label.'">';
	                        break;           
	                    case 'Opel':
	                        $AngebotIcon = '<img class="angebot-'.$label.'-icon" src="img/opel.svg" alt="'.$label.'">';
	                        break;
	                    case 'Hyundai':
	                        $AngebotIcon = '<img class="angebot-'.$label.'-icon" src="img/hyundai.svg" alt="'.$label.'">';
	                        break;
	                    case 'Toyota':
	                        $AngebotIcon = '<img class="angebot-'.$label.'-icon" src="img/toyota.svg" alt="'.$label.'">';
	                        break;
	                    case 'Ford':
	                        $AngebotIcon = '<img class="angebot-'.$label.'-icon" src="img/ford.svg" alt="'.$label.'">';
	                        break;
	                }
	                if ($angebotData->getValue("neufahrzeug") == 1) {
	                    $isUsed = "Neuwagen";
	                }elseif ($angebotData->getValue("tageszulassung") == 1) {
	                    $isUsed = "Tageszulassung";
	                }elseif ($angebotData->getValue("vorfuehrfahrzeug") == 1) {
	                    $isUsed = "Vorführfahrzeug";
	                }else{
	                    $isUsed = "Gebrauchtwagen";
	                }
	                $ez = $angebotData->getValue("ez");
	                $ez = explode(".", $ez);
	                $months = array("0", "Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember");
	                $textMonth = $months[((int)$ez[0])];
	                $ez = $textMonth.' '.$ez[1];
	                if($angebotData->getValue('mwstsatz') == '19')
	                    $price = round((1.19 * str_replace(",", ".", $angebotData->getValue('preis'))), 0);
	                else $price = round(str_replace(",", ".", $angebotData->getValue('preis')), 0);
	                $price = number_format($price, 2, ",", ".");
	                $carPriceSplit = explode(",", $price);
	                $carPriceDecimals = $carPriceSplit[1];
	                if($carPriceDecimals == "00"){
	                    $carPriceDecimals = ",-";
	                    $carPrice = $carPriceSplit[0].$carPriceDecimals;
	                }else{
	                    $carPrice = $price;
	                }

	                ?>
	   

	                <div class="angebot-box">
	                    <span class="angebot-pfeilpreis"><?php echo $carPrice.' €'; ?></span>
	                    <a href="<?php echo $verwaltungUrl.'?action=prefill'.'&marke='.$label.'&modell='.$shortmodell.'%&neufahrzeug='.str_replace(0, 3, $angebotData->getValue("neufahrzeug")).'&car_id='.$angebotData->getValue("satz_nummer"); ?>">
	                        <img alt="<?php echo $fullname; ?>" src="<?php echo $image; ?>" data-original="<?php echo $image; ?>">
	                    </a>
	                    <div class="angebot-info">
	                        <div class="angebot-namebox">              
	                            <?php echo $AngebotIcon; ?>
	                            <a href="<?php echo $verwaltungUrl.'?action=prefill'.'&marke='.$label.'&modell='.$shortmodell.'%&neufahrzeug='.str_replace(0, 3, $angebotData->getValue("neufahrzeug")).'&car_id='.$angebotData->getValue("satz_nummer"); ?>" class="angebot-button">
	                            <span class='angebot-name'><?php echo $label." ".$modell; ?></span>  
	                            </a>          
	                        </div>
	                        <div class="angebot-beschreibungbox row">
	                            <div class="beschreibung-item">
	                                <i class="angebot-clock-icon"></i>
	                                <span class="beschreibung-text"><?php echo $isUsed; ?></span>
	                            </div>
	                            <div class="beschreibung-item">
	                                <i class="angebot-street-icon"></i>
	                                <span class="beschreibung-text"><?php echo ($isUsed == "Neuwagen" ? "0" :number_format($angebotData->getValue("kilometer"), 0, "", "."))." km" ; ?></span>
	                            </div>
	                            <?php if(!$isUsed){ ?>
	                                <div class="beschreibung-item">
	                                    <i class="angebot-calendar-icon"></i>
	                                    <span class="beschreibung-text"><?php echo $ez; ?></span>
	                                </div>
	                            <?php } ?>
	                            <div class="beschreibung-item">
	                                <i class="angebot-speedometer-icon"></i>
	                                <span class="beschreibung-text"><?php echo $angebotData->getValue("leistung")." kW (".round(($angebotData->getValue("leistung")*1.35962))." PS)"; ?></span>
	                            </div>
	                            <div class="beschreibung-item">
	                                <i class="angebot-fuel-icon"></i>
	                                <span class="beschreibung-text"><?php echo getKraftstoffArt($angebotData->getValue("kraftstoffart")); ?></span>
	                            </div>
	                            <div class="beschreibung-item">
	                                <i class="angebot-engine-icon"></i>
	                                <span class="beschreibung-text"><?php echo getGetriebeArt($angebotData->getValue("getriebeart")); ?></span>
	                            </div>
	                            <div class="beschreibung-item">
	                                <i class="angebot-calendar-icon"></i>
	                                <span class="beschreibung-text"><?php echo $angebotData->getValue("ez") ?></span>
	                            </div>
	                        </div>
	                        <div class="angebot-enkv">
	                            <div class="enkv">
	                                <?php 
	                                    echo "<div class='enkv-box'>";
	                                        echo "<span class='offer-envkv-label'>Kraftstoffverbr. <span>(komb./innerorts/außerorts)</span>:</span><br />";
	                                        echo "<span class='offer-envkv-value'> ".$angebotData->getValue("verbrauch_kombiniert")." l/100km / ".$angebotData->getValue("verbrauch_innerorts")." l/100km / ".$angebotData->getValue("verbrauch_ausserorts")." l/100km</span>";
	                                    echo "</div>";
	                                    echo "<div class='enkv-box'>";
	                                        echo "<span class='offer-envkv-label'>CO2-Emissionen kombiniert: </span>";
	                                        echo "<span class='offer-envkv-value'>".$angebotData->getValue("emission")." g/km</span>";
	                                    echo "</div>";
	                                ?>
	                            </div>
	                        </div>
	                        <div class="angebot-buttonbox">
	                            <a href="<?php echo $verwaltungUrl.'?action=prefill'.'&marke='.$label.'&modell='.$shortmodell.'%&neufahrzeug='.str_replace(0, 3, $angebotData->getValue("neufahrzeug")).'&car_id='.$angebotData->getValue("satz_nummer"); ?>" class="angebot-button">Zum Angebot<span class="icon arrow"></span></a>
	                        </div>
	                    </div>
	                </div>
	                <?php $angebotData->next(); ?>
	            <?php } ?>
	        </div>
	    </div>

	<?php

	?>

	    <script type="text/javascript">

	        jQuery(document).ready(function($) {

	            var ocClients = $("#oc-clients-full");

	            ocClients.owlCarousel({
	                items: 4,
	                margin: 30,
	                loop: true,
	                nav: true,
	                autoplay: true,
	                dots: false,
	                autoplayHoverPause: true,
	                responsive:{
	                    0:{ items:1 },
	                    480:{ items:2 },
	                    768:{ items:3 },
	                    992:{ items:4 },
	                }
	            });

	        });

	    </script>
	<?php 
    } 
*/    
}

add_shortcode('fahrzeugdetails', 'fahrzeugdetails');

function fahrzeugdetails()
{
	require_once(__dir__."/fahrzeugsuche/fs-functions.php");
	if ($_GET['car_id'])
	{
		return getOfferHtml($_GET['car_id']);
	}
}