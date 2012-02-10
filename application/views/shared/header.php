<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $this->Core->title(); ?></title>

<?php
//  We use these meta tags in the App JS Package {@see assets/js/app.js}
//  Wouldn't recommend changing it.
?>
<meta name="current-uid" content="<?php echo ( logged_in() ) ? current_uid() : "-1"; ?>" />
<meta name="csrf-value" content="<?php echo $this->security->get_csrf_hash(); ?>" />
<meta name="csrf-name" content="<?php echo $this->security->get_csrf_token_name(); ?>" />
<meta name="simple-env" content="<?=strtolower(ENV)?>" />

<!-- Mobile Tags -->
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<meta name="description" content="" />
<meta name="keywords" content="" />

<!-- OpenGraph -->
<meta property="og:title" content="<?=$this->Core->title()?>" />
<meta property="og:type" content="business" />
<meta property="og:url" content="<?=current_url()?>" />

<?php $this->carabiner->display('css'); ?>

<link rel="shortcut icon" href="/favicon.png" />
<!-- <link rel="stylesheet" href="/apple-icon.png" type="text/css" media="screen,projection"> -->

<!-- Google -->
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-000000-00']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
</head>

<body>
<div id="top-of-body"></div>

<!-- Come'on, no Javscript? Really?! -->
<noscript><div id="flash" class="notice-bar alert-message info"><span><strong>Whoops!</strong> We require that you enable Javascript for optimal user experience.</span></div></noscript>

<?php
// If we're displaying a msg
$did = false;

// We save the error into the userdata for a session and the core object
$key_error = $this->session->userdata('current_error');

if ( is_string( $key_error ) && $key_error !== '' )
{
     $did = true;
     
	?><div id="" class="notice-bar alert-message error">
	<span><strong>Whoops!</strong> <?php echo $key_error; ?></span>
	<a href="" class="close">x</a> 
</div><?php

	$this->session->unset_userdata('current_error');
}

// Also saved into the user session data
$key_good =  $this->session->userdata('current_good');
if ( is_string( $key_good ) && $key_good !== '' )
{
     $did = true;
     
	?><div id="flash" class="notice-bar alert-message success">
	<span><?php if ( $key_good !== 'Welcome!' AND $key_good !== 'Logged out - goodbye!' ) { ?><strong>Awesome!</strong> <?php } echo $key_good; ?></span>
	<a href="" class="close">x</a> 
</div><?php

	$this->session->unset_userdata('current_good');		
}
?>

<!-- The Actual Content -->