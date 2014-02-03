<script type="text/javascript" src="/js/jquery.rules.js"></script>
<script>
$(document).ready(function(){

    $("#searchButton").click(function(){
        var nameVal = $("#nameField").val();
	var categoryVal = $("#categoryField").val();
        var statusVal = $("#statusField").val();
	
        if(nameVal == '' && categoryVal == '' && statusVal == ''){
            $("#errorMsg").fadeIn('slow');
            return false;
	} else {
            $("#errorMsg").hide();
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
                        	
                    <form id="form" class="form panel" method="post" action="/places/searchPlace" novalidate>
                        <header><h2>Use any of the following criteria:</h2></header>

                        <hr />
                        <fieldset>
                            <div class="clearfix">
                                <label>Name</label>
                                <input id="nameField" type="text" name="data[Place][name]" minlength="3"/>
                            </div>
                            <div class="clearfix">
                                <label>Category</label>
                                
                                <select id="categoryField" name="data[Place][category]" />
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
                                <label>Status</label>
                                <select id="statusField" name="data[Place][active]">
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
					<th align="left">Category</th>	
					<th align="left">Created</th>
					<th align="left">Edit</th>
				    </tr>
				</thead>
                                <tbody>
	                    	
	                    	<?php
	                    	foreach($results as $r){
                                    $placeId = $r['Place']['id'];
                                    $editLink = "/places/editPlace/$placeId";
                                    ?>
                                    <tr>
                                        <td><?php echo $r['Place']['name']; ?></td>
                                        <td><?php echo $r['Place']['category']; ?></td>
                                        <td><?php echo $r['Place']['created']; ?></td>
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

                <?php echo $this->element('menu_configuration'); ?>
                <!-- End of Right column/section -->
                <div class="clear"></div>

            </div>
            <div id="push"></div>
        </section>