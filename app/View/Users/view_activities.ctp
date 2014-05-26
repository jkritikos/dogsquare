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
                        <th align="left">Date</th>
                        <th align="left">Temperature</th>
                    </tr>
                </thead>
                <tbody>
                    
                <?php

                    foreach($activitiesList as $r => $data){
                        $created = $data['Activity']['created'];
                        $id = $data['Activity']['id'];
                        $temperature = $data['Activity']['temperature'];
                        $pace = $data['Activity']['pace'];
                        $distance = $data['Activity']['distance'];
                        
                        $link = "/users/editActivity/$id";
                        ?>
                        <tr>
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
        
    <?php echo $this->element('menu_user'); ?>
        
    </aside>	
 <!-- End of Right column/section -->

</div>
</section>