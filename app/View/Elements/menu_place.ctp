<!-- Right column/section -->	
<aside class="grid_2">		
    
    <div class="widget">    
        <header>
            <h2>Options</h2>
    	</header>    
    	<section>
    
    	<dl>				    				                                    				    
            <dd><img src="/img/fam/picture_add.png" />&nbsp;<a href="/places/addPhoto/<?php echo $id; ?>">Add photos</a></dd>
            <dd><img src="/img/fam/picture_edit.png" />&nbsp;<a href="/places/profilePhoto/<?php echo $id; ?>">Set profile photo</a></dd>
    	</dl>
    
    	</section>    						    
    </div>
    
    <div class="widget">				    
        <header>				    
            <h2>Place properties</h2>			    
        </header>

        <section>				    
            <dl>				    				                                    				    
                <dd>
                <img src="/img/fam/comment.png" />&nbsp;
                <?php
                if($comments > 0){
                    $link = "/places/viewComments/$id";
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
                
                <dd>
                <img src="/img/fam/emoticon_smile.png" />&nbsp;
                <?php
                if($likes > 0){
                    $link = "/places/viewLikes/$id";
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
                <img src="/img/fam/accept.png" />&nbsp;
                <?php
                if($checkins > 0){
                    $link = "/places/viewCheckins/$id";
                    ?>
                    <a href="<?php echo $link; ?>"><?php echo $checkins; ?> checkins</a>
                    <?php
                } else {
                    ?>
                    0 checkins
                    <?php
                }
                ?>
                </dd>
                <dd>
                    <img src="/img/fam/camera.png" />&nbsp;
                    <?php
                    if($photos > 0){
                        $link = "/places/viewPhotos/$id";
                        ?>
                        <a href="<?php echo $link; ?>"><?php echo $photos; ?> photos</a>
                        <?php
                    } else {
                        ?>
                        0 photos
                        <?php
                    }
                    ?>
                </dd>
            </dl>				    
        </section>				    
    </div>
</aside>		
 <!-- End of Right column/section -->