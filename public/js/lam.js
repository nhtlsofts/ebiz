(function() {

    $('.chat[data-chat=person2]').addClass('active-chat');
    $('.person[data-chat=person2]').addClass('active');

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
                url: "https://a8d7b7c3.ngrok.io/laravel/public/getdetail",
                type: 'GET',
                cache: false,
                data: 'oid=' + $(this).attr('data-chat'),
                success: function(getData) {
                    //var getData = $.parseJSON(string);
                    //input dữ liệu lấy về từ server vào textbox
                    $(".wrapper .container .right").empty();
                    // Top
                    var top = $("<div class='top'><span>To: <span class='name'>" + currentPerson.find('.name').text() + "</span></span></div>");
                    $(".wrapper .container .right").append(top);

                    // chat
                    var chat = $("<div class='chat' id='" + currentPerson.attr('data-chat') + "' data-chat='" + currentPerson.attr('data-chat') + "''>");
                    chat.append("<div class='conversation-start'><span>" + getData[0].created_at + "</span></div>");
                    getData.forEach(function(entry) {
                    	if(entry.receive_name != 'Me'){	                    		
	                        var div = $("<div class='bubble you'>" + entry.comments + "</div>");
	                        chat.append(div);
                    	}
                    	else{	                    		
	                        var div = $("<div class='bubble me'>" + entry.comments + "</div>");
	                        chat.append(div);
                    	}
                    });
                    $(".wrapper .container .right").append(chat);

                    // write
                    $(".wrapper .container .right").append(
                        "<div class='write'>" +
                        "<a href='javascript:;' class='write-link attach'></a>" +
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
            $('#chat_' + $(this).attr('parrent')).append("<div class='bubble me'>" + $(this).val() + "</div>");
            $.ajax({
                url: "https://a8d7b7c3.ngrok.io/laravel/public/chat",
                type: 'GET',
                cache: false,
                data: 'oid=' + $(this).attr('parrent') + "&chatdata=" + $(this).val(),
                success: function(getData) {
                    alert('ok con dê');
                },
                error: function() {
                    alert('Mạng cùi bắp, tắt modem mở lại giùm cái.');
                }
            });
        }
    });

    try {
        var socket = new WebSocket('ws://localhost:6868');
        socket.onopen = function(e) {
            console.log("Connection established!");
            console.log(e);
        };
        socket.onmessage = function(e) {
            var json = JSON.parse(e.data);
            if (json.Isroot == 2) {
                $("#" + json.parent_id).empty();
                $("#" + json.parent_id).append(
                    "<img src='https://s13.postimg.org/ih41k9tqr/img1.jpg' alt='' />" +
                    "<span class='name'>" + json.sender_name + "</span>" +
                    "<span class='time preview'>" + json.created_at + "</span>" +
                    "<span class='preview'>" + json.comments + "</span>");
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
        };
    } catch (err) {
        console.log(err);
    };
})();