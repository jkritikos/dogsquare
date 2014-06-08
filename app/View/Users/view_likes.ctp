<section id="content">
    <div class="wrapper">

    <!-- Left column/section -->

    <section class="grid_6 first">

    <div class="columns leading">
        <div class="grid_6 first">
	     
            <h3><?php echo count($activityLikes); ?> Activity Likes</h3>				
            <hr />
            
	     <table class="paginate sortable full">
                <thead>
                    <tr>
                        
                        <th align="left">Activity ID</th>
                        <th align="left">Activity owner</th>
                        <th align="left">Date</th>
                    </tr>
                </thead>
                <tbody>
                    
                <?php

                    foreach($activityLikes as $r => $data){
                        $name = $data['User']['name'];
                        $id = $data['User']['id'];
                        $created = $data['User']['creation_date'];
                        $activityId = $data['User']['activity_id'];
                        
                        $link = "/users/edit/$id";
                        $linkActivity = "/users/viewActivity/$activityId";
                        ?>
                        <tr>
                            <td><a href="<?php echo $linkActivity; ?>"><?php echo $activityId; ?></a></td>
                            <td><a href="<?php echo $link; ?>"><?php echo $name; ?></a></td>
                            
                            <td><?php echo $created; ?></td>
                        </tr>	                    		
                        <?php	                    	
                    }

                ?>

            </tbody>
            </table>
             
	</div>
        
        <div class="grid_6 first">
	     
            <h3><?php echo count($placeLikes); ?> Place Likes</h3>				
            <hr />
            
	     <table class="paginate sortable full">
                <thead>
                    <tr>
                        
                        <th align="left">Place</th>
                        <th align="left">Date</th>
                    </tr>
                </thead>
                <tbody>
                    
                <?php

                    foreach($placeLikes as $r => $data){
                        $name = $data['User']['name'];
                        $id = $data['User']['id'];
                        $created = $data['User']['creation_date'];
                        $placeId = $data['User']['place_id'];
                        
                        $link = "/users/edit/$id";
                        $linkPlace = "/places/editPlace/$placeId";
                        ?>
                        <tr>
                            <td><a href="<?php echo $linkPlace; ?>"><?php echo $name; ?></a></td>
                            
                            <td><?php echo $created; ?></td>
                        </tr>	                    		
                        <?php	                    	
                    }

                ?>

            </tbody>
            </table>
             
	</div>
        
        <div class="grid_6 first">
	     
            <h3><?php echo count($dogLikes); ?> Dog Likes</h3>				
            <hr />
            
	     <table class="paginate sortable full">
                <thead>
                    <tr>
                        
                        <th align="left">Place</th>
                        <th align="left">Date</th>
                    </tr>
                </thead>
                <tbody>
                    
                <?php

                    foreach($dogLikes as $r => $data){
                        $name = $data['User']['name'];
                        $id = $data['User']['id'];
                        $created = $data['User']['creation_date'];
                        
                        
                        $link = "/users/edit/$id";
                        $linkDog = "/dogs/edit/$id";
                        ?>
                        <tr>
                            <td><a href="<?php echo $linkDog; ?>"><?php echo $name; ?></a></td>
                            
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