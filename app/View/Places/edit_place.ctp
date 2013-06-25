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

<script type="text/javascript" src="/js/jquery.rules.js"></script>
<script>
$(document).ready(function(){
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
		<header><h2>Use the following fields to update the user details:</h2></header>

		<hr />
		<fieldset>
                    <div class="clearfix">
                        <label>Name</label>
                        <input value="<?php echo $place['Place']['name']; ?>" type="text" name="data[Place][name]" " />
                    </div>
                    <div class="clearfix">
                    <label>Category</label>
                    <select name="data[Place][category_id]" " />
                        <option value="">Please select</option>
                        <?php  
                        	foreach ($categoryNames as $key => $cat): 
                        		$categoryName = $cat['PlaceCategory']['name']; 
								$data['Place']['id'] = $cat['PlaceCategory']['id'];  
								$categoryId = $data['Place']['id'];
                        ?>
                         	<option value="<?php echo $categoryId ?>"><?php echo $categoryName ?></option>
                        <?php endforeach; ?>
                    </select>
                	</div>
                    <div class="clearfix">
                        <label>Status</label>
                        <select name="data[Place][active]">
							<option <?php echo $activeSelected; ?> value="1">Active</option>
							<option <?php echo $deactiveSelected; ?> value="0">Deactive</option>
						</select>
                    </div>
		</fieldset>

		<hr />
		<button class="button button-green" type="submit">Update</button>
		<button class="button button-gray" type="reset">Reset</button>
		<img id="loader" style="display:none;position:absolute;" src="/img/ajax-loader.gif" />
	    </form>
	</div>
    </div>
    
    <div class="clear">&nbsp;</div>    
    </section>
    
    <!-- End of Left column/section -->    
    <!-- Right column/section -->
    
    <?php echo $this->element('menu_configuration'); ?>
    
    <!-- End of Right column/section -->
    <div class="clear"></div>    
</div>
<div id="push"></div>
</section>