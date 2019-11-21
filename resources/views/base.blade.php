<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
  	<link href="{{ asset('css/app.css') }}" rel="stylesheet">
  	<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<style>
		.content {
			display: flex;
			justify-content: center;
			align-items: center;
			height: 100vh;
			width: 100vw;
		}
	</style>
</head>
<body>
	<div class="content">
		@yield('content')
	</div>
</body>
</html>