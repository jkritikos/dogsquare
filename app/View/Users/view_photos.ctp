<script src="/js/jquery-1.11.0.min.js"></script>
<script src="/js/lightbox.min.js"></script>
<script>
$(document).ready(function(){

    $( ".photoDeletion" ).click(function(e){
        
        var photoId = $( this ).attr("photoId");
        var actionFlag = $(this).attr('actionFlag');
        
        //alert('delete photo '+photoId+' with actionFlag '+actionFlag);
        //return false;
        
        var button = $(this);
        
        var actualButton = $(this).find('span');
        //actualButton.removeClass('accept');
        //actualButton.addClass('delete');
        
        
        var options = {
            url: "/users/processPhoto",
            type: "POST",
            dataType: "json",
            data: ({photo_id : photoId, flag:actionFlag}),
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
<link href="/css/lightbox.css" rel="stylesheet" />
<section id="content">
    <div class="wrapper">

    <!-- Left column/section -->

    <section class="grid_6 first">

    <div class="columns leading">
        <div class="grid_6 first">
	     
            <h3><?php echo count($photoList); ?> Photos</h3>				
            <hr />
            
	     <table class="paginate sortable full">
                <thead>
                    <tr>
                        
                        <th align="left">ID</th>
                        <th align="left">Photo</th>
                        <th align="left">Date</th>
                        <th align="left">Action</th>
                    </tr>
                </thead>
                <tbody>
                    
                <?php
                    $i = 0;
                    foreach($photoList as $r => $data){
                        $i++;
                        
                        $active = $data['active'];
                        $id = $data['id'];
                        $created = $data['creation_date'];
                        $thumb = $data['thumb'];
                        $img = $data['path'];
                        $thumbPath = FILE_PATH . USER_PATH . "/" . $thumb;
                        $imgPath = FILE_PATH . USER_PATH . "/" . $img;
                        
                        $actionFlag = "0";
                        $buttonClass = "delete";
                        if($active == 0){
                            $actionFlag = 1;
                            $buttonClass = "accept";
                        }
                        ?>
                        <tr>
                            <td><?php echo $id; ?></td>
                            <td><a data-lightbox="image-<?php echo $i; ?>" href="<?php echo $imgPath; ?>"><img src="<?php echo $thumbPath; ?>"></td>
                            <td><?php echo $created; ?></td>
                            <td><a photoId="<?php echo $id; ?>" actionFlag="<?php echo $actionFlag; ?>" href="#" class="action-button photoDeletion" title="accept"><span class="<?php echo $buttonClass; ?>"></span></a></td>
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