<section id="content">
    <div class="wrapper">

    <!-- Left column/section -->

    <section class="grid_6 first">

    <div class="columns leading">
        <div class="grid_6 first">
	     
            <h3>Total users: <?php echo $totalUsers; ?></h3>				
            <hr />
            
	     <table class="paginate sortable full">
                <thead>
                    <tr>
                        <th align="left"># of users</th>
                        
                        <th align="left">Country</th>
                    </tr>
                </thead>
                <tbody>
                    
                <?php

                    foreach($users as $r => $data){
                        
                        ?>
                        <tr>
                            <td><?php echo $data['cnt']; ?></td>
                            
                            <td><?php echo $data['name']; ?></td>
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
              
 <?php echo $this->element('menu_report'); ?>	
 <!-- End of Right column/section -->

</div>
</section>