<section id="content">
    <div class="wrapper">

    <!-- Left column/section -->

    <section class="grid_6 first">

    <div class="columns leading">
        <div class="grid_6 first">
	     
            <h3><?php echo count($likesLists); ?> Likes</h3>				
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

                    foreach($likesLists as $r => $data){
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
              
<aside class="grid_2">
    <div class="widget">
        <header>
            <h2>Activity properties</h2>
        </header>
        <section>
            <dl>				    				                                    				    
                <dd>
                    <img src="/img/fam/emoticon_smile.png" />&nbsp;
                    <?php
                    if($likes > 0){
                        $link = "/users/viewActivityLikes/$activity_id";
                        ?>
                        <a href="<?php echo $link; ?>"><?php echo $likes; ?> likes</a>
                        <?php
                    } else {
                        ?>
                        0 likes
                        <?php
                    }
                    ?>
                </dd>	
                <dd>
                    <img src="/img/fam/comment.png" />&nbsp;
                    <?php
                    if($comments > 0){
                        $link = "/users/viewActivityComments/$activity_id";
                        ?>
                        <a href="<?php echo $link; ?>"><?php echo $comments; ?> comments</a>
                        <?php
                    } else {
                        ?>
                        0 comments
                        <?php
                    }
                    ?>
                </dd>
            </dl>
        </section>	    
    </div>
</aside>	
 <!-- End of Right column/section -->

</div>
</section>