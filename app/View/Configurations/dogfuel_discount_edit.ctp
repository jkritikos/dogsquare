<?php
$url = "/configurations/dogfuelDiscountEdit/".$discount['DogfuelDiscount']['id'];
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
            $.post("/configurations/validateBreed", { breed: $("#breed").val()},function(json) {
                
                if(json.data === true){
                    $('#loader').show();
                    document.getElementById('form').submit();
                } else {
                    form.data("validator").invalidate(json.data);
                }
            }, "json");
            
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

        <form id="form" class="form panel" method="post" action="<?php echo $url; ?>" novalidate>
            <header><h2>Use the following fields to edit this Dogfuel discount:</h2></header>

            <hr />
            <fieldset>
                <div class="clearfix">
                    <label>Age from *</label>
                    <input id="breed" type="number" name="data[DogfuelDiscount][age_from]" required="required" value="<?php echo $discount['DogfuelDiscount']['age_from'];?>"/>
                </div>
                <div class="clearfix">
                    <label>Age to  *</label>
                    <input id="breed" type="number" name="data[DogfuelDiscount][age_to]" required="required" value="<?php echo $discount['DogfuelDiscount']['age_to'];?>"/>
                </div>
                <div class="clearfix">
                    <label>Dogfuel boost (%) *</label>
                    <input id="breed" type="number" name="data[DogfuelDiscount][extra_dogfuel]" required="required" value="<?php echo $discount['DogfuelDiscount']['extra_dogfuel'];?>"/>
                </div>
                <div class="clearfix">
                    <label>Weather temperature *</label>
                    <input id="breed" type="number" name="data[DogfuelDiscount][weather_temp]" required="required" value="<?php echo $discount['DogfuelDiscount']['weather_temp'];?>"/>
                </div>
                <div class="clearfix">
                    <label>Weather boost (%) *</label>
                    <input id="breed" type="number" name="data[DogfuelDiscount][weather_value]" required="required" value="<?php echo $discount['DogfuelDiscount']['weather_value'];?>"/>
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