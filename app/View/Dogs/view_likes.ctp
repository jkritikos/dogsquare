<section id="content">
    <div class="wrapper">

    <!-- Left column/section -->

    <section class="grid_6 first">

    <div class="columns leading">
        <div class="grid_6 first">
	     
            <h3><?php echo count($likesList); ?> Likes</h3>				
            <hr />
            
	     <table class="paginate sortable full">
                <thead>
                    <tr>
                        <th align="left">Name</th>
                        <th align="left">Date</th>
                    </tr>
                </thead>
                <tbody>
                    
                <?php

                    foreach($likesList as $r => $data){
                        $name = $data['User']['name'];
                        $id = $data['User']['id'];
                        $created = $data['User']['creation_date'];
                        
                        $link = "/dogs/edit/$id";
                        ?>
                        <tr>
                            <td><a href="<?php echo $link; ?>"><?php echo $name; ?></a></td>
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
              
<?php echo $this->element('menu_dog'); ?>	
 <!-- End of Right column/section -->

</div>
</section>