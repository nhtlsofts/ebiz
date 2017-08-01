(function() {
    /* Set rates + misc */
    var taxRate = 0.05;
    var shippingRate = 15.00; 
    var fadeTime = 300;
    /* Assign actions */
    $('.product-quantitydetail input').live('keyup change', function() {
      updateQuantity(this);
    });

    $('.product-pricedetail input').live('keyup change', function() {
      updateQuantity(this);
    });

    $('.product-removaldetail input').live('click', function() {
      removeItem(this);
    });

    /* Recalculate cart */
    function recalculateCart()
    {
      var subtotal = 0;
      
      /* Sum up row totals */
      $('.product').each(function () {
        subtotal += parseFloat($(this).children('.product-line-pricedetail').text());
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
      var price = parseFloat(productRow.children('.product-pricedetail').children('input').val());
      var quantity = parseFloat(productRow.children('.product-quantitydetail').children('input').val());
      var linePrice = price * quantity;
      productRow.children('.line-amount').children('input').val(linePrice);
      
      /* Update line price display and recalc cart totals */
      productRow.children('.product-line-pricedetail').each(function () {
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

    $('.product-selectdetail').live('change', function() {
        var productRow = $(this).parent().parent().parent();
        var price = parseFloat(JSON.parse($(this).val()).price);
        productRow.children('.product-pricedetail').children('input').val(price);
        productRow.children('.product-unit').children('input').val(JSON.parse($(this).val()).unit);
        var quantity = parseFloat(productRow.children('.product-quantitydetail').children('input').val());
        if ( quantity == 0){
            productRow.children('.product-quantitydetail').children('input').val(1);
            quantity = 1;
        }
        var linePrice = price * quantity;
        productRow.children('.line-amount').children('input').val(linePrice);
      
        /* Update line price display and recalc cart totals */
        productRow.children('.product-line-pricedetail').each(function () {
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
        var products = $("<div class='product'>"+
                            "<div class='product-image'></div>"+
                            "<div class='product-detailsdetail'><div class='product-title'><select style = 'width: 610px;' name='product[]' class='product-selectdetail'></select></div></div>"+
                            "<div class='product-unit'><input name='unit[]' type='text' disabled ></div>"+
                            "<div class='product-pricedetail'><input name='price[]' type='number' value='0' min='0'></div>"+
                            "<div class='product-quantitydetail'><input name='quantity[]'  type='number' value='0' min='0'></div>"+
                            "<div class='product-removaldetail'><input type='button' class='remove-productdetail' value='-'></div>"+
                            "<div class='line-amount'><input name='amount[]' type='number' style='display: none;' ></div>"+
                            "<div class='product-line-pricedetail'>0</div></div>");
        products.children('.product-detailsdetail').children('.product-title').children('.product-selectdetail').select2({
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

    $(".product-selectdetail").select2({
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
    });
    $('.receiptdetail').perfectScrollbar();
    recalculateCart();
})();
