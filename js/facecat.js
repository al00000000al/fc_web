
function controlSize() {
    var threshold = 100;

    $('.chat__title').each(function () {
        var $self = $(this),
            fs = parseInt($self.css('font-size'));

        while ($self.height() > threshold) {
            $self.css({'font-size': fs--});
        }
        $self.height(threshold);
    });
}

function controlSizeMyChat() {
    var threshold = 40;

    $('.mychat_title').each(function () {
        var $self = $(this),
            fs = parseInt($self.css('font-size'));

        while ($self.height() > threshold) {
            $self.css({'font-size': fs--});
        }
        $self.height(threshold);
    });
}

function scrollToBottom(id) {
    $('html, body').scrollTop( $(document).height() );
}

$( function()
{
    $('.message_img_href').magnificPopup({
        type: 'image',
        closeOnContentClick: true,
        closeBtnInside: true,
        fixedContentPos: true,
        mainClass: 'mfp-with-zoom', // class to remove default margin from left and right side
        image: {
            verticalFit: true
        },
        zoom: {
            enabled: true,
            duration: 300 // don't foget to change the duration also in CSS
        }
    });


    $('.modal__wrap').on('click', function() {
        closeBox('modal_box');
    });
    selectMsgs();

});


function selectMsgs(){
    $(".message_wrap").click(function () {
        $(this).toggleClass("selected");
        var cnt = $('.selected').length;
        if($('.message_img_href').hasClass('.mfp-img')){
            uncheckMessages();
        }else{


            if(cnt > 0) {
                $('.fc__arrow_left').hide();
                $('.fc__chat_title').hide();
                $('.fc__chat_settings').hide();
                $('.fc__message_cnt_wrap').show();
                $('.fc__message_cnt').text(cnt + ' сообщений');
            }else{
                $('.fc__arrow_left').show();
                $('.fc__chat_title').show();
                $('.fc__chat_settings').show();
                $('.fc__message_cnt_wrap').hide();
            }
        }
    });
}

function uncheckMessages(){
    $(".message_wrap").removeClass("selected");
    $('.fc__arrow_left').show();
    $('.fc__chat_title').show();
    $('.fc__chat_settings').show();
    $('.fc__message_cnt_wrap').hide();
}

function stickersClose() {
    
}

function sendSticker(k, chat){
    $.ajax({
        type: "POST",
        data: {
            act: 'send_sticker',
            sticker_id: k,
            chat_id: chat,
        },
        url: "/facecat.php",
        success: function(data){
            $('#fc_messages').prepend(data);
            closeBox('modal_wrap');
            scrollToBottom();
        },
        done: function () {
            closeBox('modal_wrap');
        }
    });
}

function showStickers(i, chat_id){
    var stickers_html =  '<div class="stickers_wrap">';
    var k;
    for (k = 1; k < 30; k++){
        var cover = 'http://stickerface.io/api/png/s'+k+'%3B'+encodeURI(i)+'?size=86';
        stickers_html = stickers_html+'<div class="sticker" style=\'background: ' +
            'url('+cover+') no-repeat center center\' onclick="sendSticker('+k+',\''+chat_id+'\'); return false;"></div>';
    }
   // console.log(stickers_html);
    stickers_html = stickers_html + '</div>';
    showBox('',
        stickers_html);
}


function updateMyChats(type, cl, cl2) {
    setInterval(function () {

        $.ajax({
            type: "POST",
            data: {
                act: 'a_load'+type,
                from: 0,
            },
            url: "/facecat.php",
            success: function (data) {
               /* for(var i = 0; i<=31;i++) {
                    $('.' + cl2).eq(i).remove();
                }*/
                $("."+cl).prepend(data);
                $('[id]').each(function() {
                    var idAttr = $(this).attr('id'),
                        selector = '[id=' + idAttr + ']';
                    if ($(selector).length > 1) {
                        $(selector).not(':first').remove();
                    }
                });

                if(type === '_chat'){
                    controlSizeMyChat();
                } else{
                    controlSize();
                }
            }
        });
       // $("."+cl).load("/facecat.php?act=a_load"+type+"&from=0", "");
    }, 5000); // milliseconds to wait

}

function sendMessage(chat_id){
    var text = $('.fc__msg_text').val();
    $.ajax({
        type: "POST",
        data: {
            act: 'send',
            chat_id: chat_id,
            msg: text,
        },
        url: "/facecat.php",
        success: function (data) {
            if(data === 'redirect'){
                document.location.reload(true);
            }
            $('.fc__msg_text').val('');
           // $('#fc_messages').prepend(data);
            scrollToBottom();
        }
    });
}


function updateMessages(chat_id, from) {
    $(document).on('keypress',function(e) {
        if(e.which === 13) {
            sendMessage(chat_id);
        }
    });
    setInterval(function () {

        $.ajax({
            type: "POST",
            data: {
                act: 'a_get_msg',
                from: from,
                chat_id: chat_id
            },
            url: "/facecat.php",
            success: function (data) {

                $("#fc_messages").prepend(data);
                $('[id]').each(function() {
                    var idAttr = $(this).attr('id'),
                        selector = '[id=' + idAttr + ']';
                    if ($(selector).length > 1) {
                        $(selector).not(':first').remove();
                    }
                });
                selectMsgs();
               //нахуй scrollToBottom();
            }
        });
        // $("."+cl).load("/facecat.php?act=a_load"+type+"&from=0", "");
    }, 1000); // milliseconds to wait

}


function loadOnSwipe(module){

    $('body').swipe( {
        swipeStatus:function(event, phase, direction, distance, duration, fingerCount, fingerData, currentDirection)
        {

            if (phase==="end") {
                if (direction === 'right') {
                    if(module === 'chats'){
                        go('facecat.php');
                    } else if(module ==='main'){
                        go('facecat.php?act=profile');
                    }
                }

                if (direction === 'left') {
                    if(module === 'profile')
                    {
                        go('facecat.php');
                    } else if (module === 'main'){
                        go('facecat.php?act=chats');
                    }
                }
            }

            },
            triggerOnTouchEnd:false,
                threshold:50 // сработает через 20 пикселей
        });
}


function onUpload(){
    $(".new_chat__menu--photo").on('click',function(){
        $("#selector").trigger('click');
    });
    $('#selector').change(function() {
        readURL(this,'.new_chat__wrap');
        $('#upload_photo').submit();

    });
    $('#upload_photo').on('submit',(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
$('.loader').css("display","block");
        $.ajax({
            type:'POST',
            url: $(this).attr('action'),
            data:formData,
            cache:false,
            contentType: false,
            processData: false,
            success:function(data){
                console.log("success");
                console.log(data);
                const regex = /{"photo":"(.*)"}/gm;
                const str = data;
                let m;

                while ((m = regex.exec(str)) !== null) {
                    // This is necessary to avoid infinite loops with zero-width matches
                    if (m.index === regex.lastIndex) {
                        regex.lastIndex++;
                    }

                    // The result can be accessed through the `m`-variable.
                    m.forEach(function(match)  {

                        $('#photo_number').val(match);
                    });
                }
                $('.loader').css("display","none");

            },
            error: function(data){
                console.log("error");
                console.log(data);
            }
        });
    }));
}


function onChangeText(){
    $('.fc__msg_text').on('input',function(e){
        if($('.fc__msg_text').val() !== '') {
            $('#send_photo').hide();
            $('#send_msg').show();
            $.ajax({
                type: "POST",
                data: {
                    act: 'a_typing',
                    chat_id: $('#chatid').val(),
                },
                url: "/facecat.php",
                success: function () {

                }
            });
        } else{
            $('#send_photo').show();
            $('#send_msg').hide();
        }
    });
}

function onUploadInChats(){
    $("#send_photo").on('click',function(){
        $("#selector").trigger('click');
    });
    $('#selector').change(function() {
        readURL(this,'.new_chat__wrap');
        $('#upload_photo').submit();

    });
    $('#upload_photo').on('submit',(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $('.loader').css("display","block");
        $.ajax({
            type:'POST',
            url: $(this).attr('action'),
            data:formData,
            cache:false,
            contentType: false,
            processData: false,
            success:function(data){
                console.log("success");
                console.log(data);
                const regex = /{"photo":"(.*)"}/gm;
                const str = data;
                let m;

                while ((m = regex.exec(str)) !== null) {
                    // This is necessary to avoid infinite loops with zero-width matches
                    if (m.index === regex.lastIndex) {
                        regex.lastIndex++;
                    }

                    // The result can be accessed through the `m`-variable.
                    m.forEach(function(match)  {

                        $('#photo_number').val(match);
                    });
                }
                $('.loader').css("display","none");
                $.ajax({
                    type:'POST',
                    url: $(this).attr('action'),
                    data:formData,
                    cache:false,
                    contentType: false,
                    processData: false,
                    success:function(data){
                        console.log("success");
                        console.log(data);
                   /*     const regex = /{"photo":"(.*)"}/gm;
                        const str = data
                        let m;

                        while ((m = regex.exec(str)) !== null) {
                            // This is necessary to avoid infinite loops with zero-width matches
                            if (m.index === regex.lastIndex) {
                                regex.lastIndex++;
                            }

                            // The result can be accessed through the `m`-variable.
                            m.forEach(function(match)  {

                                $('#photo_number').val(match);
                            });
                        }*/
                        $('.loader').css("display","none");

                    },
                    error: function(data){
                        console.log("error");
                        console.log(data);
                    }
                });

            },
            error: function(data){
                console.log("error");
                console.log(data);
            }
        });
    }));
}

function readURL(input,el) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $(el).css("background", 'linear-gradient( to bottom right, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.6) ),url('+e.target.result+') no-repeat center center');
            $(el).css('background-size', 'cover');
           // $(el).attr('src', e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function createNewChat(){
    var bg, cover = '';
    var tmp_ = $('#photo_number').val();
    if(tmp_>7){
        cover = tmp_;
    } else{
        bg = tmp_;
    }
    $.ajax({
        type: "POST",
        data: {
            act: 'a_new_chat',
            name: $('.new_chat__title').val(),
            cover: cover,
            bg: bg,
        },
        url: "/facecat.php",
        success: function () {
           go('facecat.php');
/* (Что за дерьмо???)
            $("#fc_messages").prepend(data);
            $('[id]').each(function() {
                var idAttr = $(this).attr('id'),
                    selector = '[id=' + idAttr + ']';
                if ($(selector).length > 1) {
                    $(selector).not(':first').remove();
                }
            });*/
            //нахуй scrollToBottom();
        }
    });
}

function saveProfile(){
    $('.fc__edit_profile_form').on('submit',(function(e) {
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            type:'POST',
            url: $(this).attr('action'),
            data:formData,
            cache:false,
            contentType: false,
            processData: false,
            success:function() {
            }
        });
    }));
}
function randomInteger(min, max) {
    var rand = min + Math.random() * (max + 1 - min);
    rand = Math.floor(rand);
    return rand;
}

function makeRandomLayots() {
    var layout = [];
    for(var i = 1; i<=20; i++){
        var c = randomInteger(0,264);
        if(layout.indexOf( c ) !== -1 ){
            layout[i]=c;
        }
        else{
            c = randomInteger(0,264);
            layout[i]=c;
        }
    }
    var lay = layout.slice(1).join(';');
    $('#layoutX').val(lay);
    $('.fc__edit_profile_img').attr("src","http://stickerface.io/api/svg/"+lay+"?size=267");
}

function saveLayout(btn){

    if ($(btn).hasClass('loading')) {
        return;
    }
    var name = getUrlParameter('p_name');
    var bio = getUrlParameter('p_bio');
    $.post('/facecat.php?act=save_layer&layers='+App.getLayers().join(';')+'&p_name='+name+'&p_bio='+bio).done(function() {
      //  clearTimeout(window.animTimer);
        window.location.href='facecat.php?act=profile';
       // $('#step1, #animation_step').hide();
       // $('#step2').addClass('shown');
    }).fail(function (xhr) {
        $(btn).removeClass('loading');
        App.showError(xhr.responseJSON.error);
    });


}

var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
};