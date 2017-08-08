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
			<?php
				echo "var province = \"{'province':" . str_replace("\"","'",json_encode($province)) . "}\";";
    			echo "province = province.replace(/'/g, '\"');";
    			echo 'var P = JSON.parse(province);';
    			echo "var district = \"{'district':" . str_replace("\"","'",json_encode($district)) . "}\";";
    			echo "district = district.replace(/'/g, '\"');";
    			echo 'var D = JSON.parse(district);';
    		?>
    		D.district.unshift({ id: "0", name: "" });
    		P.province.unshift({ id: "0", name: "" });

    		var DateField = function(config) {
			    jsGrid.Field.call(this, config);
			};

			DateField.prototype = new jsGrid.Field({
			    sorter: function(date1, date2) {
			        return new Date(date1) - new Date(date2);
			    },    

			    itemTemplate: function(value) {
			    	if ( value != null ){
			    		return moment(new Date(value)).format('DD-MM-YYYY HH:mm:ss');
			    	}
			    },

			    filterTemplate: function() {
			        var now = new Date();
			        this._fromPicker = $("<input>").datepicker({ dateFormat: 'yy-mm-dd',defaultDate: now.setFullYear(now.getFullYear() - 1) });
			        this._toPicker = $("<input>").datepicker({ dateFormat: 'yy-mm-dd',defaultDate: now.setFullYear(now.getFullYear() + 1) });
			        return $("<div>").append(this._fromPicker).append(this._toPicker);
			    },

			    filterValue: function() {
			        return {
			            from: $(this)[0]._fromPicker.datepicker("option", "dateFormat", "yy-mm-dd" ).val(),
			            to: $(this)[0]._toPicker.datepicker("option", "dateFormat", "yy-mm-dd" ).val()
			        };
			    }
			});

			jsGrid.fields.date = DateField;

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
	                        url: "searchCustomerList",
	                        data: filter
	                    });
	                },
	                deleteItem: function(item) {
	                    return $.ajax({
	                        type: "GET",
	                        url: "DeleteCustomerList",
	                        data: item
	                    });
	                },
	                insertItem: function(item) {
	                    return $.ajax({
	                        type: "GET",
	                        url: "InsertCustomerList",
	                        data: item
	                    });
	                },
	                updateItem: function(item) {
	                    return $.ajax({
	                        type: "GET",
	                        url: "UpdateCustomerList",
	                        data: item
	                    });
	                }
	            },
		 
		        fields: [
		        	{ name: "oid", type: "text" , width: 0, css: "hide" },		        	
		        	{ name: "fbid", type: "text" , width: 0, css: "hide" },	        	
		        	{ name: "facebookuser", type: "text" , width: 0, css: "hide" },
 					{ title: "Tên", type: "text", width: 150, name: "name" },		            
		            { title: "Email", type: "text" , width: 150, name: "email" },
		            { title: "SĐT", type: "text", name: "tel" } ,
		            { title: "Địa chỉ", type: "text", name: "address" },
		            { title: "Quận/Huyện", type: "select", name: "district" , items: D.district, valueField: "id", textField: "name"  },
		            { title: "Tỉnh/Thành phố", type: "select", name: "province" , items: P.province, valueField: "id", textField: "name"  }, 
		            { title: "Cấm", type: "checkbox", name: "banned" },
		            { title: "Ngày đã cấm", type: "date", name: "banned_date", editing: false  },           
		            { type: "control" }
		        ]
		    });
		</script>
    </body>    
</html>
