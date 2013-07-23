<?php
$action = "/configurations/breedEdit/$id";

$activeSelected = "";
$deactiveSelected = "";

if(isset($breed)){
    if($breed['DogBreed']['active'] == '1'){
	$activeSelected = "selected";
	$deactiveSelected = "";
    } else {
	$deactiveSelected = "selected";
	$activeSelected = "";
    }

}

?>

<script type="text/javascript" src="/js/jquery.rules.js"></script>
<script>
$(document).ready(function(){
			           
    $("#form").validator({    	
    	position: 'left',
    	offset: [25, 10],
    	messageClass:'form-error',
    	message: '<div><em/></div>' // em element is the arrow
    }).submit(function(e) {
    	
    	var form = $(this);
        
    	//client-side passed
    	if (!e.isDefaultPrevented()) {
            $.post("/configurations/validateBreed", { 
            	breed: $("#breed").val(),
            	edit_breed: '<?php echo $breed['DogBreed']['name']; ?>'
            },function(json) {
                if(json.data === true){
                    $('#loader').show();
                    document.getElementById('form').submit();
                } else {
                    form.data("validator").invalidate(json.data);
                }
            }, "json");
            
            e.preventDefault();
    	}
    	
    });
});
</script>

<section id="content">
	<div class="wrapper">

                <!-- Left column/section -->

                <section class="grid_6 first">

<div class="columns">
    <div class="grid_6 first">		

        <form id="form" class="form panel" method="post" action="<?php echo $action; ?>" novalidate>
            <input type="hidden" name="data[DogBreed][id]" value="<?php echo $id; ?>" />
            <header><h2>Use the following fields to edit this breed:</h2></header>

            <hr />
            <fieldset>
                <div class="clearfix">
                    <label>Name *</label>
                    <input id="breed" type="text" name="data[DogBreed][name]" value="<?php echo $breed['DogBreed']['name']; ?>" required="required" />
                </div>
                <div class="clearfix">
                    <label>Origin *</label>
                    <input id="breed" type="text" name="data[DogBreed][origin]" value="<?php echo $breed['DogBreed']['origin']; ?>" required="required" />
                </div>
                <div class="clearfix">
                    <label>Weight *</label>
                    <label style="width:30px;">from </label>
                    <input id="breed" style="width:30px;" type="number" name="data[DogBreed][weight_from]" value="<?php echo $breed['DogBreed']['weight_from']; ?>" required="required" />
                    <label style="width:22px; margin-left: 10px;">to </label>
                    <input id="breed" style="width:30px; margin-left:0;" type="number" name="data[DogBreed][weight_to]" value="<?php echo $breed['DogBreed']['weight_to']; ?>" required="required" />
                </div>
                <div class="clearfix">
                    <label>Kennel Club *</label>
                    <input id="breed" type="text" name="data[DogBreed][kennel_club]" value="<?php echo $breed['DogBreed']['kennel_club']; ?>" value="<?php echo $breed['DogBreed']['kennel_club']; ?>" required="required" />
                </div>
                <div class="clearfix">
                    <label>Active *</label>
                    <select name="data[DogBreed][active]" required="required" />
                        <option <?php echo $activeSelected; ?> value="1">Active</option>
                        <option <?php echo $deactiveSelected; ?> value="0">Deactive</option>
                    </select>
                </div>

            </fieldset>

            <hr />
            <button class="button button-green" type="submit">Create</button>
            <button class="button button-gray" type="reset">Reset</button>
            <img id="loader" style="display:none;position:absolute;" src="/img/ajax-loader.gif" />
        </form>
    </div>
</div>
    
<div class="clear">&nbsp;</div>
    
</section>
    
    <!-- End of Left column/section -->

    <!-- Right column/section -->

    <?php echo $this->element('menu_configuration'); ?>

    <!-- End of Right column/section -->
    <div class="clear"></div>
    
</div>
<div id="push"></div>
</section>