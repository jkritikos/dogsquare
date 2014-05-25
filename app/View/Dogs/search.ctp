<script type="text/javascript" src="/js/jquery.rules.js"></script>
<script>
$(document).ready(function(){

    $("#searchButton").click(function(){
        var nameVal = $("#nameField").val();
	var breedVal = $("#breedField").val();
        var countryVal = $("#countryField").val();
        var matingVal = $("#matingField").val();
        
	if(nameVal == '' && breedVal == '' && countryVal == '' && matingVal == ''){
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
                        	
                    <form id="form" class="form panel" method="post" action="/dogs/search">
                        <header><h2>Use any of the following criteria:</h2></header>

                        <hr />
                        <fieldset>
                            <div class="clearfix">
                                <label>Name</label>
                                <input id="nameField" type="text" name="data[Dog][name]" minlength="3"/>
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
                                <select id="countryField" name="data[User][country_id]">
                                    <option selected value="">Please select</option>
                                    <?php
                                    
                                    foreach($countries as $i => $data){
                                        ?>
                                        <option value="<?php echo $i; ?>"><?php echo $data; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="clearfix">
                                <label>Mating</label>
                                <select id="matingField" name="data[Dog][mating]">
                                    <option selected value="">Please select</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
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
					<th align="left">Breed</th>					
					<th align="left">Country</th>
                                        <th align="left">Owner</th>
				    </tr>
				</thead>
                                <tbody>
	                    	
	                    	<?php
	                    
	                    	foreach($results as $r => $data){
                                    $dogId = $data['Dog']['id'];
                                    $userId = $data['Dog']['owner_id'];
                                    
                                    $dogLink = "/dogs/edit/$dogId";
                                    $userLink = "/users/edit/$userId";
                                    ?>
                                    <tr>
                                        <td><a href="<?php echo $dogLink; ?>"><?php echo $data['Dog']['name']; ?></a></td>
                                        <td><?php echo $data['Dog']['country']; ?></td>
                                        <td><?php echo $data['Dog']['breed']; ?></td>
                                        <td><a href="<?php echo $userLink; ?>"><?php echo $data['Dog']['owner']; ?></a></td>
                             
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
                        <dd><img src="/img/fam/search.png" />&nbsp;<a href="/dogs/search">Search dogs</a></dd>				    				                                    				    				    
                    </dl>				    
                </section>				    
            </div>		
	</aside>

        <!-- End of Right column/section -->
        <div class="clear"></div>

    </div>
    <div id="push"></div>
</section>