<section id="content">
    <div class="wrapper">

    <!-- Left column/section -->

    <section class="grid_6 first">

    <div class="columns leading">
        <div class="grid_6 first">
	     
            <h3><?php echo count($checkinList); ?> Checkins</h3>				
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

                    foreach($checkinList as $r => $data){
                        $userId = $data['user_id'];
                        $userName = $data['user_name'];
                        $created = $data['creation_date'];
                        
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