(function() {

    $('.bar #like').live('click',function() {
        $.ajax({
            url: "https://75d24456.ngrok.io/laravel/public/like",
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
        $.ajax({
            url: "https://75d24456.ngrok.io/laravel/public/like",
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
        $.ajax({
            url: "https://75d24456.ngrok.io/laravel/public/hide",
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
        $.ajax({
            url: "https://75d24456.ngrok.io/laravel/public/hide",
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
        $.ajax({
            url: "https://75d24456.ngrok.io/laravel/public/delete",
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
            $('.right .top .name').html(personName);
            $('.chat').removeClass('active-chat');
            $('.left .person').removeClass('active');
            $(this).addClass('active');
            var currentPerson = $(this);
            ///////
            $.ajax({
                url: "https://75d24456.ngrok.io/laravel/public/getdetail",
                type: 'GET',
                cache: false,
                data: 'oid=' + $(this).attr('data-chat'),
                success: function(getData) {
                    //var getData = $.parseJSON(string);
                    //input dữ liệu lấy về từ server vào textbox
                    $(".wrapper .container .right").empty();
                    // Top
                    var top = $("<div class='top' id='curent_post' post='"+ currentPerson.attr('data-chat') +"'><span>To: <span class='name'>" + currentPerson.find('.name').text() + "</span></span></div>");
                    $(".wrapper .container .right").append(top);

                    // chat
                    var chat = $("<div class='chat' id='chat_" + currentPerson.attr('data-chat') + "' data-chat='" + currentPerson.attr('data-chat') + "''>");
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
	                        var div = "<div class='bubble me'>";
                            if ( typeof entry.comments != "undefined" && entry.comments !=null ){
                                div=div+"<div>"+entry.comments+"</div>";
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
                    });
                    $(".wrapper .container .right").append(chat);

                    // write
                    $(".wrapper .container .right").append(
                        "<div class='write'>" +
                        "<a href='#' id='mofile' class='write-link attach'></a>" +
                        "<input type='text' id='chat_input' parrent='" + currentPerson.attr('data-chat') + "'/>" +
                        "<a href='javascript:;' class='write-link smiley'></a>" +
                        "<a href=javascript:;' class='write-link send'></a></div>"
                    );
                    ///////////
                    $('.chat[data-chat = ' + findChat + ']').addClass('active-chat');
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

    $("#chat_input").live("keyup", function(e) {
        if (e.which == 13) {
            //$('#chat_' + $(this).attr('parrent')).append("<div class='bubble me'>" + $(this).val() + "</div>");
            var target = $(this);
            var value = target.val();
            target.val('');
            $.ajax({
                url: "https://75d24456.ngrok.io/laravel/public/chat",
                type: 'GET',
                cache: false,
                data: 'oid=' + target.attr('parrent') + "&chatdata=" + value,
                success: function(getData) {
                    //////
                },
                error: function() {
                    alert('Mạng cùi bắp, tắt modem mở lại giùm cái.');
                }
            });
        }
    });

    try {
        var socket = new WebSocket('ws://localhost:6868?aid=123');
        socket.onopen = function(e) {
            console.log("Connection established!");
            console.log(e);
        };

        socket.onclose = function (evt) {
            alert("aaaa");
        };

        socket.onmessage = function(e) {
            var json = JSON.parse(e.data);
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
                    $("#" + json.parent_id).empty();
                    $("#" + json.parent_id).append(
                        "<img src='https://s13.postimg.org/ih41k9tqr/img1.jpg' alt='' />" +
                        "<span class='name'>" + name + "</span>" +
                        "<span class='time preview'>" + json.created_at + "</span>" +
                        "<span class='preview'>" + json.comments + "</span>");
                    if(json.receive_name != 'Me'){                             
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
                        var div = "<div class='bubble me'>";
                            if ( typeof json.comments != "undefined" && json.comments !=null ){
                                div=div+"<div>"+json.comments+"</div>";
                            }
                            if ( typeof json.attackment != "undefined" && json.attackment != null ){
                                div=div+"<p><img src='"+json.attackment+"'>";
                            }
                            div=div+"</div>";                            
                        $("#chat_" + json.parent_id).append(div);
                    }
                    console.log(e);
                }
                if (json.Isroot == 1) {
                    if (json.type == 'comment') {
                        $("#section-underline-1 .people").prepend(
                            "<div class='person' data-chat=" + json.oid + " id=" + json.oid + ">" +
                            "<img src='https://s13.postimg.org/ih41k9tqr/img1.jpg' alt='' />" +
                            "<span class='name'>" + json.sender_name + "</span>" +
                            "<span class='time preview'>" + json.created_at + "</span>" +
                            "<span class='preview'>" + json.comments + "</span>");
                        console.log(e);
                    } else {
                        $("#section-underline-2 .people").prepend(
                            "<div class='person' data-chat=" + json.oid + " id=" + json.oid + ">" +
                            "<img src='https://s13.postimg.org/ih41k9tqr/img1.jpg' alt='' />" +
                            "<span class='name'>" + json.sender_name + "</span>" +
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
                    fbUpload($('#curent_post').attr('post'));
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
        $('#selectedFile').onclick = function () {
            this.value = null;
        };
    });
    
    function fbUpload(oid) {
        var dataURL = canvas.toDataURL('image/jpeg', 1.0);
        var blob = dataURItoBlob(dataURL);
        var formData = new FormData();
        formData.append('access_token', pagekey);
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
})();