<?php
$action = "/configurations/dogfuelEdit/$id";

$activeSelected = "";
$deactiveSelected = "";

if(isset($dogfuel)){
    if($dogfuel['Dogfuel']['active'] == '1'){
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
    	
    	var form = $(this);
    	//client-side passed
    	if (!e.isDefaultPrevented()) {
	        if(json.data === true){
	            $('#loader').show();
	            document.getElementById('form').submit();
	        } else {
	            form.data("validator").invalidate(json.data);
	        }
       
        	e.preventDefault();
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

        <form id="form" class="form panel" method="post" action="<?php echo $action; ?>" novalidate>
            <input type="hidden" name="data[DogfuelRule][id]" value="<?php echo $id; ?>" />
            <header><h2>Use the following fields to edit this dog fuel rule:</h2></header>

            <hr />
            <fieldset>
                <div class="clearfix">
                    <label>Breed *</label>
                    <label><?php echo $dogfuel['Dogfuel']['name']; ?></label>
                </div>
                <div class="clearfix">
                    <label>Walk Distance *</label>
                    <input id="walkDistance" type="number" name="data[DogfuelRule][walk_distance]" value="<?php echo $dogfuel['Dogfuel']['walkDistance']; ?>" required="required" />
                </div>
                <div class="clearfix">
                    <label>Playtime *</label>
                    <input id="playtime" type="number" name="data[DogfuelRule][playtime]" value="<?php echo $dogfuel['Dogfuel']['playtime']; ?>" required="required" />
                </div>
                <div class="clearfix">
                    <label>Active *</label>
                    <select name="data[DogfuelRule][active]" required="required" />
                        <option <?php echo $activeSelected; ?> value="1">Active</option>
                        <option <?php echo $deactiveSelected; ?> value="0">Deactive</option>
                    </select>
                </div>

            </fieldset>

            <hr />
            <button class="button button-green" type="submit">Create</button>
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