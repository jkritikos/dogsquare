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

        <form id="form" class="form panel" method="post" action="/places/addPhoto/<?php echo $id; ?>" enctype="multipart/form-data" novalidate>
            <input type="hidden" name="data[Place][id]" value="<?php echo $id; ?>" />
            <header><h2>Use the following fields to add a new photo for this place:</h2></header>

            <hr />
            <fieldset>
                <div class="clearfix">
                    <label>Photo (square) *</label>
                    <input type="file" name="photo" required="required" />
                </div>
                <div class="clearfix">
                    <label>Photo Thumbnail (square) *</label>
                    <input type="file" name="thumbnail" required="required" />
                </div>
            </fieldset>

            <hr />
            <button class="button button-green" type="submit">Add</button>
            <button class="button button-gray" type="reset">Reset</button>
            <img id="loader" style="display:none;position:absolute;" src="/img/ajax-loader.gif" />
        </form>
    </div>
</div>
    
<div class="clear">&nbsp;</div>
    
</section>
    
    <!-- End of Left column/section -->

    <!-- Right column/section -->

    <?php echo $this->element('menu_place'); ?>	
    <!-- End of Right column/section -->
    <div class="clear"></div>
</div>
<div id="push"></div>
</section>