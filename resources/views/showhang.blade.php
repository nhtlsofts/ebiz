

<html>
	<head>
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge"> 
		<meta name="viewport" content="width=device-width, initial-scale=1"> 
		<title>Main</title>
		<link rel="shortcut icon" href="../favicon.ico">
		<link rel="stylesheet" type="text/css" href="css/normalize.css" />
		<link rel="stylesheet" type="text/css" href="css/tabs.css" />
		<link rel="stylesheet" type="text/css" href="css/tabstyles.css" />	
		<link rel="stylesheet" type="text/css" href="css/lam.css" />
		<link rel="stylesheet" type="text/css" href="css/pe-icon-7-stroke.css" />  	
		<script type="text/javascript">
    		<?php echo "var pagekey = '".Session::get('page_key')."';" ?>
		</script>	
	</head>
    <body>
	    <div class="wrapper">
		    <div class="container">
		        <div class="left">
		            <section>
					<div class="tabs tabs-style-underline">
						<div class="top">
						<nav>
							<ul>
								<li><a href="#section-underline-1" class="icon icon-comment"><span>Comments</span></a></li>
								<li><a href="#section-underline-2" class="icon icon-chat"><span>Messages</span></a></li>
							</ul>
						</nav>
						</div>
						<div class="content-wrap">
							<section id="section-underline-1">							
							<div class="people">
								@foreach ($manages as $manage)
									@if ( $manage->type == 'comment' )
									<div class="person" data-chat={{$manage->oid}} id={{$manage->oid}}>						
										<img src="https://s13.postimg.org/ih41k9tqr/img1.jpg" alt="" />
										<span class="name">{{$manage->sender_name}}</span>
					                    <span class="time preview">{{$manage->created_at}}</span>
					                    <span class="preview">{{$manage->comments}}</span>	
					                </div>
									@endif
								@endforeach													
							</div>
							</section>
							<section id="section-underline-2">
							<div class="people">
								@foreach ($manages as $manage)
									@if ( $manage->type == 'message' )
								    <div class="person" data-chat={{$manage->oid}} id={{$manage->oid}}>								
										<img src="https://s13.postimg.org/ih41k9tqr/img1.jpg" alt="" />
										<span class="name">{{$manage->sender_name}}</span>
					                    <span class="time preview">{{$manage->created_at}}</span>
					                    <span class="preview ">{{$manage->comments}}</span>
					                </div>
									@endif
								@endforeach																				
							</div>
							</section>
						</div><!-- /content -->
					</div><!-- /tabs -->
					</section>
		        </div>
		        <div class="right">
		            <div class="top"><span><span class="name"></span></span></div>
		            <div class="chat" >
		                <!--<div class="bubble you">
		                <div class="bubble me">-->
		            </div>
		            <div class="write">
		                <a href="javascript:;" class="write-link attach"></a>
		                <input type="text" />
		                <a href="javascript:;" class="write-link smiley"></a>
		                <a href="javascript:;" class="write-link send"></a>
		            </div>
		        </div>
		        <div class='right_bar'>
		        </div>
		    </div>
		</div>
		<input type="file" id="selectedFile" style="display: none;" accept="image/x-png,image/gif,image/jpeg"/>                          
	    <canvas id="canvas" width="300" height="300" style="display: none;">
	    </canvas>
		<!--
		<script src="https://a8d7b7c3.ngrok.io/autobahn.js"></script>
		-->
		<script src="https://code.jquery.com/jquery-2.2.4.js"></script>
		<script src="https://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
		<script src="https://cdn.socket.io/socket.io-1.3.4.js"></script>
		<script src="js/cbpFWTabs.js"></script>
		<script src="js/modernizr.custom.js"></script>
		<script src="js/lam.js"></script>
    </body>    
</html>


