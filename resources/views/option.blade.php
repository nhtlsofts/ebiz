<html>
	<head>
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge"> 
		<meta name="viewport" content="width=device-width, initial-scale=1"> 
		<title>ReceiptList</title>
		<link rel="stylesheet" type="text/css" href="css/normalize.css" />
		<link rel="stylesheet" type="text/css" href="css/tabs.css" />
		<link rel="stylesheet" type="text/css" href="css/tabstyles.css" />	
		<link rel="stylesheet" type="text/css" href="css/lam.css" />
		<link rel="stylesheet" type="text/css" href="css/component.css" />	
		<link type="text/css" rel="stylesheet" href="https://code.jquery.com/ui/1.10.4/themes/redmond/jquery-ui.css" />
		<link rel="stylesheet" type="text/css" href="css/perfect-scrollbar.css" />
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css">
		<link type="text/css" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jsgrid/1.5.3/jsgrid.min.css" />
		<link type="text/css" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jsgrid/1.5.3/jsgrid-theme.min.css" />
	</head>
    <body>
    	<div class="wrapper">
		    <div class="container">
			    <div class='leftbar'>
			    	<ul class="cbp-vimenu">
						<li><a href="logout" class="icon-logo">Logo</a></li>
						<li><a href="getdata" class="icon-facebook">Archive</a></li>
						<li><a href="receiptlist" class="icon-cart">Search</a></li>
						<li><a href="productlist" class="icon-product">Pencil</a></li>
						<!-- Example for active item:
										<li class="cbp-vicurrent"><a href="#" class="icon-product">Pencil</a></li>
										-->
						<li class="cbp-vicurrent"><a href="customerlist" class="icon-customer">Location</a></li>
						<li><a href="option" class="icon-option">Images</a></li>
						<li><a href="#" class="icon-unseen">Download</a></li>
					</ul>
				</div>
				<div class='receipt'>
			    	<div id="jsGrid"></div>
		    	</div>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-2.2.4.js"></script>
		<script src="https://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
		<script src="https://cdn.socket.io/socket.io-1.3.4.js"></script>
		<script src="js/cbpFWTabs.js"></script>
		<script src="js/modernizr.custom.js"></script>
		<script src="js/perfect-scrollbar.jquery.js"></script>
		<script src="https://code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jsgrid/1.5.3/jsgrid.min.js"></script>  
		<script type="text/javascript" src="js/moment.js"></script>  
		<script>

			$("#jsGrid").jsGrid({
		        width: "100%",
		        height: "100%",
					        
		        autoload: true,
		        sorting: true,
		        paging: true,
            	editing: true,

            	controller: {
	                loadData: function(filter) {
	                    return $.ajax({
	                        type: "GET",
	                        url: "SearchOption",
	                        data: filter
	                    });
	                },
	                updateItem: function(item) {
	                    return $.ajax({
	                        type: "GET",
	                        url: "UpdateOption",
	                        data: item
	                    });
	                },
	                deleteItem: function(item) {
	                    return $.ajax({
	                        type: "GET",
	                        url: "deleteOption",
	                        data: item
	                    });
	                }
	            },
		 		
		        deleteConfirm: "Bạn không có quyền xóa ở trang này.",

		        pageSize: 13,
		        pageButtonCount: 5,		 
		 
		        fields: [
		        	{ name: "pageid", type: "text" , width: 0, editing: false, css: "hide" },	       	
		        	{ name: "pagesname" , title: "Tên trang" , width: 250 , editing: false , type: "text"  },	        	
		        	{ name: "autohideemailandphone", title: "Tự ẩn đoạn hội thoại có chứa số điện thoại hoặc email" , width: 150 , type: "checkbox"  },
		        	{ type: "control" }
		        ]
		    });
		</script>
    </body>    
</html>
