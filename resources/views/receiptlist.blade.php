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
						<li class="cbp-vicurrent"><a href="receiptlist" class="icon-cart">Search</a></li>
						<li><a href="productlist" class="icon-product">Pencil</a></li>
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
		 	var DateField = function(config) {
			    jsGrid.Field.call(this, config);
			};

			DateField.prototype = new jsGrid.Field({
			    sorter: function(date1, date2) {
			        return new Date(date1) - new Date(date2);
			    },    

			    itemTemplate: function(value) {
			    	return moment(new Date(value)).format('DD-MM-YYYY HH:mm:ss');
			    },

			    filterTemplate: function() {
			        var now = new Date();
			        this._fromPicker = $("<input>").datepicker({ dateFormat: 'yy-mm-dd',defaultDate: now.setFullYear(now.getFullYear() - 1) });
			        this._toPicker = $("<input>").datepicker({ dateFormat: 'yy-mm-dd',defaultDate: now.setFullYear(now.getFullYear() + 1) });
			        return $("<div>").append(this._fromPicker).append(this._toPicker);
			    },

			    insertTemplate: function(value) {
			        return this._insertPicker = $("<input>").datepicker({ defaultDate: new Date() });
			    },

			    editTemplate: function(value) {
			        return this._editPicker = $("<input>").datepicker().datepicker("setDate", new Date(value));
			    },

			    insertValue: function() {
			        return this._insertPicker.datepicker("getDate").toISOString();
			    },

			    editValue: function() {
			        return this._editPicker.datepicker("getDate").toISOString();
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
		 
		        pageSize: 11,
		        pageButtonCount: 5,
		 
		        deleteConfirm: "Bạn chắc muốn xóa phiếu này chứ ?",

		 		rowClick: function(args) {
				    var item = $(args.event.target).closest("tr");

				    if(this._clicked_row != null) {
				        this._clicked_row.removeClass('jsgrid-clicked-row');
				    }
				    this._clicked_row = item;
				    window.location.href="http://"+$(location).attr('host')+"/laravel/public/receiptdetail?receipt="+item.find('.jsgrid-cell.hide').text();
				},

		        controller: {
	                loadData: function(filter) {
	                    return $.ajax({
	                        type: "GET",
	                        url: "searchReceiptList",
	                        data: filter
	                    });
	                },
	                deleteItem: function(item) {
	                    return $.ajax({
	                        type: "GET",
	                        url: "DeleteReceiptList",
	                        data: item
	                    });
	                }
	            },
		 
		        fields: [
		        	{ name: "id", type: "text" , width: 0, css: "hide" },
 					{ title: "Ngày", type: "date", width: 150, name: "issue_date" },		            
		            //{ title: "address", type: "text", items: countries, valueField: "Id", textField: "title" },
		            { title: "Mã phiếu", type: "text" , width: 50, name: "code" },
		            { title: "Tên khách hàng", type: "text" , name: "customername" },
		            { title: "SĐT", type: "text", name: "tel" } ,
		            { title: "Địa chỉ", type: "text", name: "customeradd" },
		            { title: "Quận/Huyện", type: "text", name: "dname" },
		            { title: "Tỉnh/Thành phố", type: "text", name: "pname" },
		            { title: "Phí Ship", type: "number", name: "shipcost" },
		            { title: "Tổng Giảm", type: "number", name: "totaldiscount" },
		            { title: "Tổng tiền", type: "number", name: "total" },		            
		            { type: "control" }
		        ]
		    });
		</script>
    </body>    
</html>
