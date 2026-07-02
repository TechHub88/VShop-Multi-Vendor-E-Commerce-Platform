<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link rel="shortcut icon" href="images/favicon.png" type="image/x-icon">
      <!-- SITE TITLE -->
      <title>Order Details</title>   
	  <!-- =========================
         STYLESHEETS   
         ============================== -->
		 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.css" />
		  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/rs-spacing-min.css')}}" />
		  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/admin-style.css')}}" />		  
		  <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v7.1.0/css/all.css">
		  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    
</head>
<body>
	<div class="adminBodySec">
	<div class="adminBodySecinner">
	<!-- Main Sidebar -->
    @include('manager.partials.sidebar')
	<!-- Main Content -->
   <div class="adminPagesContainer">
   <!-- Main Topbar -->
    @include('manager.partials.topbar')
   <!-- Main Content -->
    @yield('content')
	</div>	
    
     </div>
	</div>	 

	<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
	  <script src="https://cdn.datatables.net/2.3.4/js/dataTables.js"></script>
	  <script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.js"></script>
      <script src="{{ asset('assets/js/admin-main.js')}}"></script> 
</body>
</html>
