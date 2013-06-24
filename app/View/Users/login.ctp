<?php
if(!isset($redirectUrl)){
    $redirectUrl = "/users/index";
}

//Cookie data
if(isset($email)){	
    $userEmail = $email;
    $rememberMeChecked = "checked";
} else {	
    $userEmail = "";
    $rememberMeChecked = "";
}

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico"/>
<title>Dogsquare</title>

<link rel="stylesheet" media="screen" href="/css/reset.css" />
<link rel="stylesheet" media="screen" href="/css/style.css" />
<link rel="stylesheet" media="screen" href="/css/messages.css" />
<link rel="stylesheet" media="screen" href="/css/forms.css" />

<script src="/js/html5.js"></script>
<!-- jquerytools -->
<script src="/js/jquery.tools.min.js"></script>
<!--[if lte IE 9]>
<link rel="stylesheet" media="screen" href="/css/ie.css" />
<script type="text/javascript" src="/js/ie.js"></script>
<![endif]-->
<!--[if IE 8]>
<link rel="stylesheet" media="screen" href="/css/ie8.css" />
<![endif]-->

<script>
	$(document).ready(function(){
    $("#form").validator({
    	position: 'top',
    	offset: [25, 10],
    	messageClass:'form-error',
    	message: '<div><em/></div>' // em element is the arrow
    });
});
</script>


</head>
<body class="login">
    <div class="login-box">
    	<img src="/img/logo.png" />
       
    	<div class="login-box-top">
    		<div class="login-hd">Login</div>
    		
    		<?php
    		//show error message
    		if(isset($errorMsg)){
    			?>
			<div class="message error"><?php echo $errorMsg; ?></div>
    			<?php
    		}
    		?>    		
    		<form id="form"  action="/users/login" method="post">
    		<input type="hidden" name="data[User][redirectUrl]" value="<?php echo $redirectUrl; ?>"/>
                    <p>
                        <label for="username">Email: *</label><br/>
                        <input type="text" id="username"  class="full" value="<?php echo $userEmail; ?>" name="data[User][email]" required="required" />
                    </p>
                    <p>
                        <label for="password">Password: *</label><br/>
                        <input type="password" id="password" class="full" value="" name="data[User][password]" required="required"/>
                    </p>			
                    <p>
                        <input type="checkbox" <?php echo $rememberMeChecked; ?> id="remember" class="" value="1" name="data[User][remember_me]"/>
                        <label class="choice" for="remember">Remember me?</label>
                    </p>
                    <p>
                        <button class="button button-gray" type="submit">Login</button>
                    </p>
		</form>
    		<div id="linebreak"></div>
		<a href="/users/resetPassword">Forgot your password?</a>
    	</div>
	</div>
</body>
</html>