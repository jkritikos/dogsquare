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

        <form id="form" class="form panel" method="post" action="/configurations/dogfuelDiscountAdd" novalidate>
            <header><h2>Use the following fields to add a Dogfuel discount:</h2></header>

            <hr />
            <fieldset>
                <div class="clearfix">
                    <label>Age from *</label>
                    <input id="breed" type="number" name="data[DogfuelDiscount][age_from]" required="required" />
                </div>
                <div class="clearfix">
                    <label>Age to  *</label>
                    <input id="breed" type="number" name="data[DogfuelDiscount][age_to]" required="required" />
                </div>
                <div class="clearfix">
                    <label>Dogfuel boost (%) *</label>
                    <input id="breed" type="number" name="data[DogfuelDiscount][extra_dogfuel]" required="required" />
                </div>
                <div class="clearfix">
                    <label>Weather temperature *</label>
                    <input id="breed" type="number" name="data[DogfuelDiscount][weather_temp]" required="required" />
                </div>
                <div class="clearfix">
                    <label>Weather boost (%) *</label>
                    <input id="breed" type="number" name="data[DogfuelDiscount][weather_value]" required="required" />
                </div>
            </fieldset>

            <hr />
            <button class="button button-green" type="submit">Create</button>
            <button class="button button-gray" type="reset">Reset</button>
            <img id="loader" style="display:none;position:absolute;" src="/img/ajax-loader.gif" />
        </form>
    </div>
</div>


<div class="columns">
    <div class="grid_6 first">		
        
        <?php
        if(isset($data) && !empty($data)){
            ?>
            <h3>Dogfuel Boosts</h3>				
            <hr />
            
	     <table class="paginate sortable full">
                <thead>
                    <tr>
                        <th align="left">Age from</th>					
                        <th align="left">Age to</th>
                        <th align="left">Boost</th>
                        <th align="left">Weather temp</th>
                        <th align="left">Weather boost</th>
                        <th align="left">Action</th>
                    </tr>
                </thead>
                <tbody>
                    
                <?php

                    foreach($data as $df){
                        $id = $df['DogfuelDiscount']['id'];
                        $editLink = "/configurations/dogfuelDiscountEdit/$id";
                        $age_from = $df['DogfuelDiscount']['age_from'];
                        $age_to = $df['DogfuelDiscount']['age_to'];
                        $dogfuel = $df['DogfuelDiscount']['extra_dogfuel'];
                        $weather_temp = $df['DogfuelDiscount']['weather_temp'];
                        $weather_value = $df['DogfuelDiscount']['weather_value'];
                        ?>
                        <tr>
                            <td><?php echo $age_from; ?></a></td>
                            <td><?php echo $age_to; ?></td>
                            <td><?php echo $dogfuel; ?></td>
                            <td><?php echo $weather_temp; ?></td>
                            <td><?php echo $weather_value; ?></td>
                            <td><a href="<?php echo $editLink; ?>">Edit</a></td>
                        </tr>	                    		
                        <?php	                    	
                    }

                ?>

            </tbody>
            </table>
            <?php
        } else {
            ?>
            <div style="margin-top: 30px;" class="message info">
                <h3>Info</h3>
                <p>
                No Dogfuel discounts were found.
                </p>
            </div>
            <?php
        }
        ?>
        
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