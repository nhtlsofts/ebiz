<html>
	<head>
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge"> 
		<meta name="viewport" content="width=device-width, initial-scale=1"> 
		<title>Main</title>
		<link rel="shortcut icon" href="../favicon.ico">
		<link rel="stylesheet" type="text/css" href="../css/normalize.css" />
		<link rel="stylesheet" type="text/css" href="../css/tabs.css" />
		<link rel="stylesheet" type="text/css" href="../css/tabstyles.css" />	
		<link rel="stylesheet" type="text/css" href="../css/lam.css" />
	</head>
    <body>
    	<div class="wrapper">
		    <div class="container">
		        <div class="left">
			</div>
			<div class="right">
	            <div class="top"><span><span class="name">Chọn fanpage để quản lý</span></span></div>
	            <div class="chat active-chat" >
		            @if (count($pages) > 0 )
					  	@foreach ($pages as $page) 
					  		<div style="display: inline;">
					  			<div style="width: 90%; float: left;">{{$page->pagesname}}</div>  
					      		<input class='check' style='width: 10% ;display: inline; float: left' type="checkbox" @if ( $page->isactive == true ) checked @endif name='key[]' value="{{$page->pagesid}}">
					      	</div>
						@endforeach
					@endif
				  <a type="button" class="fakebutton" href='/laravel/public/getdata' >Bắt đầu quản lý</a>
	            </div>
            </div>
        </div>     
    </body>
	<script src="https://code.jquery.com/jquery-2.2.4.js"></script>
    <script type="text/javascript">
    	$('.check').on('click', function(){
    		$.ajax({
            url: "/laravel/public/regis",
            type: 'GET',
            cache: false,
            data: 'oid=' + $(this).val() + '&check=' + $(this).prop('checked'),
            success: function(getData) {
                alert('Xử lý thành công trang '+$(this).val());
            },
            error: function(getData) {
                    alert(getData);
                }
        });
    	});
    </script>    
</html>