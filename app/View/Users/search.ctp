<script type="text/javascript" src="/js/jquery.rules.js"></script>
<script>
$(document).ready(function(){

    $("#searchButton").click(function(){
        var nameVal = $("#nameField").val();
	var emailVal = $("#emailField").val();
        var statusVal = $("#statusField").val();
	if(nameVal == '' && emailVal == '' && statusVal == ''){
            $("#errorMsg").fadeIn('slow');
            return false;
	}
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
            <div class="columns leading">

                <div class="columns">
                <div class="grid_6 first">
                        	
                    <form id="form" class="form panel" method="post" action="/users/search">
                        <header><h2>Use any of the following criteria:</h2></header>

                        <hr />
                        <fieldset>
                            <div class="clearfix">
                                <label>Name</label>
                                <input id="nameField" type="text" name="data[User][name]" minlength="3"/>
                            </div>
                            <div class="clearfix">
                                <label>Email</label>
                                <input id="emailField" type="text" name="data[User][email]" minlength="3"/>
                            </div>
                            <div class="clearfix">
                                <label>Status</label>
                                <select id="statusField" name="data[User][active]">
                                    <option selected value="">Please select</option>
                                    <option value="1">Active</option>
                                    <option value="0">Deactive</option>
                                </select>
                            </div>
                        </fieldset>
                        <span id="errorMsg" style="display:none"><b><font color="red">You must specify at least one of the criteria.</font></b></span>
                        <hr />
                        <button id="searchButton" class="button button-green" type="submit">Search</button>
                        <button class="button button-gray" type="reset">Reset</button>
                        <img id="loader" style="display:none;position:absolute;" src="/img/ajax-loader.gif" />
                    </form>
               </div>
                    </div>

			<?php
			if(isset($results)){			
			?>

                    <div class="columns leading">
                        <div class="grid_6 first">
                            <h3>Search results</h3>				
                            <hr />
                            
                            <?php
                            if(count($results) == 0){
                            	?>
                            	<div class="message warning">
                            	    <h3>No matching users found</h3>
                            	    <p>
                            	        Try specifying different criteria.
                            	    </p>
	                        </div>
	                    	<?php
	                    } else {
	                    
	                    	?>
	                    	
	                    	<table class="paginate sortable full">
				<thead>
				    <tr>
					<th align="left">Name</th>
					<th align="left">Email</th>					
					<th align="left">Created</th>
					<th align="left">Edit</th>
				    </tr>
				</thead>
                                <tbody>
	                    	
	                    	<?php
	                    
	                    	foreach($results as $r){
                                    $userId = $r['User']['id'];
                                    $editLink = "/users/edit/$userId";
                                    ?>
                                    <tr>
                                        <td><?php echo $r['User']['name']; ?></td>
                                        <td><?php echo $r['User']['email']; ?></td>
                                        <td><?php echo $r['User']['created']; ?></td>
                                        <td><a href="<?php echo $editLink; ?>"><img src="/img/fam/user_edit.png" /></a></td>
                                    </tr>	                    		
                                    <?php	                    	
	                    	}
	                    }
	                    ?>
	                    
	                    </tbody>
                            </table>
	                    
                        </div>
                    </div>
                        
                        <?php
                        }
                        ?>
                        
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