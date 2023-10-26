<?php
require "function.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport"    content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author"      content="Sergey Pozhilov (GetTemplate.com)">
	
	<title>Sign in - Progressus Bootstrap template</title>

	<link rel="shortcut icon" href="assets/images/gt_favicon.png">
	
	<link rel="stylesheet" media="screen" href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,700">
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/font-awesome.min.css">

	<!-- Custom styles for our template -->
	<link rel="stylesheet" href="assets/css/bootstrap-theme.css" media="screen" >
	<link rel="stylesheet" href="assets/css/main.css">

	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	<script src="assets/js/html5shiv.js"></script>
	<script src="assets/js/respond.min.js"></script>
	<![endif]-->
</head>

<body>
	<!-- container -->
	<div class="container">

		<div class="row">
			
			<!-- Article main content -->
			<article class="col-xs-12 maincontent">
				<header class="page-header">
				</header>
				
				<div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
					<div class="panel panel-default">
						<div class="panel-body">
							<h3 class="thin text-center">Sign in to your account</h3>
							<p class="text-center text-muted">Lorem ipsum dolor sit amet, <a href="signup.php">Register</a> adipisicing elit. Quo nulla quibusdam cum doloremque incidunt nemo sunt a tenetur omnis odio. </p>
							<hr>
						
							<?php 
							if (isset($_SESSION['email'])) {
								// Sesuatu yang akan dieksekusi jika sesi 'email' sudah ada
								echo "#";
							} else {
								// Sesuatu yang akan dieksekusi jika sesi 'email' belum ada
								echo " Masukkan Email dan Password";
							}

							if (isset($message)) echo $message."<br/>";
							if (isset($_SESSION['delayto'])) $delay = $_SESSION['delayto'] - time();
							else $delay = 0;

							if ($delay > 0): ?>

							<p>
								Sudah <?php echo $_SESSION['failed'];?> kali gagal.<br/>
								Silakan coba
								<span id="delay"><?php echo $delay;?></span>.
							</p>
							<script type="text/javascript">
								var seconds;
								var temp;
											
								function countdown() {
								seconds = document.getElementById('delay').innerHTML;
								seconds = parseInt(seconds, 10);
											
									if (seconds == 1) {
										temp = document.getElementById('delay');
										temp.innerHTML = "<a href='signin.php'>login</a>";
										return;
									}
												
										seconds--;
										temp = document.getElementById('delay');
										temp.innerHTML = seconds+" detik lagi";
										timeoutMyOswego = setTimeout(countdown, 1000);
								} countdown();
							</script>

							<?php else: ?>

							<form method="post">
								<div class="top-margin">
									<label>Email <span class="text-danger">*</span></label>
									<input type="text" name="email" id="email" class="form-control" placeholder="email@gmail.com">
								</div>
								<div class="top-margin">
									<label>Password <span class="text-danger">*</span></label>
									<input type="password" name="password"  id="password" class="form-control">
								</div>

								<div class="row">
									<div class="col-lg-8">
										<!-- Tambahkan select box untuk memilih role -->
										<label for="role">Role:</label>
										<select name="role">
											<option value="pengguna">Pengguna</option>
											<option value="admin">Admin</option>
											<option value="petugas">Petugas</option>
										</select><br> 
										<input type="checkbox" name="remember_me"> Remember Me<br>
									</div>
									<div class="col-lg-4 text-right">
									
										<input type="hidden" name="level" value="1"> <!-- Level 1 untuk pengguna biasa -->
										<input type="hidden" name="level" value="2"> <!-- Level 2 untuk admin -->
										<button class="btn btn-action" type="submit" name="login">Login</button>
									</div>
								</div>
							</form>										

							<?php endif; ?>
							
						</div>
					</div>

				</div>
				
			</article>
			<!-- /Article -->

		</div>
	</div>	<!-- /container -->
		
	<!-- JavaScript libs are placed at the end of the document so the pages load faster -->
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script src="http://netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
	<script src="assets/js/headroom.min.js"></script>
	<script src="assets/js/jQuery.headroom.min.js"></script>
	<script src="assets/js/template.js"></script>
</body>
</html>