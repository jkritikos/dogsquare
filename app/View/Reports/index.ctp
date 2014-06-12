<script type="text/javascript" src="/js/highcharts.js"></script>
<!--<script type="text/javascript" src="/js/exporting.js"></script>-->
<script type="text/javascript">
$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'chartTimeline',
                zoomType: 'x',
                spacingRight: 20
            },
            title: {
                text: ''
            },
            subtitle: {
                text: document.ontouchstart === undefined ?
                    'Click and drag in the plot area to zoom in' :
                    'Drag your finger over the plot to zoom in'
            },
            xAxis: {
                type: 'datetime',
                maxZoom: 14 * 24 * 3600000, // fourteen days
                title: {
                    text: null
                }
            },
            yAxis: {
                title: {
                    text: 'New users per day'
                },
                showFirstLabel: false
            },
            tooltip: {
                shared: true
            },
            legend: {
                enabled: false
            },
            plotOptions: {
                area: {
                    fillColor: {
                        linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1},
                        stops: [
                            [0, Highcharts.getOptions().colors[0]],
                            [1, 'rgba(12,0,0,0)']
                        ]
                    },
                    lineWidth: 1,
                    marker: {
                        enabled: false,
                        states: {
                            hover: {
                                enabled: true,
                                radius: 5
                            }
                        }
                    },
                    shadow: false,
                    states: {
                        hover: {
                            lineWidth: 1
                        }
                    },
                    threshold: null
                }
            },
    
            series: [{
                type: 'area',
                name: 'New users per day',
                pointInterval: 24 * 3600 * 1000,
                //pointStart: Date.UTC(2013, 11, 27),
                data: [
                    <?php
                    foreach($dailyUsers as $key => $values){
                        $year = substr($values["date"], 0, 4);
                        $month = substr($values["date"], 5, 2);
                        $month = $month - 1;
                        $day = substr($values["date"], 8, 2);
                        $cnt = $values["cnt"];
                        
                        echo "[Date.UTC($year, $month, $day),$cnt],";
                        
                    }
                    ?>
                    
                ]
            }]
        });
    });
    
});
</script>
<section id="content">
    
    <div class="wrapper">
    <!-- Main Section -->

        <section class="grid_6 first">
            <div class="columns leading">
                <div class="grid_3 first">
                    <h3>Today (<?php date_default_timezone_set('Europe/Athens'); echo date("d/m/Y");?>)</h3>
                    <hr/>
                    <table class="no-style full">
                        <tbody>
                            <tr>
                                <td>New users</td>
                                <td class="ar"><?php echo $todayUsers; ?></td>
                            </tr>
                            <tr>
                                <td>Activities</td>
                                <td class="ar"><?php echo $todayActivities;?></td>
                            </tr>
                            <tr>
                                <td>Dogs</td>
                                <td class="ar"><?php echo $todayDogs;?></td>
                            </tr>
                            <tr>
                                <td>Places</td>
                                <td class="ar"><?php echo $todayPlaces;?></td>
                            </tr>
                            <tr>
                                <td>Checkins</td>
                                <td class="ar"><?php echo $todayCheckins;?></td>
                            </tr>
                            <tr>
                                <td>Photos</td>
                                <td class="ar"><?php echo $todayPhotos;?></td>
                            </tr>
                        </tbody>

                    </table>

                </div>

                <div class="grid_3">

                    <h3>Total</h3>
                    <hr/>
                    <table class="no-style full">

                        <tbody>
                            <tr>
                                <td>Users</td>        
                                <td class="ar"><?php echo $totalUsers; ?></td>
                            </tr>
                            <tr>
                                <td>Activities</td>
                                <td class="ar"><?php echo $totalActivities;?></td>
                            </tr>
                            <tr>
                                <td>Dogs</td>
                                <td class="ar"><?php echo $totalDogs;?></td>
                            </tr>
                            <tr>
                                <td>Places</td>
                                <td class="ar"><?php echo $totalPlaces;?></td>
                            </tr>
                            <tr>
                                <td>Checkins</td>
                                <td class="ar"><?php echo $totalCheckins;?></td>
                            </tr>
                            <tr>
                                <td>Photos</td>
                                <td class="ar"><?php echo $totalPhotos;?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="clear">&nbsp;</div>
                <h3>New users over time</h3>
                <hr/>
                <div id="chartTimeline" class="grid_6 first" style="height:400px">   	     
                </div>
            </div>
            
            
            
            <div class="clear">&nbsp;</div>

        </section>

        <!-- Main Section End -->

         <?php
         echo $this->element('menu_report');
         ?>
	
        <div class="clear"></div>

    </div>
    <div id="push"></div>
</section>