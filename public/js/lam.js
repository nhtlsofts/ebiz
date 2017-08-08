(function() {

    $('.bar #like').live('click',function() {
        $('#k_'+ $(this).attr('data_id') +' .bar #like').attr("id", "dislike");
        $.ajax({
            url: "/laravel/public/like",
            type: 'GET',
            cache: false,
            data: 'oid=' + $(this).attr('data_id')+'&type=1',
            success: function(getData) {
                //alert(getData);
            },
            error: function() {
                    alert('Mạng cùi bắp, tắt modem mở lại giùm cái.');
                }
        });
    });

    $('.bar #dislike').live('click',function() {
        $('#k_'+ $(this).attr('data_id') +' .bar #dislike').attr("id", "like");
        $.ajax({
            url: "/laravel/public/like",
            type: 'GET',
            cache: false,
            data: 'oid=' + $(this).attr('data_id')+'&type=0',
            success: function(getData) {
                //alert(getData);
            },
            error: function() {
                    alert('Mạng cùi bắp, tắt modem mở lại giùm cái.');
                }
        });
    });


    $('.bar #hide').live('click',function() {
        $('#k_'+ $(this).attr('data_id') +' .bar #hide').attr("id", "unhide");
        $.ajax({
            url: "/laravel/public/hide",
            type: 'GET',
            cache: false,
            data: 'oid=' + $(this).attr('data_id')+'&type=1',
            success: function(getData) {
                //alert(getData);
            },
            error: function() {
                    alert('Mạng cùi bắp, tắt modem mở lại giùm cái.');
                }
        });
    });

    $('.bar #unhide').live('click',function() {
        $('#k_'+ $(this).attr('data_id') +' .bar #unhide').attr("id", "hide");
        $.ajax({
            url: "/laravel/public/hide",
            type: 'GET',
            cache: false,
            data: 'oid=' + $(this).attr('data_id')+'&type=0',
            success: function(getData) {
                //alert(getData);
            },
            error: function() {
                    alert('Mạng cùi bắp, tắt modem mở lại giùm cái.');
                }
        });
    });

    $('.bar #delete').live('click',function() {        
        $('#k_'+$(this).attr('data_id')).fadeOut();
        $.ajax({
            url: "/laravel/public/delete",
            type: 'GET',
            cache: false,
            data: 'oid=' + $(this).attr('data_id'),
            success: function(getData) {
                //alert(getData);
            },
            error: function() {
                    alert('Mạng cùi bắp, tắt modem mở lại giùm cái.');
                }
        });
    });


    $('.left .person').live('mousedown',function() {
        if ($(this).hasClass('.active')) {
            return false;
        } else {
            var findChat = $(this).attr('data-chat');
            var personName = $(this).find('.name').text();
            $(this).children('.button__badge').remove();
            $(this).append('<span class="button__badge_not" style="display: none;">new</span>');
            $.ajax({
                url: "/laravel/public/read",
                type: 'GET',
                cache: false,
                data: 'oid=' + $(this).attr('data-chat')
            });
            $('.right .top .name').html(personName);
            $('.chat').removeClass('active-chat');
            $('.left .person').removeClass('active');
            $(this).addClass('active');
            var currentPerson = $(this);
            ///////
            $.ajax({
                url: "/laravel/public/getdetail",
                type: 'GET',
                cache: false,
                data: 'oid=' + $(this).attr('data-chat'),
                success: function(getData) {
                    //var getData = $.parseJSON(string);
                    //input dữ liệu lấy về từ server vào textbox
                    $(".wrapper .container .right").empty();
                    // Top
                    var top = $("<div class='top' pagesid='" + currentPerson.attr('pagesid') +"' id='curent_post' post='"+ currentPerson.attr('data-chat') +"'><span>To: <span class='name'>" + currentPerson.find('.name').text() + "</span></span></div>");
                    $(".wrapper .container .right").append(top);

                    // chat
                    var chatcha = $("<div class='chat' id='chat_" + currentPerson.attr('data-chat') + "' data-chat='" + currentPerson.attr('data-chat') + "''>");
                    var chat = $("<div></div>");
                    chat.append("<div class='conversation-start'><span>" + getData[0].created_at + "</span></div>");
                    var like = 'like';
                    var hide = 'hide';
                    getData.forEach(function(entry) {
                    	if(entry.receive_name != 'Me'){	   
                            if ( entry.hidden == false){
                                hide='hide';
                            }
                            else{
                                hide='unhide';
                            }  
                            if ( entry.like == false){
                                like='like';
                            }
                            else{
                                like='dislike';
                            }    		
	                        var div = "<div class='bubble you' id='k_"+ entry.oid +"'>"+
                                            "<div class='bar_name'>"+entry.sender_name+"</div><div>";
                            if ( typeof entry.comments != "undefined" && entry.comments !=null ){
                                div=div+"<div>"+entry.comments+"</div>";
                            }
                            if ( typeof entry.attackment != "undefined" && entry.attackment != null ){
                                div=div+"<p><img src='"+entry.attackment+"'>";
                            }
                            if (entry.type == 'message'){
                                div=div+"</div></div>";
                            }
                            else{
                                div=div+"</div>"+
                                                "<div class='bar'>"+
                                                "<a id='" + like + "' class='icon icon-like' data_id ='"+entry.oid+
                                                "'></a><a id='"+ hide +"' class='icon icon-hide' data_id ='"+entry.oid+
                                                "'></a><a id='delete' class='icon icon-delete' data_id ='"+entry.oid+
                                                "'></a><a id='inbox' class='icon icon-comment' data_id ='"+entry.oid+
                                                "'></a></div></div>";
                            }
	                        chat.append(div);
                    	}
                    	else{	                    		
	                        var div = "<div class='bubble me' id='k_"+ entry.oid +"'>";
                            if ( typeof entry.comments != "undefined" && entry.comments !=null ){
                                div=div+"<div class='mycomment'>"+entry.comments+"</div>";
                            }
                            if ( typeof entry.attackment != "undefined" && entry.attackment != null ){
                                div=div+"<p><img src='"+entry.attackment+"'>";
                            }
                            if (entry.type == 'message'){
                                div=div+"</div>";
                            }
                            else{
                                div=div+"<div class='bar'>"+
                                        "<a id='delete' class='icon icon-delete' data_id ='"+entry.oid+
                                        "'></a></div></div>";
                            }
	                        chat.append(div);
                    	}
                        chatcha.append(chat);
                        chatcha.perfectScrollbar();
                    });
                    $(".wrapper .container .right").append(chatcha);

                    // write
                    $(".wrapper .container .right").append(
                        "<div class='write'>" +
                        "<input type='text' id='chat_input' parrent='" + currentPerson.attr('data-chat') + "'/>" +
                        "<a href='#' id='mofile' class='write-link attach'></a></div>"
                    );
                    ///////////
                    $('.chat[data-chat = ' + findChat + ']').addClass('active-chat');
                },
                error: function() {
                    alert('Mạng cùi bắp, tắt modem mở lại giùm cái.');
                }
            });
             $.ajax({
                url: "/laravel/public/search",
                type: 'GET',
                cache: false,
                data: 'q=' + $(this).attr('fb-id') + '&type=onecustomer',
                success: function(getData) {
                    if (getData.length === 0){
                        $('#customer-name .customer_name').val(currentPerson.attr('fb-name'));
                        $('#Salepage').val(currentPerson.attr('pagesid'));
                        $('#Cus_fbid').val(currentPerson.attr('fb-id'));
                    }
                    else{
                        $('#Salepage').val(currentPerson.attr('pagesid'));
                        $('#Cus_id').val(getData.oid);
                        $('#Cus_fbid').val(getData.fbid);
                        $('#customer-name').children('input').val(getData.name);  
                        $('#customer-name').children('select').val(getData.province).trigger("change");
                        $('#district-').children('input').val(getData.tel);  
                        $('#district-').children('select').val(getData.district).trigger("change");
                        $('#email-').children('input').val(getData.email); 
                        $('#customer_add-').children('textarea').val(getData.address); 
                    }
                },
                error: function() {
                    alert('Mạng cùi bắp, tắt modem mở lại giùm cái.');
                }
            });
        }            
    });

    [].slice.call(document.querySelectorAll('.tabs')).forEach(function(el) {
        new CBPFWTabs(el);
    });

    $("#chat_input").live("keyup click", function(e) {
        var target = $(this);
        $('#'+target.attr('parrent')).children('.button__badge').remove();
        $('#'+target.attr('parrent')).append('<span class="button__badge_not" style="display: none;">new</span>');
        if (e.which == 13) {            
            $('#chat_' + $(this).attr('parrent')).append("<div class='bubble me wait' >" + $(this).val() + "</div>");
            var value = target.val();            
            target.val('');
            $.ajax({
                url: "/laravel/public/chat",
                type: 'GET',
                cache: false,
                data: 'oid=' + target.attr('parrent') + "&chatdata=" + value,
                success: function(getData) {
                    var json = JSON.parse(getData);
                    $('.wait').each(function(entry) {
                        if ( $(this, 'mycomment').html() == value){
                            $(this).attr('class','bubble me');
                            $(this).attr('id','k_'+json[0].id);
                            if (json[0].type == 'comments'){
                                $(this).append("<div class='bar'>"+
                                        "<a id='delete' class='icon icon-delete' data_id ='"+json[0].id+
                                        "'></a></div></div>"
                                    );
                            }
                        }
                    });
                },
                error: function() {
                    $('.wait').each(function(entry) {
                        if ( $(this, 'mycomment').html() == value){
                            $(this, 'mycomment').html('không gửi được: '+value);
                        }                        
                    });
                }
            });
            $("#chat_" + target.attr('parrent')).scrollTop(9999999);
        }
    });

    try {
        //var socket = new WebSocket('wss://fc500d6a.ngrok.io?aid='+user_id);
        var socket = new WebSocket('wss://5f28ba51.ngrok.io?aid='+user_id);
        socket.onopen = function(e) {
            console.log("Connection established!");
            console.log(e);
        };

        socket.onmessage = function(e) {
            var json = JSON.parse(e.data);
            if ( typeof json.comments == "undefined" || json.comments ==null){
                json.comments = 'Hình ảnh';
            }
            if ('delete' in json){
                json.oid.forEach(function(entry) {
                    $('#k_'+entry.oid).remove();
                });
            }
            if ('updatelike' in json){
                if(json.updatelike == true){
                    $('#k_'+ json.oid +' .bar #like').attr("id", "dislike");
                }
                else{
                    $('#k_'+ json.oid +' .bar #dislike').attr("id", "like");
                }
            }
            if ('updatehide' in json){
                if(json.updatehide == true){
                    $('#k_'+ json.oid +' .bar #hide').attr("id", "unhide");
                }
                else{
                    $('#k_'+ json.oid +' .bar #unhide').attr("id", "hide");
                }
            }
            else {
                if (json.Isroot == 2) {
                    var name = $("#" + json.parent_id + ' .name').html();
                    var ava = $("#" + json.parent_id + ' .name').attr('ava');
                    $("#" + json.parent_id).empty();
                    $("#" + json.parent_id).append(
                        "<img src='"  + ava +  "' alt='' />" +
                        "<span class='name' ava= '" + ava +"'>" + name + "</span>" +
                        "<span class='time preview'>" + json.created_at + "</span>" +
                        "<span class='preview'>" + json.comments + "</span>");
                    if(json.receive_name != 'Me'){
                        $("#" + json.parent_id).append('<span class="button__badge">new</span>');                            
                        var div = "<div class='bubble you' id='k_"+ json.oid +"'>"+
                                            "<div class='bar_name'>"+json.sender_name+"</div>"+
                                            "<div>";
                        if ( typeof json.comments != "undefined" && json.comments !=null ){
                                div=div+"<div>"+json.comments+"</div>";
                            }
                        if ( typeof json.attackment != "undefined" && json.attackment != null ){
                            div=div+"<p><img src='"+json.attackment+"'>";
                        }
                        if (json.type == 'message'){
                                div=div+"</div></div>";
                            }
                        else {
                            div = div + "</div><div class='bar'>"+
                                                "<a id='like' class='icon icon-like' data_id ='"+json.oid+
                                                "'></a><a id='hide' class='icon icon-hide' data_id ='"+json.oid+
                                                "'></a><a id='delete' class='icon icon-delete' data_id ='"+json.oid+
                                                "'></a><a id='inbox' class='icon icon-comment' data_id ='"+json.oid+
                                                "'></a></div></div>";
                        }
                        $("#chat_" + json.parent_id).append(div);
                    }
                    else{
                        if ( $('#k_'+json.oid).length == 0) {                          
                            var div = "<div class='bubble me' id='#_"+ json.oid +"'>";
                            if ( typeof json.comments != "undefined" && json.comments !=null ){
                                div=div+"<div class='mycomment'>"+json.comments+"</div>";
                            }
                            if ( typeof json.attackment != "undefined" && json.attackment != null ){
                                div=div+"<p><img src='"+json.attackment+"'>";
                            }
                            if (json.type == 'message'){
                                div=div+"</div>";
                            }
                            else{
                                div=div+"<div class='bar'>"+
                                        "<a id='delete' class='icon icon-delete' data_id ='"+json.oid+
                                        "'></a></div></div>";
                            }                            
                            $("#chat_" + json.parent_id).append(div);
                        }
                    }
                     $("#chat_" + json.parent_id).scrollTop(9999999);
                    console.log(e);
                }
                if (json.Isroot == 1) {
                    if (json.type == 'comment') {
                        $("#section-underline-1 .people").prepend(
                            "<div class='person' pagesid='" + json.page +"' data-chat=" + json.oid + " id=" + json.oid + ">" +
                            "<span class='button__badge'>new</span>"+
                            "<img src='" + json.ava + "' alt='' />" +
                            "<span class='name' ava='"+ json.ava +"'>" + json.sender_name + "</span>" +
                            "<span class='time preview'>" + json.created_at + "</span>" +
                            "<span class='preview'>" + json.comments + "</span>");
                        console.log(e);
                    } else {
                        $("#section-underline-2 .people").prepend(
                            "<div class='person' data-chat=" + json.oid + " id=" + json.oid + ">" +
                            "<span class='button__badge'>new</span>"+
                            "<img src='" + json.ava + "' alt='' />" +
                            "<span class='name' ava='"+ json.ava +"'>" + json.sender_name + "</span>" +
                            "<span class='time preview'>" + json.created_at + "</span>" +
                            "<span class='preview'>" + json.comments + "</span>");
                        console.log(e);
                    }
                }
            }
        };
    } catch (err) {
        console.log(err);
    };

    function el(id) { return document.getElementById(id); }
    var canvas = el("canvas");
    var context = canvas.getContext("2d");

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                
                var img = new Image();
                img.addEventListener("load", function () {
                    canvas.width = img.width;
                    canvas.height = img.height;
                    context.drawImage(img, 0, 0, img.width, img.height);
                    fbUpload($('#curent_post').attr('post'),$('#curent_post').attr('pagesid'));
                    //context.clearRect(0, 0, canvas.width, canvas.height);
                });
                img.src = e.target.result;
            }

            reader.readAsDataURL(input.files[0]);

            
        }
    }

    $("#selectedFile").live("change", function(e) {

        console.log(0);
        readURL(this); 

    });

              
    $("#mofile").live("click", function(e) {     
        $('#selectedFile').click();              
    });
    
    function fbUpload(oid,pageid) {
        var dataURL = canvas.toDataURL('image/jpeg', 1.0);
        var blob = dataURItoBlob(dataURL);
        var formData = new FormData();
        formData.append('access_token', keylist['Pages'+pageid]);
        formData.append('attachment', blob);


        if ( oid.indexOf('mid_') != -1 ){
            var link = 'https://graph.facebook.com/'+oid+'/messages';
            link = link.replace('t_mid_','t_mid.$')
        }
        else {
            var link = 'https://graph.facebook.com/'+oid+'/comments';
        }

        var xhr = new XMLHttpRequest();
        xhr.open('POST', link , true);
        xhr.onload = xhr.onerror = function () {
            console.log(xhr.responseText);
        };
        xhr.send(formData);
    }

    function dataURItoBlob(dataURI) {
        var byteString = atob(dataURI.split(',')[1]);
        var ab = new ArrayBuffer(byteString.length);
        var ia = new Uint8Array(ab);
        for (var i = 0; i < byteString.length; i++) { ia[i] = byteString.charCodeAt(i); }
        return new Blob([ab], { type: 'image/jpeg' });
    }
    /* Set rates + misc */
    var taxRate = 0.05;
    var shippingRate = 15.00; 
    var fadeTime = 300;


    /* Assign actions */
    $('.product-quantity input').live('keyup change', function() {
      updateQuantity(this);
    });

    $('.product-price input').live('keyup change', function() {
      updateQuantity(this);
    });

    $('.product-removal input').live('click', function() {
      removeItem(this);
    });


    /* Recalculate cart */
    function recalculateCart()
    {
      var subtotal = 0;
      
      /* Sum up row totals */
      $('.product').each(function () {
        subtotal += parseFloat($(this).children('.product-line-price').text());
      });
      
      /* Calculate totals */
      var tax = subtotal * taxRate;
      var shipping = (subtotal > 0 ? shippingRate : 0);
      var total = subtotal + tax + shipping;
      
      /* Update totals display */
      $('.totals-value').fadeOut(fadeTime, function() {
        $('#cart-subtotal').html(subtotal.toLocaleString());
        $('#hidesubtotal').attr('value',subtotal);
        $('#cart-tax').html(tax.toLocaleString());
        $('#hidevat').attr('value',tax);
        $('#cart-shipping').html(shipping.toLocaleString());
        $('#hideship').attr('value',shipping);
        $('#cart-total').html(total.toLocaleString());
        $('#hidetotal').attr('value',total);
        if(total == 0){
            if ($('.checkout').is(":visible")){
                $('.checkout').fadeOut(fadeTime);
            }
        }else{
            if (!$('.checkout').is(":visible")){
                $('.checkout').fadeIn(fadeTime);
            }
        }
        $('.totals-value').fadeIn(fadeTime);
      });
    }


    /* Update quantity */
    function updateQuantity(quantityInput)
    {
      /* Calculate line price */
      var productRow = $(quantityInput).parent().parent();
      var price = parseFloat(productRow.children('.product-price').children('input').val());
      var quantity = parseFloat(productRow.children('.product-quantity').children('input').val());
      var linePrice = price * quantity;
      productRow.children('.line-amount').children('input').val(linePrice);
      
      /* Update line price display and recalc cart totals */
      productRow.children('.product-line-price').each(function () {
        $(this).fadeOut(fadeTime, function() {
          $(this).text(linePrice);
          recalculateCart();
          $(this).fadeIn(fadeTime);
        });
      });  
    }


    /* Remove item from cart */
    function removeItem(removeButton)
    {
      /* Remove row from DOM and recalc cart total */
      var productRow = $(removeButton).parent().parent();
      productRow.slideUp(fadeTime, function() {
        productRow.remove();
        recalculateCart();
      });
    }


    $('.province').append('<option></option>');
    P.province.forEach(function(entry) {
        $('.province').append("<option value='"+entry.provinceid+"'>"+entry.name+"</option>");
    });

    $('.district').append('<option></option>');
    PL.district.forEach(function(entry) {
        $('.district').append("<option value='"+entry.districtid+"'>"+entry.name+"</option>");
    });

    $('.product-select').live('change', function() {
        var productRow = $(this).parent().parent().parent();
        var price = parseFloat(JSON.parse($(this).val()).price);
        productRow.children('.product-price').children('input').val(price);
        var quantity = parseFloat(productRow.children('.product-quantity').children('input').val());
        if ( quantity == 0){
            productRow.children('.product-quantity').children('input').val(1);
            quantity = 1;
        }
        var linePrice = price * quantity;
        productRow.children('.line-amount').children('input').val(linePrice);
      
        /* Update line price display and recalc cart totals */
        productRow.children('.product-line-price').each(function () {
            $(this).fadeOut(fadeTime, function() {
            $(this).text(linePrice);
            recalculateCart();
            $(this).fadeIn(fadeTime);
            });
        }); 
    });

    $('.add-product').on('click',function() {
        var shoppingcart = $(this).parent().parent();
        var productlist = shoppingcart.children('.product_list');
        var products = $("<div class='product'><div class='product-image'></div><div class='product-details'><div class='product-title'><select style = 'width: 178px' name='product[]' class='product-select'></select></div></div><div class='product-price'><input name='price[]' type='number' value='0' min='0'></div><div class='product-quantity'><input name='quantity[]'  type='number' value='0' min='0'></div><div class='product-removal'><input type='button' class='remove-product' value='-'></div><div class='line-amount'><input name='amount[]' type='number' style='display: none;''></div><div class='product-line-price'>0</div></div>");
        products.children('.product-details').children('.product-title').children('.product-select').select2({
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
        productlist.append(products);
    });

    $("#province").select2({
        placeholder: "Tỉnh/Thành",
        allowClear: true
    });

    $("#district").select2({
        placeholder: "Quận/Huyện",
        allowClear: true
    });

    function formatRepo (repo) {
        if (repo.loading) return repo.text;
        var result = JSON.parse(repo.data_value);
        var markup = "<div class='select2-result-repository clearfix'><div>"+result.name+"</div><div>"+result.price+"</div><div>"+repo.text+"</div></div>";

        return markup;
    }

    function formatRepoSelection (repo) {
        return repo.product_name || repo.text;
    }

    $(".product-select").select2({
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
    
    $(".customer_select").select2({
        ajax: {
            url: "/laravel/public/search",
            dataType: 'json',
            delay: 250,
            data: function (params) {
              return {
                q: params.term, // search term
                type : 'customer',
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
        templateSelection: formatRepoSelection, // omitted for brevity, see the source of this page
        placeholder: "Chọn khách hàng"
    });

    $(".customer_select").on('change',function() {
        var customer_info = $(this).parent().parent();
        $('#Cus_id').val(JSON.parse($(this).val()).oid);
        $('#Cus_fbid').val(JSON.parse($(this).val()).fbid);
        customer_info.children('#customer-name').children('input').val(JSON.parse($(this).val()).name);  
        customer_info.children('#customer-name').children('select').val(JSON.parse($(this).val()).province).trigger("change");
        customer_info.children('#district-').children('input').val(JSON.parse($(this).val()).tel);  
        customer_info.children('#district-').children('select').val(JSON.parse($(this).val()).district).trigger("change");
        customer_info.children('#email-').children('input').val(JSON.parse($(this).val()).email); 
        customer_info.children('#customer_add-').children('textarea').val(JSON.parse($(this).val()).address); 
    });

    $("#form").on("submit", function(e){
        recalculateCart();
        $.ajax({
            url: 'save',
            type: 'GET',
            cache: false,
            data: $('#form').serialize(),
            success: function(getData) {
                //alert($('#form').serialize());
            },
            error: function() {
                    alert('Mạng cùi bắp, tắt modem mở lại giùm cái.');
            }
        });
        e.preventDefault();
        $('#form')[0].reset();
        $('.remove-product').trigger("click");
        $("#province").val("").trigger("change");
        $("#district").val("").trigger("change");
        $(".customer_select").val("").trigger("change");

    });
    $('.right_bar').perfectScrollbar();

    var timeout,
        template,
        container,content,height,
        template2,
        container2,content2,height2,
    container = $('#section-underline-1');
    content =  $('#section-underline-1').children('.people');

    container2 = $('#section-underline-2');
    content2 =  $('#section-underline-2').children('.people');

    container.scroll(function() {
        clearTimeout(timeout);  
        timeout = setTimeout(function() {
            var active;
            var scrollTop = container.scrollTop();
            var height = content.height();
            var containerHeight = content.parent().height();
            
            if (active) return;
            
            if (height-containerHeight-scrollTop<100) {
                active=true;
                addLines($('#section-underline-1').children('.people').children('.person').last().children('.time.preview').html());
                active=false;
            }
        }, 100); 
    });


    container2.scroll(function() {
        clearTimeout(timeout);  
        timeout = setTimeout(function() {
            var active2;
            var scrollTop2 = container2.scrollTop();
            var height2 = content2.height();
            var containerHeight2 = content2.parent().height();
            
            if (active2) return;
            
            if (height2-containerHeight2-scrollTop2<100) {
                active2=true;
                addLines2($('#section-underline-2').children('.people').children('.person').last().children('.time.preview').html());
                active2=false;
            }
        }, 100); 
    });

    function addLines(time) {
        $.ajax({
            url: "/laravel/public/getmoredata",
            type: 'GET',
            cache: false,
            data: 'time=' + time,
            success: function(getData) {
                for(var i=0;i<getData.length;i++){
                    var person = '<div class="person" fb-name= ' + getData[i].sender_name + ' fb-id= ' +
                                getData[i].sender_id + ' data-chat= ' + getData[i].oid + ' id= '+ getData[i].oid + ' pagesid= ' + 
                                getData[i].page + '><img src="' + getData[i].ava  +'" alt="" />'+
                                ' <span class="name" ava= "' + getData[i].ava + '"> '+getData[i].sender_name + ' </span><span class="time preview"> ' + 
                                getData[i].created_at + ' </span> ';
                    if ( getData[i].is_read == 0){
                        person = person + '<span class="button__badge">new</span>';
                    }
                    else {
                        person = person + '<span class="button__badge_not" style="display: none;"></span>';
                    }         
                    if ( getData[i].comments != null){
                        person = person + '<span class="preview">' + getData[i].comments + '</span></div>';
                    }
                    else{
                        person = person + '<span class="preview">Hình ảnh</span></div>';  
                    }
                    content.append(person);

                }
            },
            error: function() {
                    alert('Mạng cùi bắp, tắt modem mở lại giùm cái.');
                }
        });
        //content.append("<a>abc</a>");
    }

    function addLines2(time) {
        $.ajax({
            url: "/laravel/public/getmoredata2",
            type: 'GET',
            cache: false,
            data: 'time=' + time,
            success: function(getData) {
                for(var i=0;i<getData.length;i++){
                    var person = '<div class="person" fb-name= ' + getData[i].sender_name + ' fb-id= ' +
                                getData[i].sender_id + ' data-chat= ' + getData[i].oid + ' id= '+ getData[i].oid + ' pagesid= ' + 
                                getData[i].page + '><img src="'+ getData[i].ava  +'" alt="" />'+
                                ' <span class="name" ava= "' + getData[i].ava + '"> '+getData[i].sender_name + ' </span><span class="time preview"> ' + 
                                getData[i].created_at + ' </span> ';
                    if ( getData[i].is_read == 0){
                        person = person + '<span class="button__badge">new</span>';
                    }
                    else {
                        person = person + '<span class="button__badge_not" style="display: none;"></span>';
                    }       
                    if ( getData[i].comments != null){
                        person = person + '<span class="preview">' + getData[i].comments + '</span></div>';
                    }
                    else{
                        person = person + '<span class="preview">Hình ảnh</span></div>';  
                    }
                    content2.append(person);

                }
            },
            error: function() {
                    alert('Mạng cùi bắp, tắt modem mở lại giùm cái.');
                }
        });
    }
    $('.icon-unseen').live('click',function(){
        $('.button__badge_not').parent().fadeOut();
        $('.icon-unseen').attr('class','icon-seen');
    });
    $('.icon-seen').live('click',function(){
        $('.button__badge_not').parent().fadeIn();
        $('.icon-seen').attr('class','icon-unseen');
    });


    /*
    $('.person span:not(.button__badge):not(.time.preview):not(.preview):not(.name)').each(function(){
        $(this).parent().fadeOut();
    });*/
})();
