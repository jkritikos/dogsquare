<script>
$(document).ready(function(){

    $( ".commentDeletion" ).click(function(e){
        
        var commentId = $( this ).attr("commentId");
        var actionFlag = $(this).attr('actionFlag');
        var commentType = 1;
        //alert('delete comment '+commentId+' with actionFlag '+actionFlag);
        var button = $(this);
        
        var actualButton = $(this).find('span');
        //actualButton.removeClass('accept');
        //actualButton.addClass('delete');
        
        
        var options = {
            url: "/users/processComment",
            type: "POST",
            dataType: "json",
            data: ({comment_id : commentId, type_id:commentType, flag:actionFlag}),
            success: function(d){
                
                //alert('got back response '+d);
                //var s = JSON.stringify(d);
                //alert(s);

                if(d.data.result){
                    
                    if(actualButton.hasClass('accept')){
                        actualButton.removeClass('accept');
                        actualButton.addClass('delete');
                        //button.html('Restore');
                        button.attr('actionFlag', 0);
                    } else {
                        actualButton.removeClass('delete');
                        actualButton.addClass('accept');
                        //button.html('Delete');
                        button.attr('actionFlag', 1);
                    }
                } else {
                    alert('error');
                }
            }
        };

        $.ajax(options);
 
    });
			          
    
});
</script>

<section id="content">
    <div class="wrapper">

    <!-- Left column/section -->

    <section class="grid_6 first">

    <div class="columns leading">
        <div class="grid_6 first">
	     
            <h3><?php echo count($commentList); ?> Comments</h3>				
            <hr />
            
	     <table class="paginate sortable full">
                <thead>
                    <tr>
                        <th align="left">Name</th>
                        <th align="left">Comment</th>
                        <th align="left">Date</th>
                        <th align="left">Action</th>
                    </tr>
                </thead>
                <tbody>
                    
                <?php

                    foreach($commentList as $r => $data){
                        $name = $data['comm']['name'];
                        $id = $data['comm']['user_id'];
                        $comment_id = $data['comm']['id'];
                        $comment = $data['comm']['text'];
                        $creation_date = $data['comm']['creation_date'];
                        $created = $data['comm']['creation_date'];
                        $active = $data['comm']['active'];
                        
                        $link = "/users/edit/$id";
                        
                        $actionFlag = "0";
                        $buttonClass = "delete";
                        if($active == 0){
                            $actionFlag = 1;
                            $buttonClass = "accept";
                        }
                        
                        ?>
                        <tr>
                            <td><a href="<?php echo $link; ?>"><?php echo $name; ?></a></td>
                            <td><?php echo $comment; ?></td>
                            <td><?php echo $creation_date; ?></td>
                            <td><a commentId="<?php echo $comment_id; ?>" actionFlag="<?php echo $actionFlag; ?>" href="#" class="action-button commentDeletion" title="accept"><span class="<?php echo $buttonClass; ?>"></span></a></td>
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