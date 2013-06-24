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
            $.post("/users/validateEmail", { email: $("#email").val()},function(json) {
                
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

	    <form id="form" class="form panel" method="post" action="/users/create" novalidate>
	    <input type="hidden" name="data[User][activated]" value="1" />
		<header><h2>Use the following fields to create a new user:</h2></header>

		<hr />
		<fieldset>
                    <div class="clearfix">
                        <label>Name *</label>
                        <input type="text" name="data[User][name]" required="required" />
                    </div>
                    <div class="clearfix">
                        <label>Email *</label>
                        <input id="email" type="text" name="data[User][email]" required="required" />
                    </div>
                    <div class="clearfix">
                        <label>Password *</label>
                        <input type="password" id="password" name="data[User][password]" required="required"/>
                    </div>
                    <div class="clearfix">
                        <label>Password confirmation *</label>
                        <input type="password" name="check" data-equals="password" required="required"/>
                    </div>
                    <div class="clearfix">
                        <label>Roles *</label>
                        <select multiple style="width:160px;height:100px;" id="typeSelector" name="data[UserRole][role_id][]" required="required">
                            <?php
                            foreach($roles as $i => $role){
                                ?>
                                <option value="<?php echo $i; ?>"><?php echo $role; ?></option>
                                <?php
                            }
                            ?>  
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
    
    		</aside>
    
                    <!-- End of Right column/section -->
                    <div class="clear"></div>
    
                </div>
                <div id="push"></div>
        </section>