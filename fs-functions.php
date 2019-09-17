<?php

function getWhereRaw($changed){
	include(__dir__.'/db-access.php');
	$con = mysqli_connect($DB['SERVER'],$DB['USER'],$DB['PW'],$DB['TABLE']);
	$changed = explode(",", $changed);
	array_pop($changed);
	$whereParameterArr = [];
	foreach ($changed as $change) {
		$change = explode("_", $change);
		if($change[0] == "resetModell"){
			if(array_key_exists("modell", $whereParameterArr))
				unset($whereParameterArr["modell"]);
			continue;
		}
		$SqlMethod = getSqlMethodByValue($change[0]);
		if(array_key_exists(1, $change) && $change[1] != "0"){
			if($change[0] == "ez"){
				$Query = "SELECT satz_nummer, ez FROM new_mobile_de";
				$result = mysqli_query($con, $Query);
				$ezIds = "";
				while ($rows = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
					$year = explode(".", $rows["ez"]);
			  		if($year[1] >= $change[1]){
			  			$ezIds .= $rows["satz_nummer"].', ';
			  		}
			  	}
			  	$ezIds = substr($ezIds, 0, -2);
			  	$whereParameter = "satz_nummer IN(".$ezIds.")";
			}elseif($change[0] == "neufahrzeug"){
				if($change[1] != 2){
					switch ($change[1]) {
						case 1:
							$value = 1;							
							break;
						
						case 3:
							$value = 0;
							break;
					}
					if($value == 0){
						$whereParameter = "( ".$change[0]." ".$SqlMethod." '".$value."' AND ";
						$whereParameter .= "`tageszulassung` = 0 AND ";
						$whereParameter .= "`vorfuehrfahrzeug` = 0 )";
					}else{
						$whereParameter = "( ".$change[0]." ".$SqlMethod." '".$value."' OR ";
						$whereParameter .= "`tageszulassung` = 1 OR ";
						$whereParameter .= "`vorfuehrfahrzeug` = 1 )";
					}				
				}else{
					if(array_key_exists($change[0], $whereParameterArr))
						unset($whereParameterArr[$change[0]]);
					continue;
				}
			}elseif($change[0] == "kilometer"){
			  	$whereParameter = $change[0]." ".$SqlMethod." ".str_replace(".", "", $change[1]);
			}elseif ($change[0] == "preis") {
				$whereParameter = "((mwstsatz = 19 AND (".$change[0]."*1.19) ".$SqlMethod." ".str_replace(".", "", $change[1]).") OR (mwstsatz LIKE '' AND ".$change[0]." ".$SqlMethod." ".str_replace(".", "", $change[1])."))";
			}elseif($SqlMethod == "IN"){
				$whereParameter = "kategorie ".$SqlMethod." ".str_replace(array(";", "\\"), array(",", ""), $change[1]);	 	
			}elseif($change[0] == "fulltext"){
				$change[1] = str_replace("|KOMMA|", ",", $change[1]);
				$whereParameter = getFullTextWhereRaw($change[1]);	 	
			}else{
			  	$whereParameter = $change[0]." ".$SqlMethod." '".$change[1]."'";			  	
			}
			$whereParameterArr[$change[0]] = $whereParameter;
		}else{
			if(array_key_exists($change[0], $whereParameterArr))
				unset($whereParameterArr[$change[0]]);
		}
	}
	if(isset($whereParameterArr) && is_array($whereParameterArr) && count($whereParameterArr) > 0){
		return $whereParameterArr;
	}else{
		return [];
	}
}

function getWhere($whereRaw, $skip){
	if($whereRaw){
		$where = "WHERE ";
		$i = 0;
		foreach ($whereRaw as $whereHead => $whereParameter) {
			if($whereHead != $skip){
				if($skip != "marke" || ($whereHead != "modell")){
					if($i != 0)
						$where .= " AND ";
					$where .= $whereParameter;
					$i++;
				}
			}
		}
	}
	if(isset($where) && $where == "WHERE " || !isset($where)){
		return "";
	}else{
		return $where;
	}
}

function getOptionsExt($key, $where){
  	include(__dir__.'/db-access.php');
	$con = mysqli_connect($DB['SERVER'],$DB['USER'],$DB['PW'],$DB['TABLE']);
  	$Query = "SELECT ".$key."
  	 		 FROM `new_mobile_de` ".
  	 		 $where. 
  	 		 " GROUP BY ".$key.
  	 		 " ORDER BY ".$key." ASC";
  	$results = mysqli_query($con, $Query);
  	$options = array();
  	while($rows = mysqli_fetch_array($results, MYSQLI_ASSOC)){
  		if($key == "ez"){
  			$DateOption = explode(".", $rows[$key]);
  			if(array_key_exists(1, $DateOption) && $DateOption[1] != "")
	    		$options[$DateOption[1]] = $DateOption[1];
	    	ksort($options);
	    }elseif($key == "modell"){
	    	$modell = explode(" ", $rows[$key]);
	    	if($modell[0] != ""){
	    		if($modell[0] != "XC" && $modell[0] != "New"){
	    			$options[$modell[0]] = $modell[0];
	    		}else{
	    			//2016-05-25 JW: Anpassung da bei Suche nach XC-Modellen nichts gefunden wurde
	    			//$options[$modell[0].$modell[1]] = $modell[0] . $modell[1];
	    			$options[$modell[0].$modell[1]] = $modell[0] . " " .$modell[1];
	    		}
	    	}
  		}elseif($key == "neufahrzeug"){
	    	if($rows["neufahrzeug"] == 1 || $rows["tageszulassung"] == 1 || $rows["vorfuehrfahrzeug"] == 1)
	    		array_push($options, 1);
	    	elseif ($rows["neufahrzeug"] == 0 && $rows["tageszulassung"] == 0 && $rows["vorfuehrfahrzeug"] == 0) {
	    		array_push($options, 0);
	    	}
  		}else{
	    	if(array_key_exists($key, $rows) && $rows[$key] != "")
	    		array_push($options, $rows[$key]);
		}
  	}
  	return $options;
}

# Überarbeiten
function getOptionsInt($key){
	include(__dir__.'/db-access.php');
	$con = mysqli_connect($DB['SERVER'],$DB['USER'],$DB['PW'],$DB['TABLE']);
  	$Query = "SELECT ".$key." 
  					FROM `new_mobile_de` 
  					WHERE ".$key." != '' 
  					GROUP BY ".$key." 
  					ORDER BY ".$key." ASC";
  	$return = array();
  	$results = mysqli_query($con, $Query);

  	while($rows = mysqli_fetch_array($results, MYSQLI_NUM)){
  		$return[] = $rows;
  	}
  	return $return;
}

function getHtmlOptions($key, $options, $whereRaw){
	$HtmlOptions = array();
	$i = 0;
	foreach ($options as $option) {
		$i++;
		$value = $option;
		if($key == "modell"){
			$value .= "%";			
			$data = explode("=", $whereRaw["marke"]);
			$data = str_replace("'", "", $data[1]);
			$data = trim($data);
			$data = " data-mark='".$data."'";
		}
		if($key == "kraftstoffart"){
			$OptionText = getKraftstoffArt($option);
		}elseif($key == "getriebeart"){
			$OptionText = getGetriebeArt($option);
		}else{
			$OptionText = $option;
		}
		$HtmlOption = "<option".(isset($data) ? $data : '')." value='".$value."'>".$OptionText."</option>";
		array_push($HtmlOptions, $HtmlOption);
	}
	return $HtmlOptions;
}

function getCarCount($where){
	include(__dir__.'/db-access.php');
	$con = mysqli_connect($DB['SERVER'],$DB['USER'],$DB['PW'],$DB['TABLE']);
  	$Query = "SELECT satz_nummer FROM `new_mobile_de` ".$where;
  	$results = mysqli_query($con, $Query);
  	$i = 0;
  	while($rows = mysqli_fetch_array($results, MYSQLI_NUM)){
  		$i++;
  	}
  	$CarCount = $i;
  	return $CarCount;
}

function getSqlMethodByValue($change){
	switch ($change) {
		case 'marke':
		case 'neufahrzeug':
		case 'kraftstoffart':
		case 'getriebeart':
		case 'kategorie':
		case 'standort':
			$SqlMethod = "=";
			break;
		case 'ez':
		case 'kilometer':
		case 'preis':
			$SqlMethod = "<=";
			break;
		case 'modell':
			$SqlMethod = "LIKE";
			break;
		case 'prekategorie':
			$SqlMethod = "IN";
			break;
	}
	return $SqlMethod;
}
function getFullTextWhereParameter($fulltext, $neufahrzeug){
	include(__dir__.'/db-access.php');
	$con = mysqli_connect($DB['SERVER'],$DB['USER'],$DB['PW'],$DB['TABLE']);
	mysqli_set_charset ($con , "utf8");
	$textParts = explode(" ", trim($fulltext));
	$query = "SELECT `satz_nummer` FROM `new_mobile_de_suchindex` ";
	$i = 0;
	$results = array();
	foreach ($textParts as $textPart) {
		$i++;
		$textPart = mysqli_real_escape_string($con, $textPart);
		if($i == 1){
			$query .= "WHERE";
		}else{
			$query .= " AND";
		}
		$query .= " `suchspalte` LIKE '%".$textPart."%'";
	}
	$temp = mysqli_query($con, $query);
	while($rows = mysqli_fetch_array($temp, MYSQLI_ASSOC)) {
		array_push($results, $rows["satz_nummer"]);
	}
	if(count($results) > 0){
		$resultquery = "WHERE `satz_nummer` IN (";
		foreach ($results as $result) 
			$resultquery .= $result.",";
		$resultquery = substr($resultquery, 0, -1);
		$resultquery .= ")";
		if($neufahrzeug){
			if($neufahrzeug == 3){
				$resultquery .= " AND (`neufahrzeug` = 0 AND ";
				$resultquery .= "`tageszulassung` = 0 AND ";
				$resultquery .= "`vorfuehrfahrzeug` = 0 )";
			}elseif($neufahrzeug == 1) {
				$resultquery .= " AND (`neufahrzeug` = 1 OR ";
				$resultquery .= "`tageszulassung` = 1 OR ";
				$resultquery .= "`vorfuehrfahrzeug` = 1 )";
			}
		}
	}else{
		$resultquery = "WHERE 0";
	}
	return $resultquery;
}
function getFullTextWhereRaw($fulltext){
	include(__dir__.'/db-access.php');
	$con = mysqli_connect($DB['SERVER'],$DB['USER'],$DB['PW'],$DB['TABLE']);
	mysqli_set_charset ($con , "utf8");
	$textParts = explode(", ", trim($fulltext));
	foreach ($textParts as $key => $textPart) {
		$result = explode(",", trim($textPart));
		unset($textParts[$key]);
		$textParts = array_merge($result, $textParts);
	}
	foreach ($textParts as $key => $textPart) {
		$result = explode(" ", trim($textPart));
		unset($textParts[$key]);
		$textParts = array_merge($result, $textParts);
	}
	$query = "SELECT `satz_nummer` FROM `new_mobile_de_suchindex` ";
	$i = 0;
	$results = array();
	foreach ($textParts as $textPart) {
		$i++;
		$textPart = mysqli_real_escape_string($con, $textPart);
		if($i == 1){
			$query .= "WHERE";
		}else{
			$query .= " AND";
		}
		$query .= " `suchspalte` LIKE '%".$textPart."%'";
	}
	$temp = mysqli_query($con, $query);
	while($rows = mysqli_fetch_array($temp, MYSQLI_ASSOC)) {
		array_push($results, $rows["satz_nummer"]);
	}
	if(count($results) > 0){
		$resultquery = "`satz_nummer` IN (";
		foreach ($results as $result) 
			$resultquery .= $result.",";
		$resultquery = substr($resultquery, 0, -1);
		$resultquery .= ")";
	}else{
		$resultquery = "0";
	}	
	return $resultquery;
}

function getSortParameter($sort){
	$temp = explode("-", $sort);
	$column = $temp[0];
	$direction = strtoupper($temp[1]);
	$sortParameter = "ORDER BY ".$column." ".$direction." ";

	return $sortParameter;
}

function getResults($where, $page, $amount, $sort = ""){
	include(__dir__.'/db-access.php');
	$con = mysqli_connect($DB['SERVER'],$DB['USER'],$DB['PW'],$DB['TABLE']);
	mysqli_set_charset ($con , "utf8");
	$where = str_replace("\\", "", $where);
	$offset = $page*$amount;

	if($sort != ""){
		$sort = getSortParameter($sort);
	}

	$query = "SELECT * FROM `new_mobile_de` ".$where." ".$sort."LIMIT ".$offset." , ".$amount;

	$temp = mysqli_query($con, $query);
	$results = array();
	while($rows = mysqli_fetch_array($temp, MYSQLI_ASSOC)){
		array_push($results, $rows);
	}
	return $results;
}
function getGetriebeArt($typId) {
    $return = '';

    $getriebeList = array(
      "1" => "Schaltgetriebe",
      "2" => "Halbautomatik",
      "3" => "Automatik"
    );

    if(!empty($getriebeList[$typId])) {
      $return = $getriebeList[$typId];
    }
    return $return;
}
function getKraftstoffArt($typId) {
    $return = '';

    $kraftstoffList = array(
      "1" => "Benzin",
      "2" => "Diesel",
      "3" => "Autogas",
      "4" => "Erdgas",
      "6" => "Elektro",
      "7" => "Hybrid",
      "8" => "Wasserstoff",
      "9" => "Ethanol",
      "10" => "Hybrid-Diesel"
    );

    if(!empty($kraftstoffList[$typId])) {
      $return = $kraftstoffList[$typId];
    }
    return $return;
}
function getLabelIcon($label){	
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
            $AngebotIcon = '<img class="angebot-'.$label.'#icon" src="img/toyota.svg" alt="'.$label.'">';
            break;
        case 'Ford':
            $AngebotIcon = '<img class="angebot-'.$label.'#icon" src="img/ford.svg" alt="'.$label.'">';
            break;
        default:
        	$AngebotIcon = '';
    }
    return $AngebotIcon;
}
function getFrontImage($bildId) {
    $bildId .= '_';
    $imgList = array();
    $files = glob('files/newcars/*');
    foreach($files as $file){
      if(is_file($file) && substr(basename($file), 0, strlen($bildId)) == $bildId) {
        $imgList[] = basename($file);
      }
    }
    return $imgList;
}
function getResultsHtml($results, $pageId){
  	if(session_status() != PHP_SESSION_ACTIVE)
		session_start();
	$resultsHtml = "";
	$i = 0;
	$resultsHtml .= "<div class='fs-overview-page' id='page-".$pageId."'>";
	foreach ($results as $result) {
	    $i++;
	    $col_last = ($i%2 ? "" : "col_last");
	    $modell = $result["modell"];
	    $label = $result["marke"][0].strtolower(substr($result["marke"], 1));
	    $carName = $label." ".$modell;
	    $labelIcon = getLabelIcon($label);
	    $carDesc = str_replace("*", "", $result["bemerkung"]);
	    $carDesc = str_replace("\\", "", $carDesc);
	    $carDesc = substr($carDesc, 0, 150);
	    //if($_SERVER["REMOTE_ADDR"] == "217.86.140.30") {
	    	if($result['mwstsatz']=='19') {
	    		$carPrice = number_format(round(str_replace(",", ".", $result["preis"]) * 1.19, 0), 2, ",", ".");
	    	} else {
	    		$carPrice = number_format(str_replace(",", ".", $result["preis"]), 2, ",", ".");
	    	}
	    //} else {


	    //if($_SERVER['REMOTE_ADDR']=='217.92.106.129') {
	    	/*
	    	if($result['mwstsatz']=='19') {
	    		$carPrice = number_format((str_replace(",", ".", $result["preis"]) * 1.19), 2, ",", ".");
	    	} else {
	    		$carPrice = number_format(str_replace(",", ".", $result["preis"]), 2, ",", ".");
	    	}
	    	*/
	    /*} else {
	    	$carPrice = number_format(($result["preis"] * 1.19), 2, ",", ".");
	    }*/
	    //}

	    $carPriceSplit = explode(",", $carPrice);
	    $carPriceDecimals = $carPriceSplit[1];
	    if($carPriceDecimals == "00"){
	    	$carPriceDecimals = ",-";
	    	$carPrice = $carPriceSplit[0].$carPriceDecimals;
	    }
	    $kraftstoffart = getKraftstoffArt($result["kraftstoffart"]);

		    $resultsHtml .= "<div class='row car ".$col_last."'>";

			    $resultsHtml .= "<div class='car-inner'>";

			      	$resultsHtml .= "<div class='col-md-4'>";
			          	$resultsHtml .= '<a data-carId="'.$result["satz_nummer"].'" title="'.$carName.'" href="'.$_SESSION["backUrl"].'&car_id='.$result["satz_nummer"].'">';
			            	$resultsHtml .= "<div class='car-image'>";
			              		$resultsHtml .= '<img alt="'.$carName.'" src="/files/newcars/'.$result["interne_nummer"].'_01.jpg">';
			            	$resultsHtml .= "</div>";
			          	$resultsHtml .= '</a>';
			     	$resultsHtml .= "</div>";
				
		      		$resultsHtml .= "<div class='col-md-8'>";
		        		$resultsHtml .= '<div class="fbox-desc">';
		          			$resultsHtml .= '<a data-carId="'.$result["satz_nummer"].'" href="'.$_SESSION["backUrl"].'&car_id='.$result["satz_nummer"].'" class="car-descbox">';
		           				$resultsHtml .= "<h3 class='car-name'>".$carName."</h3>";
					            $resultsHtml .= "<p class='car-desc'>".$carDesc."...</p>";

							$resultsHtml .= "<div class='row'>";

								$resultsHtml .= "<div class='col-md-9 no-pad'>";

								    $resultsHtml .= "<div class='car-details'>";
									$resultsHtml .= "<div class='col-md-6'>";
										$resultsHtml .= "<div class='car-detail-box'>";
											$resultsHtml .= "<span class='car-detail-label'>Farbe:</span>";
											$resultsHtml .= "<span class='car-detail-value'> ".$result["farbe"]."</span>";
										$resultsHtml .= "</div>";
										$resultsHtml .= "<div class='car-detail-box'>";
											$resultsHtml .= "<span class='car-detail-label'>Treibstoff:</span>";
											$resultsHtml .= "<span class='car-detail-value'> ".$kraftstoffart."</span>";
										$resultsHtml .= "</div>";
										$resultsHtml .= "<div class='car-detail-box'>";
											$resultsHtml .= "<span class='car-detail-label'>Erstzulassung:</span>";
											$resultsHtml .= "<span class='car-detail-value'> ".$result["ez"]."</span>";
										$resultsHtml .= "</div>";
									    $resultsHtml .= "</div>";
									    $resultsHtml .= "<div class='col-md-6'>";
										$resultsHtml .= "<div class='car-detail-box'>";
											$resultsHtml .= "<span class='car-detail-label'>Leistung:</span>";
											$resultsHtml .= "<span class='car-detail-value'> ".$result["leistung"]." kW / ".round($result["leistung"]*1.35962)." PS</span>";
										$resultsHtml .= "</div>";
										$resultsHtml .= "<div class='car-detail-box'>";
											$resultsHtml .= "<span class='car-detail-label'>km-Stand:</span>";
											$resultsHtml .= "<span class='car-detail-value'> ".$result["kilometer"]." km</span>";
										$resultsHtml .= "</div>";
									$resultsHtml .= "</div>";
								    $resultsHtml .= "</div>";

								    $resultsHtml .= "<div class='car-envkv'>";
									$resultsHtml .= "<div class='car-detail-box'>";
										$resultsHtml .= "<span class='car-envkv-label'>Kraftstoffverbrauch: <span><br />(komb./innerorts/außerorts)</span>:</span>";
										$resultsHtml .= "<span class='car-envkv-value'> ".$result["verbrauch_kombiniert"]." l/100km / ".$result["verbrauch_innerorts"]." l/100km / ".$result["verbrauch_ausserorts"]." l/100km</span>";
									$resultsHtml .= "</div>";
									$resultsHtml .= "<div class='car-detail-box'>";
										$resultsHtml .= "<span class='car-envkv-label'>CO2-Emissionen kombiniert: </span>";
										$resultsHtml .= "<span class='car-envkv-value'>".$result["emission"]." g/km</span>";
									$resultsHtml .= "</div>";
								    $resultsHtml .= "</div>";

								$resultsHtml .= "</div>";

								$resultsHtml .= "<div class='col-md-3 no-pad'>";

								    $resultsHtml .= "<div class='button car-price'>";
									$resultsHtml .= "<span class='car-price-label'>Unser Hauspreis:</span>";
									$resultsHtml .= "<span class='car-price-value'> ".$carPrice." &euro;</span>";
									$resultsHtml .= "<span class='car-price-link'> zum Angebot</span>";
								    $resultsHtml .= "</div>";

								$resultsHtml .= "</div>";

							$resultsHtml .= "</div>";

				          	$resultsHtml .= '</a>';
				        $resultsHtml .= "</div>";
				    $resultsHtml .= "</div>";
				$resultsHtml .= "</div>";

			$resultsHtml .= "</div>";
  	}

  	$resultsHtml .= "</div>";
  	return $resultsHtml;
}
function getTotalCount(){
	include(__dir__.'/db-access.php');
	$con = mysqli_connect($DB['SERVER'],$DB['USER'],$DB['PW'],$DB['TABLE']);
	mysqli_set_charset ($con , "utf8");
	$query = "SELECT `satz_nummer` FROM `new_mobile_de`";
	$temp = mysqli_query($con, $query);
	$count = mysqli_num_rows($temp);
	return $count;
}
function getPagerCount($where, $amount){
	include(__dir__.'/db-access.php');
	$con = mysqli_connect($DB['SERVER'],$DB['USER'],$DB['PW'],$DB['TABLE']);
	mysqli_set_charset ($con , "utf8");
	$where = str_replace("\\", "", $where);
	$query = "SELECT `satz_nummer` FROM `new_mobile_de` ".$where;
	$temp = mysqli_query($con, $query);
	$count = mysqli_num_rows($temp);
	$val = $count/$amount;
	$pagerCount = round($val, 1);
	if(strlen($pagerCount) != 1){
		$singel = explode(".", $pagerCount);
		if($singel[0] != round($val, 0)){
			$pagerCount = round($val, 0);
		}else{
			$pagerCount = $singel[0]+1;
		}
	}
	return $pagerCount;
}

function convertZeroOne($value, $true, $false){
	if($value == 0){
		return $false;
	}else{
		return $true;
	}
}

function getOfferHtml($id){
	include(__dir__.'/db-access.php');
	$con = mysqli_connect($DB['SERVER'],$DB['USER'],$DB['PW'],$DB['TABLE']);
	mysqli_set_charset ($con , "utf8");
	$offerHtml = "";
	$id = mysqli_real_escape_string($con, $id);
	$query = "SELECT * FROM `new_mobile_de` WHERE `satz_nummer` = ".$id;
	$temp = mysqli_query($con, $query);
	$rows = mysqli_fetch_array($temp, MYSQLI_ASSOC);

	//if($_SERVER["REMOTE_ADDR"] == "217.86.140.30") {
		if($rows['mwstsatz']=='19') {
			$carPrice = number_format(round(str_replace(",", ".", $rows["preis"]) * 1.19, 0), 2, ",", ".");
		} else {
			$carPrice = number_format(str_replace(",", ".", $rows["preis"]), 2, ",", ".");
		}
	/*} else {
		if($rows['mwstsatz']=='19') {
			$carPrice = number_format((str_replace(",", ".", $rows["preis"]) * 1.19), 2, ",", ".");
		} else {
			$carPrice = number_format(str_replace(",", ".", $rows["preis"]), 2, ",", ".");
		}
	}*/

    $carPriceSplit = explode(",", $carPrice);
    $carPriceDecimals = $carPriceSplit[1];
    if($carPriceDecimals == "00")
    {
    	$carPriceDecimals = ",-";
    	$carPrice = $carPriceSplit[0].$carPriceDecimals;
    }
	$offerName = $rows['marke'].' '.$rows['modell'];
	$offerHtml .= "<div class='offer offer-".$id."'>";
		$offerHtml .= "<h1 class='offer-name'>".$offerName."</h1>";			
		$offerHtml .= "<h3>Fahrzeug-Id-Nr.: ".$rows['interne_nummer']."</h3>";
		$offerHtml .= "<div class='row'>";

			$offerHtml .= "<div class='col-md-8 offer-content'>";
				// if($_SERVER["REMOTE_ADDR"] == "5.158.158.123"){
					$offerHtml .= "<div class='offer-image-box'>";
						$offerHtml .= "<div id='offer-image-slider'>";				
							$imageCount = 0;
							while ($imageCount <= 20) {
								$imageCount++;
								if(strlen($imageCount) == 1){
									$imagePrefix = "0".$imageCount;
								}else{
									$imagePrefix = $imageCount;
								}
								$filePath = "/files/newcars/".$rows["interne_nummer"]."_".$imagePrefix.".jpg";
								if(file_exists("/home/www/p522481/html".$filePath)){
									$offerHtml .= "<div class='offer-image-slide'>";
										$offerHtml .= "<img rel='group_".$rows["interne_nummer"]."' class='offer-small-image' alt='".$offerName."' title='".$offerName."' src='".$filePath."' />";
										$offerHtml .= "<div class='hiddengalery' style='display:none;'>";
											$offerHtml .= '<a class="fancybox offer-fancybox" rel="group_'.$rows["interne_nummer"].'" href="'.$filePath.'" title="'.$offerName.'"><img class="hidden-image" alt="'.$offerName.'" src="'.$filePath.'"/></a>';
										$offerHtml .= "</div>";
									$offerHtml .= "</div>";
									$latestAvaibleId = $imagePrefix;
								}
							}
						$offerHtml .= "</div>";
						$offerHtml .= '<script type="text/javascript">jQuery(document).ready(function($) {var relatedPortfolio = $("#offer-image-slider");relatedPortfolio.owlCarousel({margin: 0,mouseDrag:true,nav: true,autoplay: false,autoplayHoverPause: true,dots: false,responsive:{0:{ items:1 },600:{ items:1 },1000:{ items:1 },1200:{ items:1 }}});});</script>';

					$offerHtml .= "</div>";
				// }else{
				// 	$offerHtml .= "<div class='offer-image-box'>";
				// 		$offerHtml .= '<a class="fancybox offer-fancybox bigimage" rel="group_'.$rows["interne_nummer"].'" href="/files/newcars/'.$rows["interne_nummer"].'_01.jpg" title="'.$offerName.'"><img class="offer-image" alt="'.$offerName.'" src="/files/newcars/'.$rows["interne_nummer"].'_01.jpg"/></a>';
				// 		$offerHtml .= "<div id='offer-image-slider'>";
				// 			$imageCount = 1;
				// 			while ($imageCount <= 20) {
				// 				$imageCount++;
				// 				if(strlen($imageCount) == 1){
				// 					$imagePrefix = "0".$imageCount;
				// 				}else{
				// 					$imagePrefix = $imageCount;
				// 				}
				// 				$filePath = "/files/newcars/".$rows["interne_nummer"]."_".$imagePrefix.".jpg";
				// 				if(file_exists("/home/www/p429803/html".$filePath)){
				// 					$offerHtml .= "<div class='offer-image-slide'>";
				// 						$offerHtml .= "<img rel='group_".$rows["interne_nummer"]."' class='offer-small-image' alt='".$offerName."' title='".$offerName."' src='".$filePath."' />";
				// 						$offerHtml .= "<div class='hiddengalery' style='display:none;'>";
				// 							$offerHtml .= '<a class="fancybox offer-fancybox" rel="group_'.$rows["interne_nummer"].'" href="'.$filePath.'" title="'.$offerName.'"><img class="hidden-image" alt="'.$offerName.'" src="'.$filePath.'"/></a>';
				// 						$offerHtml .= "</div>";
				// 					$offerHtml .= "</div>";
				// 					$latestAvaibleId = $imagePrefix;
				// 				}
				// 			}
				// 		$offerHtml .= "</div>";

				// 		$offerHtml .= '<script type="text/javascript">jQuery(document).ready(function() {jQuery(".offer-fancybox").fancybox();});</script>';
				// 		$offerHtml .= '<script type="text/javascript">jQuery(document).ready(function($) {var relatedPortfolio = $("#offer-image-slider");relatedPortfolio.owlCarousel({margin: 0,mouseDrag:false,nav: true,autoplay: false,autoplayHoverPause: true,dots: false,responsive:{0:{ items:1 },600:{ items:2 },1000:{ items:3 },1200:{ items:4 }}});});</script>';
				// 		$offerHtml .= '<script type="text/javascript">jQuery(document).ready(function(){jQuery(".offer-small-image").on("click", function(){newFancyImage = jQuery(this).attr("src");oldFancyImage = jQuery(".offer-fancybox").attr("href");jQuery(".offer-fancybox.bigimage").attr("href", newFancyImage);jQuery(".offer-image").attr("src", newFancyImage);jQuery(this).attr("src", oldFancyImage);jQuery(".hiddengalery .offer-fancybox[href=\""+newFancyImage+"\"]").attr("href", oldFancyImage)});});</script>';

				// 	$offerHtml .= "</div>";
				// }
				$offerHtml .= "<div class='offer-car-detail-box row'>";

					$offerHtml .= '<div class="offer-equipment row">';
					
						$offerHtml .= '<h3>Ausstattung</h3>';
						$offerHtml .= '<ul class="offer-car-detail-equipment">';
							$equipments = "";
							$checkValues = array("leichtmetallfelgen", "esp", "abs", "wegfahrsperre", "navigationssystem", "schiebedach", "zentralverriegelung", "fensterheber", "servolenkung", "tempomat", "standheizung", "kabine", "schutzdach", "vollverkleidung", "schlafplatz", "tv", "wc", "ladebordwand", "schiebetuer", "trennwand", "ebs", "luftfederung", "scheibenbremse", "fronthydraulik", "kueche", "kuehlbox", "schlafsitze", "frontheber", "xenonscheinwerfer", "sitzheizung", "partikelfilter", "einparkhilfe", "elektrische_seitenspiegel", "sportfahrwerk", "sportfahrwerk", "sportpaket", "bluetooth", "bordcomputer", "cd_spieler", "elektrische_sitzeinstellung", "head-up_display", "freisprecheinrichtung", "mp3_schnittstelle", "multifunktionslenkrad", "skisack", "sportsitze", "panorama_dach", "kindersitzbefestigung", "kurvenlicht", "lichtsensor", "nebelscheinwerfer", "tagfahrlicht", "traktionskontrolle", "start_stop_automatik", "regensensor");
							foreach ($checkValues as $checkValue) {
								if($rows[$checkValue] != 0){
									$valueName = "<li class='equipment'>".strtoupper($checkValue[0]).substr($checkValue, 1)."</li>";
									$equipments .= $valueName;
								}
							}
							//$equipments = substr($equipments, 0, -2);
							$offerHtml .= $equipments;
						$offerHtml .= '</ul>';

					$offerHtml .= '</div>';

					if($rows['mwstsatz']=='19') {
						$calcCarPrice = (str_replace(",", ".", $rows["preis"]) * 1.19);
					} else {
						$calcCarPrice = str_replace(",", ".", $rows["preis"]);
					}
					//$calcCarPrice1 = substr($calcCarPrice, 0, strpos($calcCarPrice, ','));
					$calcCarPriceAlt = round($calcCarPrice, 0);
					$offerHtml .= '<div class="offer-description row">';
						$offerHtml .= '<h3>Fahrzeugbeschreibung</h3>';
						$offerDesc = str_replace("**", "", $rows["bemerkung"]);
						$offerDesc = str_replace("*", "</li><li>", $offerDesc);
					$offerDesc = str_replace("\\", "<br />", $offerDesc);
					$offerDesc = substr($offerDesc, 5)."</li>";
						$offerHtml .= '<ul class="offer-description">'.$offerDesc.'</ul>';
					$offerHtml .= '</div>';


					/*$offerHtml .= '<div class="offer-addition row">';
						$offerHtml .= '<h3>Zusatzleistungen</h3>';
						$offerHtml .= '<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>';
						$offerHtml .= '<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>';
					$offerHtml .= '</div>';*/

				$offerHtml .= "</div>";
			$offerHtml .= "</div>";

			$offerHtml .= "<div class='col-md-4'>";

				    $offerHtml .= "<div class='button car-price'>";
					$offerHtml .= "<span class='car-price-label'>Unser Hauspreis:</span>";
					$offerHtml .= "<span class='car-price-value'> ".$carPrice." &euro;</span><br />";
					if($rows['mwstsatz']=='19') {
						$offerHtml .= "<small class='taxnote'>inkl. 19% Mwst.</small>";
					}
					//$offerHtml .= "<span class='car-price-link'> zum Angebot</span>";
					$offerHtml .= '<a class="car-price-link" target="_blank" href="/index.php?article_id=46&carId='.$rows["interne_nummer"].'"><span>Angebot anfordern</span></a>';
				    $offerHtml .= "</div>";



				$offerHtml .= "<div class='offer-contact-box text-image-box'>";

					//$offerHtml .= '<div class="add_button gelb">';
						//$offerHtml .= '<a class="button side-btn" target="_blank" href="/index.php?article_id=46&carId='.$rows["satz_nummer"].'"><span>Angebot anfordern</span><span class="icon arrow"></span></a>';
					//$offerHtml .= '</div>';
					$offerHtml .= '<div class="add_button">';
						$offerHtml .= '<a class="button side-btn" target="_blank" href="/index.php?article_id=13&carId='.$rows["interne_nummer"].'"><span>Probefahrt vereinbaren</span><span class="icon arrow"></span></a>';
					$offerHtml .= '</div>';
					$offerHtml .= '<div class="add_button">';
						$offerHtml .= '<a class="button side-btn" href="/econsor/pdfGenerator.php?id='.$id.'" target="_blank">Exposé Pdf herunterladen<span class="icon arrow"></span></a>';				
					$offerHtml .= '</div>';				
					$offerHtml .= "<div class='add_button light'>";
						if(session_status() != PHP_SESSION_ACTIVE)
							session_start();
						$offerHtml .= '<a rel="nofollow" title="Zurück zur Übersicht" href="'.$_SESSION["backUrl"].'" class="button side-btn backTo">Zurück zur Übersicht<span class="icon arrow"></span></a>';
					$offerHtml .= "</div>";

				$offerHtml .= "</div>";
		
				if(isset($rows["standort"])){
					switch ($rows["standort"]) {
						case 492:
							$offerHtml .= "<div class='location-box'>";
								$offerHtml .= "<a target='_blank' class='location' href='/index.php?article_id=216'><strong>Standort:</strong> 74321 Bietigheim-Bissingen, Berliner Str. 40</a>";
							$offerHtml .= "</div>";
							break;
						case 3188:
							$offerHtml .= "<div class='location-box'>";
								$offerHtml .= "<a target='_blank' class='location' href='/index.php?article_id=241'><strong>Standort:</strong> 74321 Bietigheim-Bissingen, Geisinger Straße 55</a>";
							$offerHtml .= "</div>";
							break;
						case 3189:
							$offerHtml .= "<div class='location-box'>";
								$offerHtml .= "<a target='_blank' class='location' href='/index.php?article_id=242'><strong>Standort:</strong> 71642 Ludwigsburg, Marbacher Straße 69</a>";
							$offerHtml .= "</div>";
							break;
						case 3190:
							$offerHtml .= "<div class='location-box'>";
								$offerHtml .= "<a target='_blank' class='location' href='/index.php?article_id=308'><strong>Standort:</strong> 71642 Ludwigsburg, Schorndorfer Str. 172</a>";
							$offerHtml .= "</div>";
							break;
					}
				}			

				$offerHtml .= '<div class="offer-car-detail-data">';
						$offerHtml .= '<h3>Fahrzeugdaten</h3>';
						$offerHtml .= '<div class="car-detail-box">';
							$offerHtml .= '<span class="car-detail-label">Fahrzeugkategorie: </span>';
							if ($rows["neufahrzeug"] == 1) {
			                    $isUsed = "Neuwagen";
			                }elseif ($rows["tageszulassung"] == 1) {
			                    $isUsed = "Tageszulassung";
			                }elseif ($rows["vorfuehrfahrzeug"] == 1) {
			                    $isUsed = "Vorführfahrzeug";
			                }else{
			                    $isUsed = "Gebrauchtwagen";
			                }
							$offerHtml .= '<span class="car-detail-value">'.$isUsed.'</span>';
						$offerHtml .= '</div>';
						$offerHtml .= '<div class="car-detail-box">';
							$offerHtml .= '<span class="car-detail-label">Außenfarbe: </span>';
							$offerHtml .= '<span class="car-detail-value">'.$rows["farbe"].'</span>';
						$offerHtml .= '</div>';
						$offerHtml .= '<div class="car-detail-box">';
							$offerHtml .= '<span class="car-detail-label">Türen: </span>';
							$offerHtml .= '<span class="car-detail-value">'.$rows["tueren"].'</span>';
						$offerHtml .= '</div>';
						$offerHtml .= '<div class="car-detail-box">';
							$offerHtml .= '<span class="car-detail-label">Erstzulassung: </span>';
							$offerHtml .= '<span class="car-detail-value">'.$rows["ez"].'</span>';
						$offerHtml .= '</div>';
						$offerHtml .= '<div class="car-detail-box">';
							$offerHtml .= '<span class="car-detail-label">Kilometerstand: </span>';
							$offerHtml .= '<span class="car-detail-value">'.number_format($rows["kilometer"], 0, ",", ".").' km</span>';
						$offerHtml .= '</div>';
						$offerHtml .= '<div class="car-detail-box">';
							$offerHtml .= '<span class="car-detail-label">Kraftstoff: </span>';
							$offerHtml .= '<span class="car-detail-value">'.getKraftstoffArt($rows["kraftstoffart"]).'</span>';
						$offerHtml .= '</div>';
						$offerHtml .= '<div class="car-detail-box">';
							$offerHtml .= '<span class="car-detail-label">Hubraum: </span>';
							$offerHtml .= '<span class="car-detail-value">'.$rows["ccm"].' ccm</span>';
						$offerHtml .= '</div>';
						$offerHtml .= '<div class="car-detail-box">';
							$offerHtml .= '<span class="car-detail-label">Leistung: </span>';
							$offerHtml .= '<span class="car-detail-value">'.$rows["leistung"]." / ".round($rows["leistung"]*1.35962).'(kW/PS)</span>';
						$offerHtml .= '</div>';
						$offerHtml .= '<div class="car-detail-box">';
							$offerHtml .= '<span class="car-detail-label">Getriebeart: </span>';
							$offerHtml .= '<span class="car-detail-value">'.getGetriebeArt($rows["getriebeart"]).'</span>';
						$offerHtml .= '</div>';
					$offerHtml .= "</div>";
					$offerHtml .= '<div class="offer-car-detail-enkv row">';
						$offerHtml .= '<h3>Kraftstoffverbrauch</h3>';
						$offerHtml .= '<table class="table">';
						$offerHtml .= '<thead>';
						$offerHtml .= '<tr><th>Wert</th><th>Verbrauch</th></tr>';
						$offerHtml .= '</thead><tbody><tr>';
						if($rows["kombinierter stromverbrauch"] == 0 && $rows["plugin hybrid"] == 0){
							$offerHtml .= '<td class="enkv-label">kombiniert: </td><td class="enkv-value">'.str_replace(".", ",", $rows["verbrauch_kombiniert"]).'l/100km</td>';
							$offerHtml .= '</tr><tr>';
							$offerHtml .= '<td class="enkv-label">innerorts: </td><td class="enkv-value">'.str_replace(".", ",", $rows["verbrauch_innerorts"]).'l/100 km</td>';
							$offerHtml .= '</tr><tr>';
							$offerHtml .= '<td class="enkv-label">außerorts: </td><td class="enkv-value">'.str_replace(".", ",", $rows["verbrauch_ausserorts"]).'l/100 km</td>';
							$offerHtml .= '</tr><tr>';
						}elseif($rows["kombinierter stromverbrauch"] >= 0 && $rows["plugin hybrid"] == 1){
							$offerHtml .= '<td class="enkv-label">kombiniert: </td><td class="enkv-value">'.str_replace(".", ",", $rows["verbrauch_kombiniert"]).'l/100km</td>';
							$offerHtml .= '</tr><tr>';
							$offerHtml .= '<td class="enkv-label">innerorts: </td><td class="enkv-value">'.str_replace(".", ",", $rows["verbrauch_innerorts"]).'l/100 km</td>';
							$offerHtml .= '</tr><tr>';
							$offerHtml .= '<td class="enkv-label">außerorts: </td><td class="enkv-value">'.str_replace(".", ",", $rows["verbrauch_ausserorts"]).'l/100 km</td>';
							$offerHtml .= '</tr><tr>';
							$offerHtml .= '<td class="enkv-label">kombinierter Stromverbrauch: </td><td class="enkv-value">'.str_replace(".", ",", $rows["kombinierter stromverbrauch"]).'kWh/100 km</td>';
							$offerHtml .= '</tr>';
						}elseif($rows["kombinierter stromverbrauch"] >= 0 && $rows["plugin hybrid"] == 0){
							$offerHtml .= '<tr><td class="enkv-label">kombinierter Stromverbrauch: </td><td class="enkv-value">'.str_replace(".", ",", $rows["kombinierter stromverbrauch"]).'kWh/100 km</td></tr>';
						}
						if($rows["emission"] != ""){
							$offerHtml .= '<tr><td class="enkv-label">CO2-Emissionen komb.: </td><td class="enkv-value">'.str_replace(".", ",", $rows["emission"]).' g/km</td></tr>';
						}
						$offerHtml .= '</tbody>';
						$offerHtml .= '</table>';
						if($latestAvaibleId){
							$offerHtml .= '<img class="enkv-image" alt="'.$offerName.'" title="'.$offerName.'" src="../files/newcars/'.$rows["interne_nummer"].'_'.$latestAvaibleId.'.jpg" />';
						}
					$offerHtml .= '</div>';
				$offerHtml .= "</div>";
		$offerHtml .= "</div>";
		$offerHtml .= '<script type="text/javascript">
							jQuery(".backTo").on("click", function(e){
								e.preventDefault();
								jQuery(".offer").fadeOut("2000", function(){});
								jQuery(".fahrzeugverwaltung, .pagination, .fs-sort-box .form-control").fadeIn("2000", function(){});
								history.pushState(
							       {current_page:window.location.pathname, previous_page:"/"},
							       "Fahrzeugübersicht",
							       window.location.pathname+window.location.search
							      );
							    history.pushState(
							        {current_page:jQuery(this).attr("href")+"&page="+jQuery(".pagination.toppager .selected").text()+"&sort="+jQuery(".fs-sort-box select").val(), previous_page:window.location.pathname},
							        "Fahrzeugdetail-Seite",
							        jQuery(this).attr("href")+"&page="+jQuery(".pagination.toppager .selected").text()+"&sort="+jQuery(".fs-sort-box select").val()
							    );
							    if(typeof scrollposition !== "undefined"){
									setTimeout(function(){ jQuery("body,html").scrollTop(scrollposition); }, 500);
								}
							});
						</script>';

		$offerHtml .= '<script>
						jQuery(document).ready(function(){						  
						  jQuery("a").on("click", function(event) {	
						    if (this.hash !== "") {						      
						      event.preventDefault();										      
						      var hash = this.hash;
						      jQuery("html, body").animate({
						        scrollTop: jQuery(hash).offset().top
						      }, 800, function(){	
						      });
						    }
						  });
						});
						loadThis = function() {
							setTimeout(function() {
								rate = jQuery("#finanzrate-ergebnis").text();
								jQuery("<span>"+rate+"</span>").insertAfter(jQuery(".car-price-value"));
								console.log("rate -> "+rate);
							}, 5000);
						}
						</script>';

	$offerHtml .= "</div>";
	return $offerHtml;
}
