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
                                <option value="<?php echo $i; ?>"><?php echo $data; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="clearfix">
                        <label>Country</label>
                        <label><?php echo $user['Country']['name']; ?></label>
                    </div>
                    <div class="clearfix">
                        <label>Registration date</label>
                        <label><?php echo $user['User']['created']; ?></label>
                    </div>
                    <div class="clearfix">
                        <label>Status</label>
                        <select name="data[User][active]">
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
    
    <aside class="grid_2">    				
    <div class="widget">    
        <header>
            <h2>Options</h2>
    	</header>    
    	<section>
    
    	<dl>				    				                                    				    
            <dd><img src="/img/fam/user_add.png" />&nbsp;<a href="/users/create">Create new user</a></dd>
            <dd><img src="/img/fam/search.png" />&nbsp;<a href="/users/search">Search users</a></dd>
            <dd><img src="/img/fam/user_edit.png" />&nbsp;<a href="/users/profile">My profile</a></dd>
    	</dl>
    
    	</section>    						    
    </div>
        
    <?php echo $this->element('menu_user'); ?>
        
    </aside>
    
    <!-- End of Right column/section -->
    <div class="clear"></div>    
</div>
<div id="push"></div>
</section>