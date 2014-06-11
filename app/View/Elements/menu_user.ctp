<div class="widget">    
    <header>
        <h2>User properties</h2>
    </header>    
    <section>

    <dl>				    				                                    				    
        <dd>
            <img src="/img/fam/group.png" />&nbsp;
            <?php
            if($followers > 0){
                $link = "/users/viewFollowers/$user_id";
                ?>
                <a href="<?php echo $link; ?>"><?php echo $followers; ?> followers</a>
                <?php
            } else {
                ?>
                0 followers
                <?php
            }
            ?>
            
        </dd>
        <dd>
            <img src="/img/fam/user_green.png" />&nbsp;
            <?php
            if($following > 0){
                $link = "/users/viewFollowing/$user_id";
                ?>
                <a href="<?php echo $link; ?>"><?php echo $following; ?> following</a>
                <?php
            } else {
                ?>
                0 following
                <?php
            }
            ?>
        </dd>
        <dd>
            <img src="/img/fam/rosette.png" />&nbsp;
            <?php
            if($dogs > 0){
                $link = "/users/viewDogs/$user_id";
                ?>
                <a href="<?php echo $link; ?>"><?php echo $dogs; ?> dogs</a>
                <?php
            } else {
                ?>
                0 dogs
                <?php
            }
            ?>
        </dd>
        <dd>
            <img src="/img/fam/door_out.png" />&nbsp;
            <?php
            if($activities > 0){
                $link = "/users/viewActivities/$user_id";
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
            <img src="/img/fam/comment.png" />&nbsp;
            <?php
            if($comments > 0){
                $link = "/users/viewComments/$user_id";
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
                $link = "/users/viewLikes/$user_id";
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
            <img src="/img/fam/camera.png" />&nbsp;
            <?php
            if($photos > 0){
                $link = "/users/viewPhotos/$user_id";
                ?>
                <a href="<?php echo $link; ?>"><?php echo $photos; ?> gallery photos</a>
                <?php
            } else {
                ?>
                0 gallery photos
                <?php
            }
            ?>
        </dd>
        <dd>
            <img src="/img/fam/accept.png" />&nbsp;
            <?php
            if($checkins > 0){
                $link = "/users/viewCheckins/$user_id";
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
    </dl>

    </section>    						    
</div>