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
            $.post("/places/validatePlace", { place: $("#place").val()},function(json) {
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

        <form id="form" class="form panel" method="post" action="/places/createPlace" novalidate>
            <header><h2>Use the following fields to create a new place:</h2></header>

            <hr />
            <fieldset>
                <div class="clearfix">
                    <label>Name *</label>
                    <input id="place" type="text" name="data[Place][name]" required="required" />
                </div>
                <div class="clearfix">
                    <label>Active *</label>
                    <select name="data[Place][active]" required="required" />
                        <option value="">Please select</option>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
                <div class="clearfix">
                    <label>Category *</label>
                    <select name="data[Place][category_id]" required="required" />
                        <option value="">Please select</option>
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
                    <input type="text" name="data[Place][lon]" required="required" />
                </div>
                <div class="clearfix">
                    <label>Latitude *</label>
                    <input type="text" name="data[Place][lat]" required="required" />
                </div>
                <div class="clearfix">
                    <label>User *</label>
                    <select name="data[Place][user_id]" required="required" />
                        <option value="">Please select</option>
                        <?php  
                        	foreach ($userNames as $key => $user): 
                        		$userName = $user['User']['name']; 
								$userId = $user['User']['id'];  
                        ?>
                         	<option value="<?php echo $userId ?>"><?php echo $userName ?></option>
                        <?php endforeach; ?>
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