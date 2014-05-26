<script type="text/javascript"src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCytXV60HGiJyfNucScCd98poZ73bVfmHo&sensor=false"></script>
<script type="text/javascript">
  function initialize() {
      
      var coordinates = [];
      
      <?php
      foreach($activity_coordinates as $i){
          $lat = $i['latitude'];
          $lon = $i['longitude'];
          
          ?>
          var obj = new google.maps.LatLng(<?php echo $lat; ?>, <?php echo $lon; ?>);
          coordinates.push(obj);
          <?php
      }
      ?>
   
    var mapOptions = {
        zoom:17,
      center: new google.maps.LatLng(<?php echo $activity_coordinates[0]['latitude']; ?>, <?php echo $activity_coordinates[0]['longitude']; ?>)
    };
    var map = new google.maps.Map(document.getElementById("map-canvas"),
        mapOptions);
        
    var activityPath = new google.maps.Polyline({
    path: coordinates,
    geodesic: true,
    strokeColor: '#FF0000',
    strokeOpacity: 1.0,
    strokeWeight: 2
  });

  activityPath.setMap(map);
    
        
  }
  google.maps.event.addDomListener(window, 'load', initialize);
</script>
<script>
$(document).ready(function(){
    initialize();
   
});
</script>

<section id="content">
    <div class="wrapper">

        <!-- Left column/section -->
        <section class="grid_6 first">
            <div class="columns leading">

                <div class="columns">
                <div class="grid_6 first">
                    <h3>Activity details:</h3>				
                            <hr />    	
                    <form id="form" class="form panel" method="post" action="/users/search">
                        
                        <fieldset>
                            <div class="clearfix">
                                <label>Name</label>
                                <input id="nameField" type="text" name="data[User][name]" minlength="3"/>
                            </div>
                            <div class="clearfix">
                                <label>Email</label>
                                <input id="emailField" type="text" name="data[User][email]" minlength="3"/>
                            </div>
                            
                            <div class="clearfix">
                                <label>Registration date from</label>
                                <input id="fromDate" type="date" name="data[User][created_from]" minlength="3"/>
                            </div>
                            <div class="clearfix">
                                <label>Registration date to</label>
                                <input id="toDate" type="date" name="data[User][created_to]" minlength="3"/>
                            </div>
                            <div class="clearfix">
                                <label>Status</label>
                                <select id="statusField" name="data[User][active]">
                                    <option selected value="">Please select</option>
                                    <option value="1">Active</option>
                                    <option value="0">Deactive</option>
                                </select>
                            </div>
                        </fieldset>
                        <span id="errorMsg" style="display:none"><b><font color="red">You must specify at least one of the criteria.</font></b></span>
                        
                    </form>
               </div>
                    </div>

                    <div class="columns leading">
                        <div class="grid_6 first">
                            <h3>Participating dogs:</h3>				
                            <hr />
                            
                            <table class="paginate sortable full">
                            <thead>
                                <tr>
                                    <th align="left">Name</th>
                                    <th align="left">Playtime</th>					
                                    <th align="left">Distance</th>
                                    <th align="left">Dogfuel</th>
                                </tr>
                            </thead>
                            <tbody>
	                    	
                            <?php

                            foreach($activity_dogs as $r => $data){
                                $name = $data['Dog']['name'];
                                $playtime = $data['Dog']['playtime'];
                                $walk_distance = $data['Dog']['distance'];
                                $dogfuel = $data['Dog']['dogfuel'];
                                
                                $id = $data['Dog']['id'];
                                $dogLink = "/dogs/edit/$id";
                                ?>
                                <tr>
                                    <td><a href="<?php echo $dogLink; ?>"><?php echo $name; ?></a></td>
                                    <td><?php echo $playtime; ?></td>
                                    <td><?php echo $walk_distance; ?></td>
                                    <td><?php echo $dogfuel; ?></td>
                                </tr>	                    		
                                <?php	                    	
                            }
	                    
	                    ?>
	                    
	                    </tbody>
                            </table>
	                    
                        </div>
                    </div>
                        
                    <div class="columns leading">
                        <div class="grid_6 first">
                            <h3>Location:</h3>				
                            <hr />
                            
                            <div style="width:760px; height:380px;" id="map-canvas"></div>
	                    
                        </div>
                    </div>    
                        
                    </div>
                    
                    <div class="clear">&nbsp;</div>

                </section>

        <!-- End of Left column/section -->

        <!-- Right column/section -->
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
        </aside>

        <!-- End of Right column/section -->
        <div class="clear"></div>

    </div>
    <div id="push"></div>
</section>