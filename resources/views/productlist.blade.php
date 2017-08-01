<html>
	<head>
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge"> 
		<meta name="viewport" content="width=device-width, initial-scale=1"> 
		<title>ProductList</title>
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
						<li ><a href="getdata" class="icon-facebook">Archive</a></li>
						<li><a href="receiptlist" class="icon-cart">Search</a></li>
						<li class="cbp-vicurrent"><a href="productlist" class="icon-product">Pencil</a></li>
						<!-- Example for active item:
										<li class="cbp-vicurrent"><a href="#" class="icon-product">Pencil</a></li>
										-->
						<li><a href="customerlist" class="icon-customer">Location</a></li>
						<li><a href="option" class="icon-option">Images</a></li>
						<li><a href="#" class="icon-download">Download</a></li>
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
		 
		       	filtering: true,
		        sorting: true,
		        paging: true,
		        autoload: true,
		        inserting: true,
            	editing: true,
		 
		        pageSize: 13,
		        pageButtonCount: 5,
		 
		        deleteConfirm: "Do you really want to delete the client?",
		 
		        controller: {
	                loadData: function(filter) {
	                    return $.ajax({
	                        type: "GET",
	                        url: "searchProductList",
	                        data: filter
	                    });
	                },
	                deleteItem: function(item) {
	                    return $.ajax({
	                        type: "GET",
	                        url: "DeleteProductList",
	                        data: item,
	                        error: function(e){
	                        	alert('Thao tác lỗi, kiểm tra lại.');
	                        }
	                    });
	                },
	                insertItem: function(item) {
	                    return $.ajax({
	                        type: "GET",
	                        url: "InsertProductList",
	                        data: item,
	                        error: function(e){
	                        	alert('Thao tác lỗi, kiểm tra lại.');
	                        }
	                    });
	                },
	                updateItem: function(item) {
	                    return $.ajax({
	                        type: "GET",
	                        url: "UpdateProductList",
	                        data: item,
	                        error: function(e){
	                        	alert('Thao tác lỗi, kiểm tra lại.');
	                        }
	                    });
	                }
	            },
		 
		        fields: [
		        	{ name: "id", type: "text" , width: 0, css: "hide" },
 					{ title: "Mã mặt hàng", type: "text",width: 150, name: "product_code" },		            
		            //{ title: "address", type: "text", items: countries, valueField: "Id", textField: "title" },
		            { title: "Tên mặt hàng", type: "text" ,width: 150, name: "product_name" },
		            { title: "Giá", type: "number" ,width: 100, name: "price" },
		            { title: "Đơn vị tính", type: "text",width: 50, name: "unit" } ,
		            { type: "text", name: "picture" , width: 0, css: "hide" },	            
		            { type: "control" }
		        ]
		    });
		</script>
    </body>    
</html>
