<?php
if(!defined("_DEF_RSF_")) set_error_exit("do not allow access");
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Welcome to ReasonableFramework</title>
		<link href="<?php echo base_url(); ?>vendor/mincss/entireframework.min.css" rel="stylesheet" type="text/css">
		<link href="<?php echo base_url(); ?>vendor/mincss/welcome.css" rel="stylesheet" type="text/css">
	</head>
	<body>
		<nav class="nav" tabindex="-1" onclick="this.focus()">
			<div class="container">
				<a class="pagename current" href="#">ReasonableFramework</a>
				<a href="#">Build</a>
				<a href="#">Legacy</a> 
				<a href="#">Security</a>
			</div>
		</nav>
		<button class="btn-close btn btn-sm">×</button>
		<div class="container">
			<div class="hero">
				<h1>ReasonableFramework</h1>
				<p>ReasonableFramework is RVHM structured PHP Web Framework, Securely. Compatibility.</p>
				<p>Do support lagacy systems (IE 5.5+, PHP 4.4.9+, and more).</p>
				<a class="btn btn-b" href="https://github.com/gnh1201/reasonableframework">Fork me</a>
			</div>
			<div class="row">
				<div class="col c4">
					<h3>Build</h3>
					You can build your ideas quickly with ReasonableFramework.
					<br><a href="https://github.com/gnh1201/reasonableframework" class="btn btn-sm btn-a">Do stuff!</a>
				</div>
				<div class="col c4">
					<h3>Legacy</h3>
					You can integrate legacy system stablely with ReasonableFramework.
					<br><a href="https://github.com/gnh1201/reasonableframework" class="btn btn-sm btn-b">Do stuff!</a>
				</div>
				<div class="col c4">
					<h3>Security</h3>
					You can safe your website easily with ReasonableFramework.
					<br><a href="https://github.com/gnh1201/reasonableframework" class="btn btn-sm btn-c">Do stuff!</a>
				</div>
			</div>
		</div>
		<noscript><div>Javascript not detected</div></noscript>
	</body>
</html>
