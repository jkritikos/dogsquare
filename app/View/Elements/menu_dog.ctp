<!-- Right column/section -->	
<aside class="grid_2">		
    <div class="widget">				    
        <header>				    
            <h2>Dog properties</h2>			    
        </header>

        <section>				    
            <dl>				    				                                    				    
                <dd>
                <img src="/img/fam/door_out.png" />&nbsp;
                <?php
                if($activities > 0){
                    $link = "/dogs/viewActivities/$id";
                    ?>
                    <a href="<?php echo $link; ?>"><?php echo $activities; ?> activities</a>
                    <?php
                } else {
                    ?>
                    0 activities
                    <?php
                }
                ?>
                </dd>
                <dd>
                <img src="/img/fam/camera.png" />&nbsp;
                <?php
                if($photos > 0){
                    $link = "/dogs/viewPhotos/$id";
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
                <dd>
                <img src="/img/fam/emoticon_smile.png" />&nbsp;
                <?php
                if($likes > 0){
                    $link = "/dogs/viewLikes/$id";
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
            </dl>				    
        </section>				    
    </div>
</aside>		
 <!-- End of Right column/section -->