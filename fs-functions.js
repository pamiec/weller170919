function getChangedItems(changedItems){
  if(jQuery("#marke").val() != "0"){
    changedItems["marke"] = jQuery("#marke option:selected");
  }
  if(jQuery("#modell").val() != "0"){
    changedItems["modell"] = jQuery("#modell option:selected");
  }
  if(jQuery("#kraftstoffart").val() != "0"){
    changedItems["kraftstoffart"] = jQuery("#kraftstoffart option:selected");
  }
  if(jQuery("#ez").val() != "0"){
    changedItems["ez"] = jQuery("#ez option:selected");
  }
  return changedItems;
}

function checkInForm(changed, totalOffers, neufahrzeug, neuwagen, gebrauchtwagen){
  var resetModell;
  jQuery("#fahrzeugsuche #fs-form input, #fahrzeugsuche #fs-form select").on("change keyup", function(e) {
    if(jQuery(this).attr('type') == "text" && e.type == "change"){
      return true;
    }
    jQuery("#fsearch-submit").addClass('disabled');
    jQuery("#fsearch-submit").on("click", function(e){
      e.preventDefault();
    });
    jQuery("#fsearch-submit").att
    changedItems[this.id] = jQuery("#"+this.id+" option:selected");
    if( (this.name == "marke" && this.value == "0") || jQuery("#marke").val() != jQuery("#modell"+" option:selected").attr("data-mark")){
      resetModell = "1";
    }else{
      resetModell = "";
    }
    if(this.name == "neuwagen" || this.name == "gebrauchtwagen"){
      if(this.name == "neuwagen" && neuwagen == 1){
        neuwagen = 0;
      }else if(this.name == "neuwagen"){
        neuwagen = 1;
      }
      if(this.name == "gebrauchtwagen" && gebrauchtwagen == 1){
        gebrauchtwagen = 0;
      }else if(this.name == "gebrauchtwagen"){
        gebrauchtwagen = 1;
      }
      if(neuwagen == 1){
        neufahrzeug = 1;      
      }
      if(gebrauchtwagen == 1){
        neufahrzeug = 3;
      }
      if((gebrauchtwagen == 0 && neuwagen == 0) || (gebrauchtwagen == 1 && neuwagen == 1)){
        neufahrzeug = 2;
      }
      changed += "neufahrzeug_"+neufahrzeug+",";
    }else if(this.name == "fulltext"){
      changed += this.name+"_"+this.value.replace(/,/g, "|KOMMA|")+",";
    }else{
      changed += this.name+"_"+this.value+",";
      if(resetModell == "1"){
        changed += "resetModell_=_1,";
      }
    }
    jQuery("#fsearch-changed").attr('value', changed);
    jQuery.ajax({
      method: "POST",
      url: "./econsor/giveNewOptions.php",
      data: {changed: changed}
    }).done(function(data) {
      allNewOptionsObj = jQuery.parseJSON(data);
      jQuery("#fsearch-submit").removeClass('disabled');
      jQuery("#fsearch-submit").off("click");
      jQuery.each(allNewOptionsObj, function(key, allNewOptions){
        if(key == "neufahrzeug"){
          if(allNewOptions < 2){
            if(jQuery.inArray("1", allNewOptions) != "-1"){
              jQuery("#check2").prop('disabled', true);
              jQuery("#check1").prop('disabled', false);
              jQuery("#check2").prop('checked', false);
              if(jQuery("#check1").prop("checked") == true){
                gebrauchtwagen = 0;
                neufahrzeug = 1;
                changed += "neufahrzeug_"+"1"+",";
              }
            }
            if(jQuery.inArray("0", allNewOptions) != "-1"){
              jQuery("#check1").prop('disabled', true);
              jQuery("#check2").prop('disabled', false);
              jQuery("#check1").prop('checked', false);
              if(jQuery("#check2").prop("checked") == true){
                neuwagen = 0;
                neufahrzeug = 3;
                changed += "neufahrzeug_"+"3"+",";
              }
            }
            return;
          }else{
            changed += "neufahrzeug_"+"2"+",";
            jQuery("#check2, #check1").prop('disabled', false);
            return;
          }
        }
        if(key == "count"){
          jQuery("#fsearch-submit").val("Suchen ("+allNewOptions+" Ergebnisse)");
          if(totalOffers > allNewOptions){
            jQuery("#filter-reset").fadeIn('slow', function() {});
          }else{
            jQuery("#filter-reset").fadeOut('slow', function() {});
          }
          if(allNewOptions == 0){
            jQuery("#fsearch-submit").addClass('disabled');
            jQuery("#fsearch-submit").on("click", function(e){
              e.preventDefault();
            });
          }else{
            jQuery("#fsearch-submit").removeClass('disabled');
            jQuery("#fsearch-submit").off("click");
          }
          return;
        }
        if(key == "where"){
          jQuery("#fsearch-where").val(allNewOptions);
          return;
        }   
        forSelect = key;
        if(forSelect == "modell")
          jQuery("#modell").prop('disabled', false);
        else
          jQuery("#modell").prop('disabled', true);
        var labelOption = jQuery("#"+forSelect+" option[hidden]");
        jQuery("#"+forSelect).html("<option value='0'>Beliebig</option>");
        jQuery("#"+forSelect).prepend(labelOption);
        jQuery.each(allNewOptions, function(key, newOptions) { 
          jQuery("#"+forSelect).append(newOptions);
        });
      });
      jQuery.each(changedItems, function(key, changedItem){        
        if((key != "modell" || (key == "modell" && resetModell != "1")) && (key != "select5" && key != "select3" && key != "check2" && key != "check1" && key != "fulltext")){
          if(jQuery("#"+key+" option[value='"+changedItem.val()+"']").length == 0)
            jQuery("#"+key).append(changedItem);
          jQuery("#"+key).val(changedItem.val());
        }
      });
      if(resetModell == "1"){
        jQuery("#modell").val("0");
      }
    });
  });
  /* Filter zurücksetzten */
  jQuery("#filter-reset").on("click", function() {
    jQuery("#check2, #check1").prop('disabled', false);
    jQuery("#check2, #check1").prop('checked', false);
    jQuery("#modell").prop('disabled', true);
    jQuery("#modell, #marke, #select3, #kraftstoffart, #select5, #ez").val("0");
    jQuery("#fsearch-changed").attr('value', '');
    jQuery("#fsearch-where").attr('value', 'WHERE 1');
    jQuery("#fulltext").val("");
    changed = "";
    neuwagen = 0;
    gebrauchtwagen = 0;
    neufahrzeug = 0;
    changedItems = {};
    jQuery.ajax({
      method: "POST",
      url: "./econsor/giveNewOptions.php",
      data: {changed: changed}
    }).done(function(data) {
      jQuery("#filter-reset").fadeOut('slow', function() {});
      allNewOptionsObj = jQuery.parseJSON(data);
      jQuery.each(allNewOptionsObj, function(key, allNewOptions){
        forSelect = key;
        var labelOption = jQuery("#"+forSelect+" option[hidden]");
        jQuery("#"+forSelect).html("<option value='0'>Beliebig</option>");
        jQuery("#"+forSelect).prepend(labelOption);
        if(key == "count"){
          jQuery("#fsearch-submit").val("Suchen");
          return;
        }
        if(key == "where"){
          return;
        }
        jQuery.each(allNewOptions, function(key, newOptions) { 
          jQuery("#"+forSelect).append(newOptions);
        });
      });
    });
  });
}

function preFillForm(changed){
  jQuery.ajax({
    method: "POST",
    url: "./econsor/giveNewOptions.php",
    data: {changed: changed}
  }).done(function(data) {
    allNewOptionsObj = jQuery.parseJSON(data);
    jQuery.each(allNewOptionsObj, function(key, allNewOptions){
      if(key == "neufahrzeug"){
        if(allNewOptions < 2){
          if(jQuery.inArray("1", allNewOptions) != "-1"){
            jQuery("#check2").prop('disabled', true);
            jQuery("#check1").prop('disabled', false);
          }
          if(jQuery.inArray("0", allNewOptions) != "-1"){
            jQuery("#check1").prop('disabled', true);
            jQuery("#check2").prop('disabled', false);
          }
          return;
        }else{
          jQuery("#check2, #check1").prop('disabled', false);
          return;
        }
      }
      if(key == "count"){
        jQuery("#fsearch-submit").val("Suchen ("+allNewOptions+" Ergebnisse)");
        return;
      }
      if(key == "where"){
        jQuery("#fsearch-where").val(allNewOptions);
        return;
      }         
      forSelect = key;
      if(forSelect == "modell")
        jQuery("#modell").prop('disabled', false);
      else
        jQuery("#modell").prop('disabled', true);
      var labelOption = jQuery("#"+forSelect+" option[hidden]");
      jQuery("#"+forSelect).html("<option value='0'>Beliebig</option>");
      jQuery("#"+forSelect).prepend(labelOption);
      labelOption.prop('selected', true);
      jQuery.each(allNewOptions, function(key, newOptions) { 
        jQuery("#"+forSelect).append(newOptions);
      });
      changedArray = changed.split(",");
      changedArray.pop();
      jQuery.each(changedArray, function(key, changedValues) {
        changedValue = changedValues.split("_");
        if(changedValue[0] != "prekategorie"){
          labelOption.prop('selected', false);
          jQuery("#"+changedValue[0]+" option[value='"+changedValue[1]+"'").prop('selected', true);
        }
      });
    });
    jQuery.each(jQuery("#fahrzeugsuche select"), function(index, el) {
      if(jQuery(el).val() === "0")
        jQuery(el).val(0);
    });
    changedItems = getChangedItems(changedItems);
  });
}

function pagerUse(maxpages, where, amount){
  firstToLoadPage = (jQuery(".pagination.toppager .selected").text()-1);
  jQuery(".fahrzeugverwaltung #page-"+firstToLoadPage).fadeIn("700", function() {});
  lock = 0;
  beforePager = "";
  jQuery(".pagination li").on("click", function(e){
    e.preventDefault();
    if(lock == 0 && !jQuery(this).hasClass('selected') && !jQuery(this).hasClass('disabled')){
      if(pageBefore || pageBefore == "0"){
        jQuery("#page-"+pageBefore).remove();
      }
      jQuery(".fs-overview-page.old").remove();
      pageBefore = jQuery(".pagination.toppager .selected").text()-1;
      jQuery(".pagination .selected").removeClass('selected');
      if(jQuery(this).hasClass('page-after')){
        page = pageBefore+1;
        jQuery(".pagination .switch-"+page).addClass('selected');
      }else{ 
        if(jQuery(this).hasClass('page-before')){
          page = pageBefore-1;
          jQuery(".pagination .switch-"+page).addClass('selected');
        }else{
          page = jQuery(this).text()-1;
          jQuery(".pagination .switch-"+page).addClass('selected');
        }
      }
      lock = 1;
      if(page == 0){
        jQuery(".page-before").addClass('disabled');
      }else{
        jQuery(".page-before").removeClass('disabled');
      }
      if(page == maxpages){
        jQuery(".page-after").addClass('disabled');
      }else{
        jQuery(".page-after").removeClass('disabled');
      }
      normalswitches = jQuery.find('.pagination .normalswitch');
      jQuery.each(normalswitches, function(key, value) {
        if((jQuery(value).text()-1) < (page-2) || (page+2) < (jQuery(value).text()-1)){
          if(
              (page == 0 && jQuery(value).text() != 4 && jQuery(value).text() != 5) || 
              (page == 1 && jQuery(value).text() != 5) || 
              (page == maxpages && jQuery(value).text() != (maxpages - 2) && jQuery(value).text() != (maxpages - 3)) ||
              (page == (maxpages-1) && jQuery(value).text() != (maxpages - 3)) || 
              (page != 0 && page != 1 && page != maxpages && page != (maxpages-1))
            )
          {
            jQuery(value).css('display', 'none');
            jQuery(value).addClass('hiddenSwitch');
          }
        }else{
          jQuery(value).css('display', 'inline');
          jQuery(value).removeClass('hiddenSwitch');
        }
      });
      jQuery(".fahrzeugverwaltung #page-"+pageBefore).fadeOut('2000', function() {jQuery(".fahrzeugverwaltung #page-"+page).fadeIn("3000", function() {lock = 0;});});
      sort = jQuery(".fs-sort-box .form-control").val();
      if(sort == 0){
        sort = "";
      }
      jQuery.ajax({
        url: './econsor/giveNewPageResults.php',
        type: 'POST',
        data: {where: where, page: page, amount: amount, sort: sort},
      }).done(function(data){
        jQuery(".fahrzeugverwaltung").append(data);
      });
      setTimeout(function() {lock = 0;}, 1000);
    }
  });
}

function sortUse(where, amount){
  jQuery(".fs-sort-box .form-control").on("change", function(e){
    if(pageBefore || pageBefore == "0"){
      jQuery("#page-"+pageBefore).remove();
    }
    jQuery(".fs-overview-page.old").remove();
    sort = jQuery(this).val();
    jQuery(".fs-sort-box .form-control").val(jQuery(this).val());
    if(sort == 0){
      sort = "";
    }

    page = parseInt(jQuery("#fs-pager-top .selected").text())-1;
    jQuery(".fahrzeugverwaltung #page-"+page).addClass('old');
    jQuery(".fahrzeugverwaltung #page-"+page).fadeOut('2000', function() {jQuery(".fahrzeugverwaltung #page-"+page+":not(.old)").fadeIn("3000", function() {lock = 0;});});

    jQuery.ajax({
      url: './econsor/giveNewPageResults.php',
      type: 'POST',
      data: {where: where, page: page, amount: amount, sort: sort},
    }).done(function(data){
      jQuery(".fahrzeugverwaltung").append(data);
    });
  });
}

function switchToDetail(backUrl){
	jQuery(".fahrzeugverwaltung").on("click", ".car-inner a", function(e){
    e.preventDefault();
    scrollposition = $(window).scrollTop();
    jQuery(".offer").remove();
    setTimeout(function(){ jQuery('body,html').scrollTop(0); }, 500);
    carId = jQuery(this).attr("data-carId");
    if(pageBefore || pageBefore == "0")
      jQuery("#page-"+pageBefore).remove();
    jQuery(".fahrzeugverwaltung, .pagination").fadeOut('2000', function() {});
    jQuery(".fahrzeugverwaltung, .pagination, .fs-sort-box .form-control").fadeOut('2000', function() {});
    url = jQuery(this).attr("href");
    jQuery.ajax({
      url: './econsor/giveOffer.php',
      type: 'POST',
      data: {carId: carId},
    }).done(function(data) {
      jQuery(data).insertAfter('.fahrzeugverwaltung');
      history.pushState(
        {current_page:backUrl+"&page="+jQuery(".pagination.toppager .selected").text()+"&sort="+jQuery(".fs-sort-box select").val(), previous_page:"/"},
        'Fahrzeugübersicht',
        backUrl+"&page="+jQuery(".pagination.toppager .selected").text()+"&sort="+jQuery(".fs-sort-box select").val()
      );
      history.pushState(
        {current_page:url, previous_page:backUrl},
        'Fahrzeugdetail-Seite',
        url+"&page="+jQuery(".pagination.toppager .selected").text()+"&sort="+jQuery(".fs-sort-box select").val()
      );
    });
  });
}

function checkFullTextUpdates(neufahrzeug){
  jQuery(".fs-fulltext-searchfield").on("change", function(){
    jQuery.ajax({
      url: './econsor/giveFulltextOption.php',
      type: 'POST',
      data: {fulltext: jQuery(this).val(), neufahrzeug: neufahrzeug},
    }).done(function(data) {
      jQuery(".fs-fulltext-block .button").val("Suchen ("+data+" Ergebnisse)");
    });
  });
}