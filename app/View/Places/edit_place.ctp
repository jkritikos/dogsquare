<?php

$activeSelected = "";
$deactiveSelected = "";
$developerSelected = "";
$managerSelected = "";
$externalSelected = "";
$internalSelected = "";

if(isset($place)){
    if($place['Place']['active'] == '1'){
	$activeSelected = "selected";
	$deactiveSelected = "";
    } else {
	$deactiveSelected = "selected";
	$activeSelected = "";
    }
}

?>

<script type="text/javascript"src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCytXV60HGiJyfNucScCd98poZ73bVfmHo&sensor=false"></script>
<script type="text/javascript" src="/js/jquery.rules.js"></script>
<script type="text/javascript">
  function initialize() {
    var myLatlng = new google.maps.LatLng(<?php echo $place['Place']['lat']; ?>, <?php echo $place['Place']['lon']; ?>);
  
    var mapOptions = {
        zoom:17,
      center: new google.maps.LatLng(<?php echo $place['Place']['lat']; ?>, <?php echo $place['Place']['lon']; ?>)
    };
    var map = new google.maps.Map(document.getElementById("map-canvas"),
        mapOptions);
        
    
    // To add the marker to the map, use the 'map' property
var marker = new google.maps.Marker({
    position: myLatlng,
    map: map,
    title:"Hello World!"
});
        
  }
  google.maps.event.addDomListener(window, 'load', initialize);
</script>
<script>
$(document).ready(function(){
    //map
    initialize();
    
    $('#deletePlaceButton').click(function(){
        
        $('#hiddenActiveFlag').val(0);
    });
    
    $("#form").validator({    	
    	position: 'left',
    	offset: [25, 10],
    	messageClass:'form-error',
    	message: '<div><em/></div>' // em element is the arrow
    }).submit(function(e) {
    	
    	if (!e.isDefaultPrevented()) {
            $('#loader').show();
    	}    	
    });
});
</script>

<section id="content">
    <div class="wrapper">

    <!-- Left column/section -->

    <section class="grid_6 first">

<div class="columns">
	<div class="grid_6 first">
		<?php 
		$action = "/places/editPlace/$targetPlaceId";
		?>
	    <form id="form" class="form panel" method="post" action="<?php echo $action; ?>" novalidate>
	    <input type="hidden" name="data[Place][id]" value="<?php echo $targetPlaceId; ?>" />
            <input id="hiddenActiveFlag" type="hidden" name="data[Place][active_override]" value="<?php echo $place['Place']['active']; ?>" />
		<header><h2>Use the following fields to update the place details:</h2></header>

		<hr />
		<fieldset>
                    <div class="clearfix">
                        <label>Name</label>
                        <input value="<?php echo $place['Place']['name']; ?>" type="text" name="data[Place][name]" />
                    </div>
                    <div class="clearfix">
                        <label>Active</label>
                        <select name="data[Place][active]">
                            <option <?php echo $activeSelected; ?> value="1">Active</option>
                            <option <?php echo $deactiveSelected; ?> value="0">Deactive</option>
                        </select>
                    </div>
                    <div class="clearfix">
                        <label>Category</label>
                        <select name="data[Place][category_id]">
                            <option value="<?php echo $place['Place']['category_id']; ?>"><?php echo $placeCategoryById['PlaceCategory']['name']; ?></option>
                            <?php  
                                foreach ($categoryNames as $key => $cat): 
                                    $categoryName = $cat['PlaceCategory']['name']; 
                                    $categoryId = $cat['PlaceCategory']['id']; 
                            ?>
                            <option value="<?php echo $categoryId ?>"><?php echo $categoryName ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="clearfix">
                        <label>Longitude *</label>
                        <input type="text" name="data[Place][lon]" value="<?php echo $place['Place']['lon']; ?>" required="required" />
                    </div>
                    <div class="clearfix">
                        <label>Latitude *</label>
                        <input type="text" name="data[Place][lat]" value="<?php echo $place['Place']['lat']; ?>" required="required" />
                    </div>
                    <div class="clearfix">
                        <label>Weight *</label>
                        <input type="number" name="data[Place][weight]" value="<?php echo $place['Place']['weight']; ?>"/>
                    </div>
                    <div class="clearfix">
                        <label>URL</label>
                        <input type="text" name="data[Place][url]" value="<?php echo $place['Place']['url']; ?>"/>
                    </div>
                    <div class="clearfix">
                        <label>Color</label>
                        <input type="text" name="data[Place][color]" value="<?php echo $place['Place']['color']; ?>"/>
                    </div>
                    <div class="clearfix">
                        <label>Font Color</label>
                        <input type="text" name="data[Place][color_font]" value="<?php echo $place['Place']['color_font']; ?>"/>
                    </div>
		</fieldset>

		<hr />
		<button class="button button-green" type="submit">Update</button>
		<button class="button button-gray" type="reset">Reset</button>
                <button id="deletePlaceButton" class="button button-orange" type="submit">Delete</button>
		<img id="loader" style="display:none;position:absolute;" src="/img/ajax-loader.gif" />
	    </form>
	</div>
    
        <div class="columns leading">
            <div class="grid_6 first">
                <h3>Location:</h3>				
                <hr />

                <div style="width:750px; height:380px;" id="map-canvas"></div>

            </div>
        </div>
    
    </div>
    
    <div class="clear">&nbsp;</div>    
    </section>
    
    <!-- End of Left column/section -->    
    <!-- Right column/section -->
    
    <?php echo $this->element('menu_place'); ?>		
	</aside>
    
    <!-- End of Right column/section -->
    <div class="clear"></div>    
</div>
<div id="push"></div>
</section>