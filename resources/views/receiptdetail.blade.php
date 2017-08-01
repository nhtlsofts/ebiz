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
		<link rel="stylesheet" type="text/css" href="css/perfect-scrollbar.css" />
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css">
		<script type="text/javascript">
    		<?php 
    			$_SESSION = Session::all();
    			echo "var keylist=[];";
    			foreach ($_SESSION as $key=>$val){
    				if (strpos($key,'ages')){
    					echo "keylist['" . str_replace(' ','',$key). "'] = '".$val."';";
    				}
    				if ($key == 'user_id'){
    					echo "var user_id = '".$val."';";
    				}
    			}
    			echo "var province = \"{'province':" . str_replace("\"","'",json_encode($province)) . "}\";";
    			echo "province = province.replace(/'/g, '\"');";
    			echo 'var P = JSON.parse(province);';
    			echo "var district = \"{'district':" . str_replace("\"","'",json_encode($district)) . "}\";";
    			echo "district = district.replace(/'/g, '\"');";
    			echo 'var PL = JSON.parse(district);';
    		?>
		</script>	
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
				<div class='receiptdetail'>
		        	<div class="container_X">
					  	<form id='form' action="save" method="get">
						    <div class="row">
						      	<h4>Thông tin khách hàng</h4>
						      	<input style="display: none;" name='facebookuser' id='facebookuser' value='{{$receipt->facebookuser}}'>
						      	<input style="display: none;" name='Salepage' id='Salepage' value='{{$receipt->page_id}}'>				      	
						      	<input style="display: none;" name='Cus_id' id='Cus_id' value='{{$receipt->customer_id}}'>			      	
						      	<input style="display: none;" name='Cus_fbid' id='Cus_fbid' value='{{$receipt->fbid}}''>

						      	<input style="display: none;" name='customer_code' id='customer_code' value='{{$receipt->customer_code}}''>	
								
								<input style="display: none;" name='code' id='code' value='{{$receipt->receipt_code}}''>

								<input style="display: none;" name='id' id='id' value='{{$receipt->id}}''>

						      	<div class="input-group input-group-icon">
						      		<select style="width: 452px;" class="customer_select"><option></option></select>		
					        	</div>
						      	<div id='customer-name' class="input-group input-group-icon">	
						        	<input name="name" type="text" class="customer_name" value='{{$receipt->name}}' placeholder="Họ tên">
						        	<div class="input-icon name-icon"><i class="fa fa-user"></i></div>			
						        	<select name='province' id='province' class="province"></select>
						      	</div>
						      	<div id='district-' class="input-group input-group-icon">
							        <input name='tel'  type="tel" class='customer_tel' value="{{$receipt->tel}}" placeholder="Số điện thoại">
							        <div class="input-icon phone-icon"><i class="fa fa-envelope"></i></div>
							        <select name='district' id='district' class="district"></select>
						      	</div>
						      	<div id='email-' class="input-group input-group-icon">
							        <input name='email' class='customer_email' value="{{$receipt->email}}" type="email" placeholder="Email">
							        <div class="input-icon email-icon"><i class="fa fa-key"></i></div>
						      	</div>
						      	<div id='customer_add-' class="input-group input-group-icon">
							        <textarea name='address' class='customer_add' placeholder="Địa chỉ" cols="40" rows="2">{{$receipt->address}}</textarea>
							        <div class="input-icon add-icon"><i class="fa fa-key"></i></div>
						      	</div>
						    </div>
						    <div class="row">
						    	<h4>Mặt hàng</h4>
						    	<div class="shopping-cart">

								  	<div class="column-labels">
									    <!--<label class="product-image">Ảnh</label>-->
									    <label class="product-detailsdetail">Sản phẩm</label>
									    <label class="product-unit">ĐVT</label>
									    <label class="product-pricedetail">Giá</label>
									    <label class="product-quantitydetail">SL</label>
									    <label class="product-removaldetail">Xóa</label>
									    <label class="product-line-price-titlepricedetail">Thành tiền</label>
								  	</div>
								  	<div class='product_list'>
								  	@foreach ($data as $attributeKey => $one)
								  		<div class="product">
								  			<div style="display: none;">
								  				<input name='oid[{{$attributeKey}}]' type="" value='{{$one->id}}'></div>
								    		<div class="product-image">
								    		</div>
								    		<div class="product-detailsdetail">
										    	<div class="product-title">
										    	 	<select name='product[{{$attributeKey}}]' class="product-selectdetail">
										    	 		<option value='{"name":"{{$one->product_name}}","product_code":"{{$one->product}}","id":"{{$one->id}}","unit":"{{$one->unit}}"}' selected>{{$one->product_name}}</option>
										    	 	</select>
										    	</div>
									      		<!--<p class="product-description"></p>-->
									    	</div>
									    	<div class="product-unit">
									    		<input name='unit[{{$attributeKey}}]' type="text" value='{{$one->unit}}' disabled >
									    	</div>
									    	<div class="product-pricedetail">
									    		<input name='price[{{$attributeKey}}]' type="number" value="{{$one->price}}" min="0">
									    	</div>
										    <div class="product-quantitydetail">
										      	<input name='quantity[{{$attributeKey}}]'  type="number" value="{{$one->quanlity}}" min="0">
										    </div>
										    <div class="product-removaldetail">
										      	<input type="button" class="remove-productdetail" value="-">
										    </div>
										    <div class='line-amount'>
										    	<input name='amount[{{$attributeKey}}]' value={{$one->amount}} type="number" style="display: none;" >
									    	</div>
									    	<div name='amount[{{$attributeKey}}]' class="product-line-pricedetail">{{$one->amount}}</div>
								    	</div>
								    @endforeach
							    	</div>							    	
								  	<div class="product-add">
								      	<input type="button" class="add-product" value="+">
								    </div>
								  	<div class="totals">
									    <div class="totals-item">
										      <label>Cộng</label>
										      <div class="totals-value" id="cart-subtotal">0</div>
										      <input name = 'subtotal' value="0" id="hidesubtotal" type='number' style="display: none" >
									    </div>
									    <div class="totals-item">
										      <label>VAT (5%)</label>
										      <div class="totals-value" id="cart-tax">0</div>
										      <input name = 'vat' value="0" id="hidevat" type='number' style="display: none" >
									    </div>
									    <div class="totals-item">
										      <label>Phí vận chuyển</label>
										      <div class="totals-value" id="cart-shipping">0</div>
										      <input name = 'ship' value="0" id="hideship" type='number' style="display: none" >
									    </div>
									    <div class="totals-item totals-item-total">
										      <label>Tổng cộng</label>
										      <div class="totals-value" id="cart-total">0</div>
										      <input name = 'total' value="0" id="hidetotal" type='number' style="display: none" >
									    </div>
								  	</div>							      
							      	<button type="submit"  class="checkout">Lưu</button>
								</div>
						    </div>
					  	</form>
        			</div>
		        </div>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-2.2.4.js"></script>
		<script src="https://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
		<script src="js/perfect-scrollbar.jquery.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
		<script type="text/javascript" src="js/detailreceipt.js"></script> 
		<script type="text/javascript">
				$("#province").val('{{$receipt->province}}').trigger('change');
				$("#district").val('{{$receipt->district}}').trigger('change');
		</script>
		<!--<script>
			function formatRepo (repo) {
		        if (repo.loading) return repo.text;
		        var result = JSON.parse(repo.data_value);
		        var markup = "<div class='select2-result-repository clearfix'><div>"+result.name+"</div><div>"+result.price+"</div><div>"+repo.text+"</div></div>";

		        return markup;
		    }

		    function formatRepoSelection (repo) {
		        return repo.product_name || repo.text;
		    } 	

		 	var Province = function(config) {
			    jsGrid.Field.call(this, config);
			};

			Province.prototype = new jsGrid.Field({
			    sorter: "string",  

			    itemTemplate: function(value) {
			    	return value;
			    },

			    filterTemplate: function() {
			        var selecter = $('<div><select name="product[]" class="product-select"></select><div>');
			        selecter.children('.product-select').select2({
				        ajax: {
				        url: "/laravel/public/search",
				        dataType: 'json',
				        delay: 250,
				        data: function (params) {
				          return {
				            q: params.term, // search term
				            type : 'product',
				            page: params.page
				          };
				        },
				        processResults: function (kaka, params) {
				          // parse the results into the format expected by Select2
				          // since we are using custom formatting functions we do not need to
				          // alter the remote JSON data, except to indicate that infinite
				          // scrolling can be used

				          var data = $.map(kaka, function (obj) {
				              obj.id = obj.id || obj.data_value;
				              obj.text = obj.text || obj.text;

				              return obj;
				            });
				          params.page = params.page || 1;

				          return {
				            results: data,
				            pagination: {
				              more: true
				            }
				          };
				        },
				        cache: true
				      },
				      escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
				      minimumInputLength: 1,
				      templateResult: formatRepo, // omitted for brevity, see the source of this page
				      templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
				    });
			        return $("<div>").append(selecter);
			    },

			    insertTemplate: function(value) {
			        var selecter = $('<select style="width: 250px;" name="product[]" class="product-select"></select>');
			        selecter.select2({
				        ajax: {
				        url: "/laravel/public/search",
				        dataType: 'json',
				        delay: 250,
				        data: function (params) {
				          return {
				            q: params.term, // search term
				            type : 'product',
				            page: params.page
				          };
				        },
				        processResults: function (kaka, params) {
				          // parse the results into the format expected by Select2
				          // since we are using custom formatting functions we do not need to
				          // alter the remote JSON data, except to indicate that infinite
				          // scrolling can be used

				          var data = $.map(kaka, function (obj) {
				              obj.id = obj.id || obj.data_value;
				              obj.text = obj.text || obj.text;

				              return obj;
				            });
				          params.page = params.page || 1;

				          return {
				            results: data,
				            pagination: {
				              more: true
				            }
				          };
				        },
				        cache: true
				      },
				      escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
				      minimumInputLength: 1,
				      templateResult: formatRepo, // omitted for brevity, see the source of this page
				      templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
				    });
			        return $("<div>").append(selecter);
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
			        return null;
			    }
			});

			jsGrid.fields.province = Province;

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
	                        url: "searchReceiptDetail",
	                        data: filter
	                    });
	                },
	                deleteItem: function(item) {
	                    return $.ajax({
	                        type: "GET",
	                        url: "DeleteReceiptDetail",
	                        data: item
	                    });
	                },
	                insertItem: function(item) {
	                    return $.ajax({
	                        type: "GET",
	                        url: "InsertProductDetail",
	                        data: item
	                    });
	                },
	                updateItem: function(item) {
	                    return $.ajax({
	                        type: "GET",
	                        url: "UpdateProductDetail",
	                        data: item
	                    });
	                }
	            },
		 
		        fields: [
		        	{ name: "id", type: "text" , width: 0, css: "hide" },
 					{ title: "receipt", type: "province", width: 150, name: "receipt" },		            
		            //{ title: "address", type: "text", items: countries, valueField: "Id", textField: "title" },
		            { title: "Mặt hàng", type: "text" , width: 50, name: "product" },
		            { title: "Đơn vị tính", type: "text" , name: "unit" },
		            { title: "Giá", type: "number", name: "price" } ,
		            { title: "Số lượng", type: "number", name: "quanlity" },
		            { title: "Giảm giá", type: "number", name: "discount", width: 0, css: "hide" },
		            { title: "Thành tiền", type: "number", name: "amount" },	            
		            { type: "control" }
		        ]
		    });
		</script>-->
    </body>    
</html>