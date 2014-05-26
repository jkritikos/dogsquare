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
		$action = "/dogs/edit/$id";
		?>
	    <form id="form" class="form panel" method="post" action="<?php echo $action; ?>" novalidate>
	    <input type="hidden" name="data[Dog][id]" value="<?php echo $id; ?>" />	    
		<header><h2>Use the following fields to update the dog details:</h2></header>

		<hr />
		<fieldset>
                    <div class="clearfix">
                        <label>Name</label>
                        <input size="50" value="<?php echo $dog['Dog']['name']; ?>" type="text" name="data[Dog][name]" required="required" />
                    </div>
                    <div class="clearfix">
                        <label>Breed</label>
                        <select id="breedField" name="data[Dog][breed_id]">
                            <option selected value="">Please select</option>
                            <?php

                            foreach($breeds as $i => $data){
                                ?>
                                <option <?php if($dog['Dog']['breed_id'] == $i) echo "selected"; ?> value="<?php echo $i; ?>"><?php echo $data; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="clearfix">
                        <label>Mating</label>
                        <select name="data[Dog][mating]">
                            <option <?php if($dog['Dog']['mating'] == 1) echo "selected"; ?> value="1">Yes</option>
                            <option <?php if($dog['Dog']['mating'] == 2) echo "selected"; ?> value="2">No</option>
			</select>
                    </div>
                    <div class="clearfix">
                        <label>Gender</label>
                        <select name="data[Dog][gender]">
                            <option <?php if($dog['Dog']['gender'] == 1) echo "selected"; ?> value="1">Male</option>
                            <option <?php if($dog['Dog']['gender'] == 2) echo "selected"; ?> value="2">Female</option>
			</select>
                    </div>
                    <div class="clearfix">
                        <label>Size</label>
                        <select name="data[Dog][size]">
                            <option <?php if($dog['Dog']['size'] == 1) echo "selected"; ?> value="1">Small</option>
                            <option <?php if($dog['Dog']['size'] == 2) echo "selected"; ?> value="2">Medium</option>
                            <option <?php if($dog['Dog']['size'] == 3) echo "selected"; ?> value="3">Large</option>
                            <option <?php if($dog['Dog']['size'] == 4) echo "selected"; ?> value="4">X-Large</option>
			</select>
                    </div>
                    <div class="clearfix">
                        <label>Owner</label>
                        <label><?php echo $owner['User']['name']; ?></label>
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
        
    <?php echo $this->element('menu_dog'); ?>
   
    <!-- End of Right column/section -->
    <div class="clear"></div>    
</div>
<div id="push"></div>
</section>