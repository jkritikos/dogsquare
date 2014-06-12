<script src="/js/jquery-1.11.0.min.js"></script>
<script src="/js/lightbox.min.js"></script>
<script>
$(document).ready(function(){

    $( ".profilePhoto" ).change(function() {
        var photoId = $( this ).attr("photoId");
        var placeId = '<?php echo $id; ?>';
        
        var options = {
            url: "/places/updateProfilePhoto",
            type: "POST",
            dataType: "json",
            data: ({photo_id : photoId, place_id:placeId}),
            success: function(d){
                
                //alert('got back response '+d);
                //var s = JSON.stringify(d);
                //alert(s);

                if(d.data.result){
                    
                    
                } else {
                    //alert('error');
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
	     
            <h3>Select the profile photo for this place </h3>				
            <hr />
            
	     <table class="paginate sortable full">
                <thead>
                    <tr>
                        
                        <th align="left">ID</th>
                        <th align="left">Photo</th>
                        <th align="left">Date</th>
                        <th align="left">Profile Photo</th>
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
                        $thumbPath = FILE_PATH . PLACE_PATH . "/" . $thumb;
                        $imgPath = FILE_PATH . PLACE_PATH . "/" . $img;
                        
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
                            <td><input photoId="<?php echo $id; ?>" class="profilePhoto" type="radio" name="data[Place][photo_id]" <?php if($place['Place']['photo_id'] == $id) echo "checked ";?>/></td>
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
              
<?php echo $this->element('menu_place'); ?>		
 <!-- End of Right column/section -->

</div>
</section>