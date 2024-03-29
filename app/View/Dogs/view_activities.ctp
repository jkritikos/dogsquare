<section id="content">
    <div class="wrapper">

    <!-- Left column/section -->

    <section class="grid_6 first">

    <div class="columns leading">
        <div class="grid_6 first">
	     
            <h3><?php echo count($activitiesList); ?> Activities</h3>				
            <hr />
            
	     <table class="paginate sortable full">
                <thead>
                    <tr>
                        <th align="left">ID</th>
                        <th align="left">Date</th>
                        <th align="left">Temperature</th>
                    </tr>
                </thead>
                <tbody>
                    
                <?php

                    foreach($activitiesList as $r => $data){
                        $created = $data['Activity']['creation_date'];
                        $id = $data['Activity']['id'];
                        $temperature = $data['Activity']['temperature'];
                        $pace = $data['Activity']['pace'];
                        $distance = $data['Activity']['distance'];
                        
                        $link = "/users/viewActivity/$id";
                        ?>
                        <tr>
                            <td><?php echo $id; ?></td>
                            <td><a href="<?php echo $link; ?>"><?php echo $created; ?></a></td>
                            <td><?php echo $temperature; ?></td>
                            
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
              
<?php echo $this->element('menu_dog'); ?>
 <!-- End of Right column/section -->

</div>
</section>