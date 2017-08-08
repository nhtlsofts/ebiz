

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
		<link rel="stylesheet" type="text/css" href="css/component.css" />	
		<link rel="stylesheet" type="text/css" href="css/lam.css" />
		<link rel="stylesheet" type="text/css" href="css/perfect-scrollbar.css" />
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css">
		<script type="text/javascript">
    		<?php 
    			$_SESSION = Session::all();
    			echo "var keylist=[];";
    			foreach ($_SESSION as $key=>$val){
    				if (strpos($key,'ages') > 0){
    					echo "keylist['" . str_replace(' ','',$key). "'] = '".$val."';";
    				}
    				if (strpos($key,'ser_id') > 0){
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
						<li class="cbp-vicurrent"><a href="getdata" class="icon-facebook">Archive</a></li>
						<li><a href="receiptlist" class="icon-cart">Search</a></li>
						<li><a href="productlist" class="icon-product">Pencil</a></li>
						<!-- Example for active item:
										<li class="cbp-vicurrent"><a href="#" class="icon-product">Pencil</a></li>
										-->
						<li><a href="customerlist" class="icon-customer">Location</a></li>
						<li><a href="option" class="icon-option">Images</a></li>
						<li><a href="#" class="icon-unseen">Download</a></li>
					</ul>
				</div>
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
								@foreach ($comments as $manage)
									@if ( $manage->type == 'comment' )
									<div class="person" fb-name={{$manage->sender_name}} fb-id={{$manage->sender_id}} data-chat={{$manage->oid}} id={{$manage->oid}} pagesid={{$manage->page}}>	
										@if ( $manage->is_read == 0 )
										<span class="button__badge">new</span>
										@else
										<span class="button__badge_not" style="display: none;"></span>
										@endif					
										<img src="{{$manage->ava}}" alt="" />
										<span class="name" ava='{{$manage->ava}}'>{{$manage->sender_name}}</span>
					                    <span class="time preview">{{$manage->created_at}}</span>
					                    @if ( $manage->comments != null)
					                    	<span class="preview">{{$manage->comments}}</span>
					                   	@else
					                   		<span class="preview">Hình ảnh</span>	
					                    @endif
					                </div>
									@endif
								@endforeach													
							</div>
							</section>
							<section id="section-underline-2">
							<div class="people">
								@foreach ($messages as $manage)
									@if ( $manage->type == 'message' )
								    <div class="person" fb-name={{$manage->sender_name}} fb-id={{$manage->sender_id}} data-chat={{$manage->oid}} id={{$manage->oid}} pagesid={{$manage->page}}>
								    	@if ( $manage->is_read == 0 )
										<span class="button__badge">new</span>
										@else
										<span class="button__badge_not" style="display: none;"></span>
										@endif						
										<img src="{{$manage->ava}}" alt="" />
										<span class="name" ava='{{$manage->ava}}'>{{$manage->sender_name}}</span>
					                    <span class="time preview">{{$manage->created_at}}</span>
					                    @if ( $manage->comments != null)
					                    	<span class="preview">{{$manage->comments}}</span>
					                   	@else
					                   		<span class="preview">Hình ảnh</span>	
					                    @endif
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
		            <div class="top"><span><span class="name">Chọn khung bên trái để thao tác.</span></span></div>
		            <div class="chat" >
		            </div>
		            <div class="write">
		            </div>
		        </div>
		        <div class='right_bar'>
		        	<div class="container_X">
					  	<form id='form' action="save" method="get">
						    <div class="row">
						      	<h4>Thông tin khách hàng</h4>
						      	<input style="display: none;" name='facebookuser' id='facebookuser' value=<?php echo '"' . Session::get('user_id') .'"'?>>
						      	<input style="display: none;" name='Salepage' id='Salepage' value=''>				      	
						      	<input style="display: none;" name='Cus_id' id='Cus_id' value=''>			      	
						      	<input style="display: none;" name='Cus_fbid' id='Cus_fbid' value=''>						      	
						      	<div class="input-group input-group-icon">
						      		<select style="width: 452px;" class="customer_select"><option></option></select>		
					        	</div>
						      	<div id='customer-name' class="input-group input-group-icon">	
						        	<input name="name" type="text" class="customer_name" placeholder="Họ tên">
						        	<div class="input-icon name-icon"><i class="fa fa-user"></i></div>			
						        	<select name='province' id='province' class="province"></select>
						      	</div>
						      	<div id='district-' class="input-group input-group-icon">
							        <input name='tel'  type="tel" class='customer_tel' placeholder="Số điện thoại">
							        <div class="input-icon phone-icon"><i class="fa fa-envelope"></i></div>
							        <select name='district' id='district' class="district"></select>
						      	</div>
						      	<div id='email-' class="input-group input-group-icon">
							        <input name='email' class='customer_email' type="email" placeholder="Email">
							        <div class="input-icon email-icon"><i class="fa fa-key"></i></div>
						      	</div>
						      	<div id='customer_add-' class="input-group input-group-icon">
							        <textarea name='address' class='customer_add' placeholder="Địa chỉ" cols="40" rows="2"></textarea>
							        <div class="input-icon add-icon"><i class="fa fa-key"></i></div>
						      	</div>
						    </div>
						    <div class="row">
						    	<h4>Mặt hàng</h4>
						    	<div class="shopping-cart">

								  	<div class="column-labels">
									    <label class="product-image">Ảnh</label>
									    <label class="product-details">Sản phẩm</label>
									    <label class="product-price">Giá</label>
									    <label class="product-quantity">SL</label>
									    <label class="product-removal">Xóa</label>
									    <label class="product-line-price-title">Thành tiền</label>
								  	</div>
								  	<div class='product_list'>
								  		<div class="product">
								    		<div class="product-image">
								    		</div>
								    		<div class="product-details">
										    	<div class="product-title">
										    	 	<select name='product[]' class="product-select"></select>
										    	</div>
									      		<!--<p class="product-description"></p>-->
									    	</div>
									    	<div class="product-price">
									    		<input name='price[]' type="number" value="0" min="0">
									    	</div>
										    <div class="product-quantity">
										      	<input name='quantity[]'  type="number" value="0" min="0">
										    </div>
										    <div class="product-removal">
										      	<input type="button" class="remove-product" value="-">
										    </div>
										    <div class='line-amount'>
										    	<input name='amount[]' type="number" style="display: none;" >
									    	</div>
									    	<div class="product-line-price">0</div>
								    	</div>
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
		<script src="js/perfect-scrollbar.jquery.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>        
		<script src="js/lam.js"></script>		
		<script>$('.checkout').fadeOut();</script>
    </body>    
</html>


