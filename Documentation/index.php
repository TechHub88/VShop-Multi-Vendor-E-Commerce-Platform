<!DOCTYPE html>
<html lang="en">

	<head>
		<!--SEO Meta Tags-->
		<meta charset="utf-8" />
		<!-- SITE TITLE -->
		<title>Dusty - Laravel 11 Admin Dashboard Template</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
		<meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc." />
		<meta name="author" content="Zoyothemes" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />

		<!-- App favicon -->
		<link rel="shortcut icon" href="favicon.ico">

		<!-- Bootstrap Core CSS -->
		<link href="css/bootstrap.min.css" rel="stylesheet">

		<link rel="stylesheet" type="text/css" href="css/prettify.css" />

		<!-- Custom CSS -->
		<link href="css/styles.css" rel="stylesheet">

	</head>

	<body>

		<!-- Begin page -->
		<div id="wrapper">

			<!-- Topbar -->
			<div id="page-topbar">
				<div class="layout-width">
					<div class="navbar-header">
						<h5 class="text-dark fw-semibold">Dusty - Laravel 11 Admin Dashboard Template</h5>
					</div>
				</div><!-- container -->
			</div>

			<!-- Sidebar -->
			<div id="sidebar-wrapper">
				<!-- LOGO -->
				<div class="navbar-brand-box">
					<!-- Dark Logo-->
					<a href="index.html" class="logo">
						<span class="logo-sm">
							<img src="images/logo-dark.png" alt="" height="22">
						</span>
					</a>
				</div>

				<ul class="sidebar-nav">
					<li class="menu-item">
						<a href="index.html">Introduction</a>
					</li>
					<li class="menu-item">
						<a href="getting-started.html">Getting Started</a>
					</li>
					<li class="menu-item">
						<a href="resources-plugins.html">Plugins</a>
					</li>
					<li class="menu-item">
						<a href="support.html">Support</a>
					</li>
					<li class="menu-item">
						<a href="changelog.html">Change Log</a>
					</li>
				</ul>
			</div>
			<!-- /#sidebar-wrapper -->

			<!-- Page Content -->
			<div id="page-content-wrapper">
				<div class="page-content">
					<div class="container-fluid">

						<div class="row">
							<div class="col-lg-12">
								<div class="card border-0 mb-4">
									<div class="card-header">
										<h4 class="card-title fs-18 mb-0">Introducation</h4>
									</div>
									<div class="card-body p-4">
										<p><b>Dusty Laravel</b> is a fully featured premium admin template built on
											top of awesome <b>Bootstrap 5.3.3 and Laravel 11</b>. It's crafted using modern
											web technologies <b>HTML5</b>, <b>SCSS</b>, <b>PHP</b> and
											<b>JavaScript</b>. The theme includes beautiful dashboard, many ready to use
											hand crafted components, widgets, pages, etc. Ideal for Web Apps,
											Dashboards, Admin Panels, and SaaS interfaces, features <b>vite based</b> build
											tools, <b>SCSS variables base</b>, and RTL support.
											Quickly customize layouts and modes using data attributes without extensive
											code changes. Enjoy a beautiful UI with minimal coding effort.
										</p>

										<p>This documentation is guideline for all users who can even be a beginner or experienced web developer.</p>

										<p>If you any f you are having any question or feedback, please feel free to get back to us via email
											<code>zoyothemes@gmail.com</code> or by filling out the contact form on our
											<a href="https://zoyothemes.com/" class="text-primary"
												target="_blank">website</a>.
										</p>
									</div>

								</div>
							</div>
						</div>
						<!-- /#page-content-wrapper -->

						<div class="row">
							<div class="col-lg-12">
								<div class="card border-0">
									<div class="card-header">
										<h4 class="card-title fs-18 mb-0">Structure</h4>
									</div>
									<div class="card-body p-4">
										<p class="fs-14">
											When you extract the zip file you received after purchasing,
											you will find the following files and folders:
										</p>
										<div class="rounded bg-light p-1">
											<pre class="language-html">
                                            <code>
├── Documentation
└── Dusty
    ├── app
    ├── bootstrap
    ├── config
    ├── database
    ├── public
    |   ├── images
    ├── resources
    |   ├── fonts
    |   ├── js
    |   ├── scss
    |   ├── views
    |   |   ├── layout
    |   |   └── multiple folders having all pages, apps, etc
    ├── routes
    ├── storage
    ├── tests
    ├── vendor // composer install this folder
    ├── composer.json - Laravel php dependencies
    ├── package.json - Laravel php dependencies
    ├── vite.config.js - Vite configuration File
    └── yarn.lock
                                            </code>
                                        </pre>
										</div>
									</div>

								</div>
							</div>
						</div>
						<!-- /#page-content-wrapper -->

						<div class="text-center">
							<p class="copy mb-0">
								<script>document.write(new Date().getFullYear())</script> © Dusty Design & Develop by
								Zoyothemes.
							</p>
						</div>
					</div>
				</div>
			</div>
			<!-- end page-content-wrapper -->
		</div>
		<!-- /#wrapper -->

		<!-- jQuery -->
		<script src="js/jquery.js"></script>

		<!-- Bootstrap Core JavaScript -->
		<script src="js/bootstrap.min.js"></script>

		<script src="js/jquery.easing.min.js"></script>

		<script type="text/javascript" src="js/prettify.js"></script>

		<!-- Menu Toggle Script -->
		<script>

			//jQuery for page scrolling feature - requires jQuery Easing plugin
			$(function () {
				$('.sidebar-nav a').bind('click', function (event) {
					var $anchor = $(this);
					$('html, body').stop().animate({
						scrollTop: $($anchor.attr('href')).offset().top - 100
					}, 1500, 'easeInOutExpo');
					event.preventDefault();
				});
			});
		</script>

	</body>

</html>