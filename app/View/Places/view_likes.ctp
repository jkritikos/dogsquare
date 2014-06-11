<section id="content">
    <div class="wrapper">

    <!-- Left column/section -->

    <section class="grid_6 first">

    <div class="columns leading">
        <div class="grid_6 first">
	     
            <h3><?php echo count($placeLikes); ?> Likes</h3>				
            <hr />
            
	     <table class="paginate sortable full">
                <thead>
                    <tr>
                        <th align="left">User</th>
                        
                        <th align="left">Date</th>
                    </tr>
                </thead>
                <tbody>
                    
                <?php

                    foreach($placeLikes as $r => $data){
                        $placeId = $data['PlaceLike']['place_id'];
                        $placeName = $data['PlaceLike']['place_name'];
                        $userId = $data['PlaceLike']['user_id'];
                        $userName = $data['PlaceLike']['user_name'];
                        $created = $data['PlaceLike']['creation_date'];
                        
                        $linkPlace = "/places/editPlace/$placeId";
                        $linkUser = "/users/edit/$userId";
                        ?>
                        <tr>
                            <td><a href="<?php echo $linkUser; ?>"><?php echo $userName; ?></a></td>
                            
                            <td><?php echo $created; ?></td>
                        </tr>	                    		
                        <?php	                    	
                    }

                ?>

            </tbody>
            </table>
             
	</div>
        
    </div>

	<div class="clear">&nbsp;</div>
 
</section>
<!-- End of Left column/section -->
              
 <?php echo $this->element('menu_place'); ?>	
 <!-- End of Right column/section -->

</div>
</section>