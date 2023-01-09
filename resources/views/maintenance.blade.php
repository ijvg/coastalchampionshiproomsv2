<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Mode</title>
    <style>


.layout-align-center-vertical {
    -webkit-justify-content: center;
    -moz-justify-content: center;
    -ms-justify-content: center;
    justify-content: center;
    max-height: 100%;
}
.horizontal-align {
    position: relative;
    left: 50%;
    -webkit-transform: translateX(-50%);
    -ms-transform: translateX(-50%);
    transform: translateX(-50%);
}

.width-7, .size-7 {
    width: 392px !important;
    margin-top:100px;
}
.height-7, .size-7 {
    height: 392px !important;
}
.logo-box-primary .logo {
    {{-- color: #fff;
    background-color: #009688 !important; --}}
}
.logo-box, .logo-box .logo {
    display: -webkit-flex;
    display: -moz-flex;
    display: -ms-flexbox;
    display: -ms-flex;
    display: flex;
    -webkit-justify-content: center;
    -moz-justify-content: center;
    -ms-justify-content: center;
    justify-content: center;
    -webkit-align-items: center;
    -moz-align-items: center;
    -ms-align-items: center;
    align-items: center;
    -webkit-align-content: center;
    -moz-align-content: center;
    -ms-align-content: center;
    align-content: center;
}
.logo-box .logo {
    {{-- width: 96px;
    height: 96px;
    font-size: 72px;
    font-weight: 500;
    color: #fff;
    background-color: #777e7d;
    -webkit-border-radius: 2px;
    -moz-border-radius: 2px;
    border-radius: 2px;
    -moz-background-clip: padding;
    -webkit-background-clip: padding-box;
    background-clip: padding-box;
    align-content: center; --}}
}

    </style>
    
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
</head>
	<body>
		<div class="full-size layout-column layout-align-center-vertical bootstrap snippets bootdeys">
			<div class="size-7 horizontal-align">
				<div class="panel panel-default">
					<div class="panel-body text-center">
						<div class="card">
							
							
							<div class="logo-box logo-box-primary padding-top-4">
								<div class="logo">
									<img src="{{ asset('/storage/images/ccrLogo.png' ) }}" height="75"/>
								</div>
							</div>
							
							<div class="card-body">
						
						
								<h2>Website under construction, update coming soon.</h2>
								
							</div>
								
							<div class="card-footer">
								<p>If you need immediate help, please contact us</p>
								<p>Email: <a href="mailto:marisa@ccrooms.com">marisa@ccrooms.com</a></p>
								<p>Phone: <a href="tel:757-937-1372">757-937-1372</a></p>
							</div>
								
							
						</div>
					</div>
				</div>	
			</div>
		</div>
	</body>
</html>