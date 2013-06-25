<section id="content">
    <div class="wrapper">

    <!-- Left column/section -->

    <section class="grid_6 first">

    <div class="columns leading">
        <div class="grid_6 first">
	     
            <h3>All Place Categories</h3>				
            <hr />
            
	     <table class="paginate sortable full">
                <thead>
                    <tr>
                        <th align="left">Name</th>					
                        <th align="left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    
                <?php

                    foreach($placeCategories as $r){
                        $placeCategoryId = $r['PlaceCategory']['id'];
                        $editLink = "/placeCategories/editPlaceCategory/$placeCategoryId";
                        $name = $r['PlaceCategory']['name'];
                        $active = $r['PlaceCategory']['active'] == 1 ? "Active" : "Deactive";
                        ?>
                        <tr>
                            <td><a href="<?php echo $editLink; ?>"><?php echo $name; ?></a></td>
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
 
</section>
<!-- End of Left column/section -->
              
<!-- Right column/section -->	
<?php echo $this->element('menu_configuration'); ?>		
 <!-- End of Right column/section -->

</div>
</section>