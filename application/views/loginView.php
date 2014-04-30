<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Welcome to Twitter::Login</title>
	
<link rel="stylesheet" type="text/css" href="<? echo base_url();?>/css/mystyle.css">

	
<script language="javascript">
function loginform(){
	loginForm.submit();
}
</script>

</head>
<body>

<div id="logoID">MEXT Association</div>

<div id="spacerID"></div>

<div id="msgID" style="display:<?php if($message=="NOT_SET"){echo("none");}else{echo("block");}?>">
<?php if(isset($message)){echo($message);}?>
</div>

<div id="spacerID"></div>

<div id="loginFormID">
<?php
//load the form helper class
$this->load->helper('form');

//open the form tab and set action to validate function of the login class
echo form_open('login/verifyLogin',array('name' => 'loginForm')); 

//set the label for the email
echo form_label('Email', 'loginemailID',  array( 'id' => 'loginlabelEmailID'));

//text input type for usename
echo form_input(array('name'        => 'email','id'          => 'loginemailID'));

//set the label for the password
echo form_label('Password', 'loginpasswordID',  array( 'id' => 'loginlabelPassID'));

//text input type for usename
echo form_password(array('name'        => 'password','id'          => 'loginpasswordID'));

//close the form
echo form_close();
?>
<div id="loginloginbuttonID"><a href="javascript:loginform()">Login</a></div>
<br/>
<div id="loginregisterbuttonID"><a href="<?php echo(base_url());?>register">Register</a></div>
<div id="loginforgotpassID"><a href="recover_pass.php">forgot password?</a></div>
</div>

<br/>
<div id="footerID">
</div>

</body>
</html>