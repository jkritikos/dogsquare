<?php
$name = $this->Session->read('name');
$role = $this->Session->read('role');

//active tab
$classConfiguration = "";
$classUsers = "";
$classReports = "";
$classDashboard = "";

$this->log("header.ctp active tab is $activeTab", LOG_DEBUG);

if($activeTab == 'configurations'){
    $classConfiguration = "class=\"active\"";
    $classUsers = "";
    $classReports = "";
    $classDashboard = "";
} else if($activeTab == 'users'){
    $classConfiguration = "";
    $classUsers = "class=\"active\"";
    $classReports = "";
    $classDashboard = "";
} else if($activeTab == 'reports'){
    $classConfiguration = "";
    $classUsers = "";
    $classReports = "class=\"active\"";
    $classDashboard = "";
} else if($activeTab == "dashboard"){
    $classConfiguration = "";
    $classUsers = "";
    $classDashboard = "class=\"active\"";
    $classReports = "";
}

?>
<header id="page-header">
    <div class="wrapper">
        <div id="util-nav">
            <ul>
                <li>Connected as: <?php echo $name; ?></li>
		<li><a href="/users/profile">My Profile</a></li>
                <li><a href="/users/logout">Logout</a></li>
            </ul>
        </div>

        <img height="36" src="/img/logo.png"/>
        <h1>Dogsquare</h1>

        <div id="main-nav">
            <ul class="clearfix">
                <li <?php echo $classDashboard; ?> ><a href="/scores/">Dashboard</a></li>
                <li <?php echo $classConfiguration; ?> ><a href="/configurations">Configuration</a></li>
		
                
                <?php
                if(in_array(ROLE_ADMIN, $role)){
                
                    ?>
                    <li <?php echo $classUsers; ?> ><a href="/users">Users</a></li>
                    <li <?php echo $classReports; ?> ><a href="/reports">Reports</a></li>
                    <?php
                }
                ?>

            </ul>
        </div>
    </div>
    <div id="page-subheader">
        <div class="wrapper">
            <h2><?php echo $headerTitle; ?></h2>
                
        </div>
    </div>
</header>