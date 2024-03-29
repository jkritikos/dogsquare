<script type="text/javascript" src="/js/jquery.rules.js"></script>
<script>
$(document).ready(function(){
	     
    $("#massUpdateButton").click(function(){
        var walkValue = $("#massUpdateWalk").val();
	var playValue = $("#massUpdatePlay").val();
        
	if(walkValue == '' && playValue == ''){
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
    	
    	var form = $(this);
        
    	//client-side passed
    	if (!e.isDefaultPrevented()) {
    		
            $.post("/configurations/validateDogfuel", { dogfuel: $("#dogfuel").val()},function(json) {
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

<!-- Start of create dog fuel section -->

<section class="grid_6 first">
	<?php if($breed != null) { ?>
	<div class="columns leading">
		
	    <div class="grid_6 first">		
			
	        <form id="form" class="form panel" method="post" action="/configurations/dogfuelView" novalidate>
	        	
	            <header><h2>Use the following fields to create a new dog fuel rule:</h2></header>
	
	            <hr />
	            <fieldset>
	            	<div class="clearfix">
	                    <label>Breed *</label>
	                    <select id="dogfuel"  name="data[DogfuelRule][breed_id]" required="required" />
	                        <option value="">Please select</option>
	                        <?php  
	                        	foreach ($breed as $key => $b): 
	                        		$breedName = $b['DogBreed']['name']; 
									$breedId = $b['DogBreed']['id'];  
	                        ?>
	                         	<option value="<?php echo $breedId ?>"><?php echo $breedName ?></option>
	                        <?php endforeach; ?>
	                    </select>
	                </div>
	                <div class="clearfix">
	                    <label>walk distance *</label>
	                    <input id="walkDistance" type="number" name="data[DogfuelRule][walk_distance]" required="required" />
	                </div>
	                <div class="clearfix">
	                    <label>Active *</label>
	                    <select name="data[DogfuelRule][active]" required="required" />
	                        <option value="">Please select</option>
	                        <option value="1">Yes</option>
	                        <option value="0">No</option>
	                    </select>
	                </div>
	                <div class="clearfix">
	                    <label>playtime *</label>
	                    <input id="playtime" type="number" name="data[DogfuelRule][playtime]" required="required" />
	                </div>
	                <div>
	                    <input type="hidden" name="data[DogfuelRule][user_id]" value="<?php echo $currentUser  ?>"/>
	                </div>
	            </fieldset>
	
	            <hr />
	            <button class="button button-green" type="submit">Create</button>
	            <button class="button button-gray" type="reset">Reset</button>
	            <img id="loader" style="display:none;position:absolute;" src="/img/ajax-loader.gif" />
	            
	        </form>
	        
	    </div>
	    
	</div>
			<?php }else{ ?>
		            	<div style="margin-top: 30px;" class="message info">
		                	<h3>Information</h3>
		                	<p>
		                    There are no breeds that need dog fuel info implemented.
		                	</p>
		            	</div>
	       <?php } ?>
	
    
<div class="clear">&nbsp;</div>

<!-- End of create dog fuel section-->

<!-- Start of dog fuel View section -->

<div class="columns leading">
        <div class="grid_6 first">
	     
            <h3>Dogfuel mass update</h3>				
            <hr />
            <form id="formMassUpdate" class="form panel" method="post" action="/configurations/dogfuelMassUpdate" novalidate>
            <header><h2>Apply the desired formula (e.g * 1.5 or / 2 or + 5 or - 3)</h2></header>

            <hr />
            <fieldset>
                <div class="clearfix">
                    <label>Walk Distance</label>
                    <input id="massUpdateWalk" type="text" name="data[DogfuelRule][walk]"/>
                </div>
                <div class="clearfix">
                    <label>Playtime</label>
                    <input id="massUpdatePlay" type="text" name="data[DogfuelRule][play]"/>
                </div>
            </fieldset>
            <span id="errorMsg" style="display:none"><b><font color="red">You must specify at least one of the values.</font></b></span>
            <hr />
            <button id="massUpdateButton" class="button button-green" type="submit">Update</button>
            <button class="button button-gray" type="reset">Reset</button>
            <img id="loader" style="display:none;position:absolute;" src="/img/ajax-loader.gif" />
        </form>
	</div>
    
        <div class="grid_6 first">
	     
            <h3>Dogfuel Rules</h3>				
            <hr />
            
	     <table class="paginate sortable full">
                <thead>
                    <tr>
                        <th align="left">Breed</th>					
                        <th align="left">Walk Distance</th>
                        <th align="left">Playtime</th>
                        <th align="left">Active</th>
                    </tr>
                </thead>
                <tbody>
                    
                <?php

                    foreach($dogfuel as $df){
                        $id = $df['Dogfuel']['id'];
                        $editLink = "/configurations/dogfuelEdit/$id";
                        $name = $df['Dogfuel']['name'];
						$walkDistance = $df['Dogfuel']['walkDistance'];
						$playtime = $df['Dogfuel']['playtime'];
                        $active = $df['Dogfuel']['active'] == 1 ? "Active" : "Deactive";
                        ?>
                        <tr>
                            <td><a href="<?php echo $editLink; ?>"><?php echo $name; ?></a></td>
                            <td><?php echo $walkDistance; ?></td>
                            <td><?php echo $playtime; ?></td>
                            <td><?php echo $active; ?></td>
                        </tr>	                    		
                        <?php	                    	
                    }

                ?>

            </tbody>
            </table>
             
	</div>
    </div>

	<div class="clear">&nbsp;</div>
	<!-- End of dog fuel View section -->
    
</section>
    
<!-- Right column/section -->

<?php echo $this->element('menu_configuration'); ?>
    <!-- End of Right column/section -->
    <div class="clear"></div>
    
</div>
<div id="push"></div>
</section>