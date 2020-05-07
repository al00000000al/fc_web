<?php
/**
 * Created by PhpStorm.
 * User: Дмитри
 * Date: 15.04.2019
 * Time: 1:17
 */


require_once '../include-fc.php';
require_once '../lib/engine.lib.php';
require_once '../lib/facecat.lib.php';

addStatic('main.js');
addStatic('facecat.css');
addStatic('utils.js');
if (isset($_COOKIE['fcdark'])) {
    addStatic('facecat_dark.css');
} else if (isset($_COOKIE['fcgay'])) {
    addStatic('facecat_lgbt.css');
}
addStatic('magnific-popup.css');
addStatic('facecat.js');
addStatic('layer/init.js');
addStatic('vendor/jquery.magnific-popup.min.js');
addStatic('vendor/jquery.touchSwipe.min.js');

try {
    callAct('facecat', $Input['act']);
} catch (Exception $e) {
    wrapPage('error', $e);
}




function a_facecat_()
{
global $Input;

    if (!isLoggedFC()) {


        wrapPage('Face cat main', sPage(sFCMain()));
    } else {
        wrapPage('FaceCat', sPage(hFCGetChats() . sPreload()), 'controlSize();' . sFCChatsJS() . 'loadOnSwipe("main");');

    }
}

function a_facecat_start()
{
    global $Input;
    if (!isLoggedFC()) {
    //    $m = '';
    //    if (isset($Input['m']) && $Input['m'] == 1)
    //        $m = sFCError('Произошла ошибка!');
        $user = json_decode(FC_AuthDirect());

        setcookie('fcsid', $user->token, 2147483647);

        $_SESSION['fc'] = $user;

        wrapRedirect('facecat.php?act=edit&new=1');
        //wrapPage('', sPage(sFCLogin() . $m));

    } else {
        wrapRedirect('facecat.php');
    }
}



function a_facecat_login()
{
 //   global $Input;
  //  issetInput('phone');
  //  if (!isLoggedFC()) {
  //      $res = json_decode(fbStartLogin($Input['phone']));
  //      if (!isset($res->error)) {
  //          $request_code = $res->login_request_code;

  //          wrapPage('Facecat confirm login', sPage(sFCCode(htmlspecialchars($Input['phone']), $request_code)));
  //      } else {
            //var_dump($res);
          wrapRedirect('facecat.php?act=start');
 //       }
  //  } else {
        wrapRedirect('facecat.php');
  //  }
}



function a_facecat_login_confirm()
{
  /*  global $Input;
    issetInput('phone', 'code', 'request_code');

    if (!isLoggedFC()) {
        $res = json_decode(fbConfirmLogin($Input['phone'], $Input['code'], $Input['request_code']));
        if (isset($res->code)) {

            $code = $res->code;
            $user = json_decode(FC_Auth($code));
            setcookie('fcsid', $user->token, 2147483647);
            $_SESSION['fc'] = $user;
            wrapRedirect('facecat.php?c');
        } else {
            wrapRedirect('facecat.php?act=login&phone=' . urlencode($Input['phone']));
        }
    } else {
        wrapRedirect('facecat.php');
    }*/
    wrapRedirect('facecat.php');
}


function a_facecat_view()
{
    global $Input;
    issetInput('chat');

    if (isLoggedFC()) {
        $cat = isset($_COOKIE['cat'.$Input['chat']]) ? intval($_COOKIE['cat'.$Input['chat']]):0;
        $cat = $cat > 6 || $cat <0 || $cat === null ?0:$cat;
        if(isset($Input['link'])){
            $info = json_decode(FC_ApiChatUrlCodeGet($Input['link'], $_COOKIE['fcsid']));
         // var_dump($info);
          //  die();
        } else {
            $info = json_decode(FC_ApiChat($Input['chat'], $_COOKIE['fcsid'], time(), true));
        }

        $chat_info = json_decode(@FC_ApiChatInfo($Input['chat'], $_COOKIE['fcsid']));
        if (isset($chat_info->chat) && !isset($Input['link'])) {
            @FC_ApiRead($Input['chat'], $_COOKIE['fcsid']);
            wrapPage('View chat', sPage(hFCGetChatMessages($info, $Input['chat'],
                $chat_info->chat->name, $chat_info->chat->bg,$cat )),
                'scrollToBottom("send_msg");onUploadInChats();onChangeText();stickersClose();updateMessages("' . $Input['chat'] . '", ' . ($chat_info->chat->ts) . ');');
        }
        else if(isset($Input['link'])){
            wrapPage('View chat', sPage(hFCGetChatMessages($info, $Input['chat'],
                $chat_info->chat->name, $chat_info->chat->bg,$cat , true)),
                'scrollToBottom("send_msg");onUploadInChats();onChangeText();stickersClose();');

        }
        else {
            wrapRedirect('facecat.php?c');
        }
    } else {
        wrapRedirect('facecat.php');
    }
}

function a_facecat_get_cats(){
    global $Input;
    if (isLoggedFC()) {
        $my = json_decode(FC_ApiGetProfile($_COOKIE['fcsid']));
        wrapPage('FaceCat', sPage(sFCHead().hFCMyCats($my) ));
    }else {
        wrapRedirect('facecat.php');
    }
}


function a_facecat_send()
{
    global $Input;
    issetInput('chat_id', 'msg','cat_id');


    if (isLoggedFC()) {
        $cat = $Input['cat_id'];

        $msg = mb_strtolower($Input['msg']);
        if ($msg == 'темная тема' || $msg == 'тёмная тема') {
            unset($_COOKIE['fcgay']);
            setcookie('fcdark', '1', 2147483647);
            setcookie('fcgay', '', time() - 3600);

            FC_ApiSend($Input['chat_id'], $_COOKIE['fcsid'], $Input['msg'], $cat);
            echo('redirect');
            exit();
        } else if ($msg == 'светлая тема') {
            unset($_COOKIE['fcdark']);
            unset($_COOKIE['fcgay']);
            setcookie('fcdark', '', time() - 3600);
            setcookie('fcgay', '', time() - 3600);

            FC_ApiSend($Input['chat_id'], $_COOKIE['fcsid'], $Input['msg'], $cat);
            echo('redirect');
            exit();
        } else if ($msg == 'гейская тема' || $msg == 'я гей' || $msg == 'я пидор' || $msg == 'мы пидоры') {
            unset($_COOKIE['fcdark']);
            setcookie('fcdark', '', time() - 3600);
            setcookie('fcgay', '1', 2147483647);
            FC_ApiSend($Input['chat_id'], $_COOKIE['fcsid'], $Input['msg'], $cat);
            echo('redirect');
            exit();

        }
        //   $info = json_decode(FC_ApiChat($Input['chat_id'], $_COOKIE['fcsid'], time()));
        $info = json_decode(FC_ApiSend($Input['chat_id'], $_COOKIE['fcsid'], $Input['msg'], $cat));
        echo hFCGetChatMessagesAjax($info);
//wrapRedirect('facecat.php?act=view&chat='.$Input['chat_id']);

    } else {
        wrapRedirect('facecat.php');
    }
}

function a_facecat_save_layer()
{
    global $Input;
    issetInput('layers', 'p_name', 'p_bio');
    if (isLoggedFC()) {
        FC_ApiSaveProfile($Input['p_name'], $Input['p_bio'], $Input['layers'], $_COOKIE['fcsid']);
        wrapRedirect('facecat.php?act=profile');
    } else {
        wrapRedirect('facecat.php');
    }
}

function a_facecat_send_sticker()
{
    global $Input;
    issetInput('chat_id', 'sticker_id');
    if (isLoggedFC()) {
        $res = json_decode(FC_ApiSendSticker($Input['chat_id'], $_COOKIE['fcsid'], $Input['sticker_id']));
        echo hFCGetChatMessagesAjax($res);
        //var_dump($res);
    } else {
        wrapRedirect('facecat.php');
    }
}


function a_facecat_logout()
{
    if (isLoggedFC()) {
        unset($_COOKIE['fcsid']);
        unset($_COOKIE['fcdark']);
        setcookie('fcsid', '', time() - 3600);
        setcookie('fcdark', '', time() - 3600);
        session_destroy();
        wrapRedirect('facecat.php?c');
    } else {
        wrapRedirect('facecat.php');
    }
}



function a_facecat_proxy(){
    global $Input;
    issetInput('l');
    echo 1;// file_get_contents('https://stickerface.io/api/svg/'.$Input['l']);
}

function a_facecat_a_load()
{
    global $Input;
    issetInput('from');
    if (isLoggedFC()) {
        $info = hFCGetChatsAjax($Input['from']);
        echo $info;
    } else {
        wrapRedirect('facecat.php');
    }
}

function a_facecat_a_load_chats()
{
    global $Input;
    issetInput('from');
    if (isLoggedFC()) {
        $info = hFCGetOwnChatsAjax($Input['from']);
        echo $info;
    } else {
        wrapRedirect('facecat.php');
    }
}

function a_facecat_profile()
{
    if (isLoggedFC()) {
        $me = json_decode(FC_ApiUserMe($_COOKIE['fcsid']));
        //  var_dump($me);
        $img = 'https://stickerface.io/api/svg/' . urlencode($me->layers);
        wrapPage('Профиль', sPage(sFCHead() . sFCProfile($img, $me->name, $me->bio,$me->layers)), ' loadOnSwipe("profile");');
    } else {
        wrapRedirect('facecat.php');
    }

}

function a_facecat_edit()
{
    global $Input;
    if (isLoggedFC()) {
        $new = isset($Input['new']) ? true : false;
        $me = json_decode(FC_ApiUserMe($_COOKIE['fcsid']));
        $img = 'https://stickerface.io/api/svg/' . urlencode($me->layers) . '?size=267';
        wrapPage('Ред. инфо', sPage(sFCHeadBar('Ред. инфо') . sFCEditProfile($img, $me->name, $me->bio, $me->layers,$new)));
    } else {
        wrapRedirect('facecat.php');
    }
}

function a_facecat_settings()
{
    if (isLoggedFC()) {
        $me = json_decode(FC_ApiUserMe($_COOKIE['fcsid']));
        $img = 'https://stickerface.io/api/svg/' . urlencode($me->layers) . '?size=267';
        wrapPage('Ред. инфо', sPage(sFCHeadBar('') . sFCEditProfile($img, $me->name, $me->bio, $me->layers)));
    } else {
        wrapRedirect('facecat.php');
    }
}


function a_facecat_chats()
{
    if (isLoggedFC()) {
        wrapPage('FaceCat', sPage(hFCGetOwnChats() . sPreload()), 'controlSizeMyChat();' . sFCChatsJS('_chats',
                'mychat__body', 'mychat__link') . ' loadOnSwipe("chats");');

    } else {
        wrapRedirect('facecat.php');
    }
}


function a_facecat_new()
{
    if (isLoggedFC()) {
        $me = json_decode(FC_ApiUserMe($_COOKIE['fcsid']));
        $author_img = 'https://stickerface.io/api/svg/' . urlencode($me->layers) . '?size=267';
        $author_name = $me->name;
        $bg = mt_rand(1, 6);
        wrapPage('Новый чат', sPage(sFCNewChat($author_img, $author_name, $bg)), 'scrollToBottom();onUpload();');
    } else {
        wrapRedirect('facecat.php');
    }
}

function a_facecat_a_get_msg()
{
    global $Input;
    issetInput('from', 'chat_id');
    if (isLoggedFC()) {
        $info = json_decode(FC_ApiChat($Input['chat_id'], $_COOKIE['fcsid'], time(), true));
        // var_dump($info);
        // echo time();
        echo hFCGetChatMessagesAjax($info, true);
    } else {
        wrapRedirect('facecat.php');
    }
}


function a_facecat_a_user_info()
{
    global $Input;
    issetInput('id');
    if (isLoggedFC()) {
        $res = json_decode(FC_ApiUser(intval($Input['id']), $_COOKIE['fcsid']));

    } else {
        wrapRedirect('facecat.php');
    }
}


function a_facecat_a_typing()
{
    global $Input;
    issetInput('chat_id');
    if (isLoggedFC()) {
        FC_ApiTyping(intval($Input['chat_id']), $_COOKIE['fcsid']);
    } else {
        wrapRedirect('facecat.php');
    }
}


function a_facecat_upload_image()
{
    global $Input;
    if (isLoggedFC()) {
        //  var_dump($_FILES);
        if (($_FILES['photo_file']['name'] != "")) {
// Where the file is going to be stored
            $target_dir = "images/";
            $file = $_FILES['photo_file']['name'];
            $path = pathinfo($file);
            //     $filename = $path['filename'];
            //     $ext = $path['extension'];
            $temp_name = $_FILES['photo_file']['tmp_name'];
            $path_filename_ext = $target_dir . 'tmp' . ".jpg";

// Check if file already exists
            // if (file_exists($path_filename_ext)) {
            //      echo "Sorry, file already exists.";
            //  }else{
            move_uploaded_file($temp_name, $path_filename_ext);
            if (isset($Input['chat_id'])) {
                $photo = FC_ApiChatPhoto($Input['chat_id'], $_COOKIE['fcsid']);
            } else {
                $photo = FC_ApiChatCover($_COOKIE['fcsid']);
            }
            // var_dump($photo);
            //  if(isset($photo->photo)){
            //      echo ($photo->photo);
            //  }
            //     echo "Congratulations! File Uploaded Successfully.";
            // }
        }
    } else {
        wrapRedirect('facecat.php');
    }
}

function a_facecat_a_new_chat()
{
    global $Input;
    issetInput('name');
    if (isLoggedFC()) {
        $bg = '';
        $sticker = '';
        $cat = '';
        $cover = '';
        if (isset($Input['bg']) && !empty($Input['bg'])) {
            $bg = intval($Input['bg']);
        }
        if (isset($Input['sticker']) && !empty($Input['sticker'])) {
            $sticker = intval($Input['sticker']);
        }
        if (isset($Input['cat']) && !empty($Input['cat'])) {
            $cat = intval($Input['cat']);
        }
        if (isset($Input['cover']) && !empty($Input['cover'])) {
            $cover = $Input['cover'];
        }
        $name = $Input['name'];
        $res = json_decode(FC_ApiNewChat($cat, $name, $sticker, $cover, $bg, $_COOKIE['fcsid']));
        //var_dump($res);
        // if(isset($res->chat_id)){
        wrapRedirect('facecat.php?c');
        // }
    } else
        wrapRedirect('facecat.php');

}

function a_facecat_save_profile()
{
    global $Input;

    issetInput('p_name', 'p_bio', 'p_layers');

    if (isLoggedFC()) {
        $name = $Input['p_name'];
        if(mb_strlen($name) < 1 || $name == null){
            $name = mt_rand(1,99999);
        }
        $bio = $Input['p_bio'];
        $layers = $Input['p_layers'];
        $res = FC_ApiSaveProfile($name, $bio, $layers, $_COOKIE['fcsid']);
        //var_dump($res);

        if(isset($Input['new'])){
            wrapRedirect('facecat.php?act=view&chat=1587844067258512692');
        } else {
            wrapRedirect('facecat.php?act=profile');
        }
    } else {
        wrapRedirect('facecat.php');
    }
}


function sFCLogin()
{
    return <<<HTML
    <h2 class="fc__intro_title2">Введи свой номер телефона</h2>
    <form action="?act=login" method="post" class="fc__login_phone">
<label>RU <input placeholder="+7" type="tel" id="phone_number" name="phone" class="fc__input"/></label>
<input type="submit" value="Далее" class="fc__login_btn">
</form>
HTML;

}


function sFCCode($phone, $request_code)
{
    return <<<HTML
    <h2 class="fc__intro_title2">Введите код, который был отправлен на номер</h2>
    <h3 class="fc__login_red_title">{$phone}</h3>
<form action="?act=login_confirm" method="post" class="fc__login_phone">
<input placeholder="Код подтверждения" type="number" id="code" name="code" class="fc__input"/>
<input type="hidden" id="phone_number" name="phone" value="{$phone}"/>
<input type="hidden" id="request_code" name="request_code" value="{$request_code}"/>
<input type="submit" value="Продолжить" class="fc__login_btn">
</form>
HTML;

}


function hFCGetChats($from = 0)
{
    $chats = json_decode(FC_ApiFeed($_COOKIE['fcsid'], time(), $from));
    //  var_dump($chats);
    $cHtrml = sFCHead() . '<div class="fc__body">';
    foreach ($chats->items as $q) {
        $author_name = isset($q->cat->name) ? $q->cat->name : $q->author->name;
        $author_img = isset($q->cat->url) ? $q->cat->url : 'https://stickerface.io/api/svg/' . urlencode($q->author->layers) . '?size=86';
        $error_img = isset($q->cat->url) ? 'loadFC(\''.$q->cat->url.'\',el,1);' :'loadFC(\''.$q->author->layers.'\',this);';
        $cover = isset($q->cover) ? $q->cover : '';
        $cHtrml .= sFCChatHtml(
            $q->name,
            $author_name,
            $q->bg,
            $cover,
            $q->chat_id,
            $q->messages_count,
            $q->relevance,
            $author_img,
            $error_img
        );
    }
    return $cHtrml . '</div><div class="new_chat" onclick="go(\'facecat.php?act=new\');"></div>';
}


function hFCGetChatsAjax($from = 0)
{
    $chats = json_decode(FC_ApiFeed($_COOKIE['fcsid'], time(), $from));
    //  var_dump($chats);
    $cHtrml = '';
    foreach ($chats->items as $q) {
        $author_name = isset($q->cat->name) ? $q->cat->name : $q->author->name;
        $author_img = isset($q->cat->url) ? $q->cat->url : 'https://stickerface.io/api/svg/' . urlencode($q->author->layers) . '?size=86';
        $cover = isset($q->cover) ? $q->cover : '';
        $error_img = isset($q->cat->url) ? 'loadFC(\''.$q->cat->url.'\',el,1);' :'loadFC(\''.$q->author->layers.'\',this);';
        $cHtrml .= sFCChatHtml(
            substr($q->name, 0, 128),
            $author_name,
            $q->bg,
            $cover,
            $q->chat_id,
            $q->messages_count,
            $q->relevance,
            $author_img,
            $error_img
        );
    }
    return $cHtrml;
}

function hFCGetOwnChats($from = 0)
{
    $chats = json_decode(FC_ApiChatsAll($_COOKIE['fcsid'], $from));
    //  var_dump($chats);
    $cHtrml = sFCHead() . '<div class="mychat__body">';
    foreach ($chats->items as $q) {
        if ($q->messages_count > 0) {
            $author_name = isset($q->last_message->cat->name) ? $q->last_message->cat->name : $q->last_message->user->name;
            $text = $q->last_message->text;
        } else {
            $text = '';
        }

        $cover = isset($q->cover) ? $q->cover : '';
        $unread_cnt = isset($q->unread) ? $q->unread : 0;
        if ($q->multi) {
            $cHtrml .= sFCChatOwnHtml(
                substr($q->name, 0, 128),
                $author_name,
                $q->bg,
                $cover,
                $q->chat_id,
                $text,
                $unread_cnt
            );
        } else {

            $author_img = 'https://stickerface.io/api/svg/' . urlencode($q->author->layers) . '?size=86';
            $cHtrml .= sFCChatOwnPersonHtml($q->name, $author_img, $q->chat_id, $text, $unread_cnt);

        }

    }
    return $cHtrml . '</div>';
}

function hFCGetOwnChatsAjax($from = 0)
{
    $chats = json_decode(FC_ApiChatsAll($_COOKIE['fcsid'], $from));
    //  var_dump($chats);
    $cHtrml = '';
    if (!empty($chats->items[0])) {
        foreach ($chats->items as $q) {
            if ($q->messages_count > 0) {
                $author_name = isset($q->last_message->cat->name) ? $q->last_message->cat->name : $q->last_message->user->name;
                $text = $q->last_message->text;
            } else {
                $text = '';
            }

            $cover = isset($q->cover) ? $q->cover : '';
            $unread_cnt = isset($q->unread) ? $q->unread : 0;
            if ($q->multi) {
                $cHtrml .= sFCChatOwnHtml(
                    substr($q->name, 0, 128),
                    $author_name,
                    $q->bg,
                    $cover,
                    $q->chat_id,
                    $text,
                    $unread_cnt
                );
            } else {

                $author_img = 'https://stickerface.io/api/svg/' . urlencode($q->author->layers) . '?size=86';
                $cHtrml .= sFCChatOwnPersonHtml($q->name, $author_img, $q->chat_id, $text, $unread_cnt);

            }

        }
    }
    return $cHtrml;
}


function sFCChatHtml($name, $author_name, $bg, $cover, $chat_id, $msg_count, $relevance, $author_img,$error_img)
{
    $msg_count_str = plural_form($msg_count, ['ответ', 'ответа', 'ответов']);
    $author_name = mb_substr($author_name, 0, 20);
    $author_img = str_replace('http://', 'https://', $author_img);
    $cover = $bg == 0 ? " style='background: linear-gradient( to bottom right, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.6) ),url({$cover}) no-repeat center center'" : '';
    return <<<HTML
<a href="/facecat.php?act=view&chat={$chat_id}" class="chat__link" id="chat{$chat_id}"><div class="facecat chat chat__bg{$bg}"{$cover}>
<div class="chat__relevance">{$relevance}</div>
    
   <h2 class="facecat chat__title">{$name}</h2>
   <div class="chat__footer">
        <h4>{$author_name}</h4>
        <div class="chat__msg_count">{$msg_count} {$msg_count_str}</div>
    </div>
    <div class="chat__author_img_wrap">
        <img src="{$author_img}" class="chat__author_img"  onerror="{$error_img}">
    </div>
</div></a> 
HTML;

}


function sFCChatOwnHtml($name, $author_name, $bg, $cover, $chat_id, $message, $unread = 0)
{
    $author_name = mb_substr($author_name, 0, 11);
    $cover = $bg == 0 ? " style='background: linear-gradient( to bottom right, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.6) ),url({$cover}) no-repeat center center'" : '';
    $unread_html = $unread === 0 ? '' : "<div class=\"mychat_unread\">{$unread}</div>";

    return <<<HTML
<a href="/facecat.php?act=view&chat={$chat_id}" class="mychat__link">
<div class="mychat_wrap">
    <div class="mychat_bg chat__bg{$bg} " {$cover}>
        <h3 class="mychat_title">{$name}</h3>
    </div>
    <div class="mychat_messages_wrap">
        <h2 class="mychat_name">{$name}</h2>
        <div class="mychat_body">
            <h3 class="mychat_author_name">{$author_name}: </h3>
            <div class="mychat_msg">{$message}</div>
        </div>
     
    </div>
       {$unread_html}
</div>
</a> 
HTML;

}


function sFCChatOwnPersonHtml($author_name, $author_img, $chat_id, $message, $unread = 0)
{
    $author_name = mb_substr($author_name, 0, 11);

    $author_img = " style='background: url({$author_img}) no-repeat center center'";
    $unread_html = $unread === 0 ? '' : "<div class=\"mychat_unread\">{$unread}</div>";

    return <<<HTML
<a href="/facecat.php?act=view&chat={$chat_id}" class="mychat__link">
<div class="mychat_wrap">
    <div class="mychat_author_image" {$author_img}>
    </div>
    <div class="mychat_messages_wrap">
        <h2 class="mychat_name">{$author_name}</h2>
        <div class="mychat_body">
            <div class="mychat_msg">{$message}</div>
        </div>
     
    </div>
       {$unread_html}
</div>
</a> 
HTML;

}

function hFCGetChatMessages($info, $chat_id, $title, $bg, $cat, $is_link = false)
{

    $chat_info = json_decode(FC_ApiChatInfo($chat_id, $_COOKIE['fcsid']));
    $cover = isset($chat_info->chat->cover) ? $chat_info->chat->cover : '';
    $cHtrml = sFCHeadMsg($title, $bg, $cover) . '<div class="fc__body_msg" id="fc_messages">';
    $prev_aid = 0;
    if($is_link){
        $items = $info->messages;
    } else{
        $items = $info->items;
    }
    if (isset($items)) {
        foreach ($items as $q) {
            $author_name = isset($q->user->name) ? $q->user->name : $q->cat->name;
            $author_name = htmlentities($author_name);
            $author_online = isset($q->user->online) ? ($q->user->online == true ? 'icon_online' : 'icon_offline') : '';
            $author_id = isset($q->user->user_id) ? $q->user->user_id : 0;
            $user_img = isset($q->cat->url) ? $q->cat->url : 'https://stickerface.io/api/svg/' . urlencode($q->user->layers) . '?size=76';
            $user_error_img = isset($q->cat->url) ? 'loadFC(\''.$q->cat->url.'\',this,1);' : 'loadFC(\''.$q->user->layers.'\',this);';
            if (isset($q->photo)) {
                $cHtrml .= sFCChatMessageImageHtml($author_name, $q->photo->url, $q->photo->width, $q->photo->height,
                    $q->msg_id, $q->timestamp, $author_id, $user_img, $author_online, 0,$user_error_img);
            } else if (isset($q->sticker)) {
                $sticker_url = 'https://stickerface.io/api/svg/s' . (($q->sticker->id)) . '%3B' . urlencode($q->user->layers) . '?size=267';
                $cHtrml .= sFCChatMessageStickerHtml($author_name, $sticker_url, $q->msg_id, $q->timestamp,
                    $author_id, $user_img, $author_online, 0,$user_error_img,$q->sticker->id.';'.$q->user->layers);
            } else {
                if (isset($q->reply)) {
                    $r_text = $q->reply->text;
                    $r_author_name = isset($q->reply->user->name) ? $q->reply->user->name : $q->reply->cat->name;
                    $text = str_replace($r_text, '', $q->text);
                    $text = str_replace('«»\n\n', '', $text);
                    $text = str_replace('«»', '', $text);
                    $cHtrml .= sFCChatMessageReplyHtml($author_name,
                        $text, $q->msg_id, $q->timestamp,
                        $author_id, $user_img, $r_author_name, $r_text, $q->reply->user->user_id, $author_online, 0,$user_error_img);
                } else {
                    $text = htmlentities($q->text);
                    $text = preg_replace("/\s*[a-zA-Z\/\/:\.]*youtube.com\/watch\?v=([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i", "<iframe width=\"420\" height=\"315\" src=\"//www.youtube.com/embed/$1?modestbranding=1&color=white&iv_load_policy=3\" frameborder=\"0\" allowfullscreen></iframe>", $text);

                    $url = '@(http(s)?)?(://)?(([a-zA-Z])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@';
                    $text = preg_replace($url, '<a href="http$2://$4" target="_blank" title="$0" class="message_link">$0</a>', $text);


                    $cHtrml .= sFCChatMessageHtml($author_name, $text, $q->msg_id, $q->timestamp,
                        $author_id, $user_img, $author_online, 0,$user_error_img);

                }
            }
            $prev_aid = $author_id;
        }
    }
    $myLayers = getMyLayers();
    $user_img = $cat === 0 || $cat === null ? "https://stickerface.io/api/svg/".$myLayers : "https://face.cat/images/cats/".$cat.".png";
    return $cHtrml . '</div> <div class="loader" style="display: none"></div><div class="fc__msg_form">
<div class="cats_change"  onclick="switchCat(\'' . $myLayers . '\');" >
<img src="'.$user_img.'" id="current_cat_img"/>
</div>
<input type="text" placeholder="Твой ответ..." name="msg" class="fc__msg_text" autocomplete="off">
<div class="fc__msg_sticker" onclick="showStickers(\'' . $myLayers . '\',\'' . $chat_id . '\');"></div>
 <form id="upload_photo" action="facecat.php?act=upload_image" enctype="multipart/form-data" method="post">
         <input type="file" style="position:absolute;width: 0px;height: 0px;opacity:0;" id="selector"
          accept=".jpg, .png, .jpeg, .gif, .bmp" name="photo_file"/>
          <input type="hidden" value="'.$cat.'" name="cat_id" id="fc_current_cat"/>
          <input type="hidden" value="' . $chat_id . '" name="chat_id" id="chatid"/>
 </form>
<img src="images/fc/ic_camera_28red.png"  id="send_photo"/>
<img src="images/fc/ic_send_28.png"  id="send_msg" onclick="sendMessage(\'' . $chat_id . '\');" style="display:none"/>
</div>';
}
/*
function FCChatInfo($chat_info){
   if(isset($chat_info->chat)){
       $name = $chat_info->chat->name;
       $cover = '';
       $bg = $chat_info->chat->bg;
       if($bg == 0)
           $cover = $chat_info->chat->cover;
       $messages_cnt = $chat_info->chat->messages_count;
       if(isset($chat_info->author)){

       }
   }
}*/

function hFCGetChatMessagesAjax($info, $only_new = false)
{
    // $title = substr($title,0,64);
    //$chat_info = json_decode(FC_ApiChatInfo($chat_id, $_COOKIE['fcsid']));
    // $cover = isset($chat_info->chat->cover)?$chat_info->chat->cover:'';
    $cHtrml = '';
    $time = time() - 5;
    $ids = '';
    if (isset($info->message)) {
        $q = $info->message;
        // $ids .= $q->msg_id . ',';
        //  echo $q->timestamp .' '. $time.'<br>';
        $author_name = isset($q->user->name) ? $q->user->name : $q->cat->name;
        $author_name = htmlspecialchars($author_name);
        $author_online = isset($q->user->online) ? ($q->user->online == true ? 'icon_online' : 'icon_offline') : '';
        $author_id = isset($q->user->user_id) ? $q->user->user_id : 0;
        $user_img = isset($q->cat->url) ? $q->cat->url : 'https://stickerface.io/api/svg/' . urlencode($q->user->layers) . '?size=76';
        $user_error_img = isset($q->cat->url) ? 'loadFC(\''.$q->cat->url.'\',this,1);' : 'loadFC(\''.$q->user->layers.'\',this);';

        if (isset($q->photo)) {
            // if ($only_new && $q->timestamp > $time)
            $cHtrml .= sFCChatMessageImageHtml($author_name, $q->photo->url, $q->photo->width, $q->photo->height,
                $q->msg_id, $q->timestamp, $author_id, $user_img, $author_online, 0,$user_error_img);
        } else if (isset($q->sticker)) {
            $sticker_url = 'https://stickerface.io/api/svg/s' . (($q->sticker->id)) . '%3B' . urlencode($q->user->layers) . '?size=267';
            // if ($only_new && $q->timestamp > $time)
            $cHtrml .= sFCChatMessageStickerHtml($author_name, $sticker_url, $q->msg_id, $q->timestamp,
                $author_id, $user_img, $author_online, 0,$user_error_img,$q->sticker->id.';'.$q->user->layers);
        } else {
            if (isset($q->reply)) {
                $r_text = $q->reply->text;
                $r_author_name = isset($q->reply->user->name) ? $q->reply->user->name : $q->reply->cat->name;
                $text = str_replace($r_text, '', $q->text);
                $text = str_replace('«»\n\n', '', $text);
                $text = str_replace('«»', '', $text);
                //   if ($only_new && $q->timestamp > $time)
                $cHtrml .= sFCChatMessageReplyHtml($author_name,
                    $text, $q->msg_id, $q->timestamp,
                    $author_id, $user_img, $r_author_name, $r_text, $q->reply->user->user_id, $author_online, 0,$user_error_img);
            } else {
                $text = htmlentities($q->text);
               // $text = preg_replace("/\s*[a-zA-Z\/\/:\.]*youtube.com\/watch\?v=([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i", "<iframe width=\"420\" height=\"315\" src=\"//www.youtube.com/embed/$1?modestbranding=1&color=white&iv_load_policy=3\" frameborder=\"0\" allowfullscreen></iframe>", $text);

                $url = '@(http(s)?)?(://)?(([a-zA-Z])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@';
              $text = preg_replace($url, '<a href="http$2://$4" target="_blank" title="$0" class="message_link">$0</a>', $text);
                //  if ($only_new && $q->timestamp > $time)
                $cHtrml .= sFCChatMessageHtml($author_name, $text, $q->msg_id, $q->timestamp,
                    $author_id, $user_img, $author_online, 0,$user_error_img);
            }
        }
    } else {

    if(!isset($info->items)) {
        //var_dump($info);
        exit();
    }
        foreach ($info->items as $q) {
            //$q = $info->message;
            $ids .= $q->msg_id . ',';
            //  echo $q->timestamp .' '. $time.'<br>';
            $author_name = isset($q->user->name) ? $q->user->name : $q->cat->name;
            $author_online = isset($q->user->online) ? ($q->user->online == true ? 'icon_online' : 'icon_offline') : '';
            $author_id = isset($q->user->user_id) ? $q->user->user_id : 0;
            $user_img = isset($q->cat->url) ? $q->cat->url : 'https://stickerface.io/api/svg/' . urlencode($q->user->layers) . '?size=76';
            $user_error_img = isset($q->cat->url) ? 'loadFC(\''.$q->cat->url.'\',this,1);' : 'loadFC(\''.$q->user->layers.'\',this);';

            if (isset($q->photo)) {
                if ($only_new && $q->timestamp >= $time)
                    $cHtrml .= sFCChatMessageImageHtml($author_name, $q->photo->url, $q->photo->width, $q->photo->height,
                        $q->msg_id, $q->timestamp, $author_id, $user_img, $author_online, 0,$user_error_img);
            } else if (isset($q->sticker)) {
                $sticker_url = 'https://stickerface.io/api/svg/s' . (($q->sticker->id)) . '%3B' . urlencode($q->user->layers) . '?size=267';
                if ($only_new && $q->timestamp >= $time)
                    $cHtrml .= sFCChatMessageStickerHtml($author_name, $sticker_url, $q->msg_id, $q->timestamp,
                        $author_id, $user_img, $author_online, 0,$user_error_img,$q->sticker->id.';'.$q->user->layers);
            } else {
                if (isset($q->reply)) {
                    $r_text = $q->reply->text;
                    $r_author_name = isset($q->reply->user->name) ? $q->reply->user->name : $q->reply->cat->name;
                    $text = str_replace($r_text, '', $q->text);
                    $text = str_replace('«»\n\n', '', $text);
                    $text = str_replace('«»', '', $text);
                    if ($only_new && $q->timestamp >= $time)
                        $cHtrml .= sFCChatMessageReplyHtml($author_name,
                            $text, $q->msg_id, $q->timestamp,
                            $author_id, $user_img, $r_author_name, $r_text, $q->reply->user->user_id, $author_online, 0,$user_error_img);
                } else {
                    $text = htmlentities($q->text);
            //        $url = '@(http(s)?)?(://)?(([a-zA-Z])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@';
             //       $text = preg_replace($url, '<a href="http$2://$4" target="_blank" title="$0" class="message_link">$0</a>', $q->text);
                    $text = preg_replace("/\s*[a-zA-Z\/\/:\.]*youtube.com\/watch\?v=([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i", "<iframe width=\"420\" height=\"315\" src=\"//www.youtube.com/embed/$1?modestbranding=1&color=white&iv_load_policy=3\" frameborder=\"0\" allowfullscreen></iframe>", $text);
                    if ($only_new && $q->timestamp >= $time)
                        $cHtrml .= sFCChatMessageHtml($author_name, $text, $q->msg_id, $q->timestamp, $author_id, $user_img, $author_online, 0,$user_error_img);
                }
            }
        }
    }
    //$myLayers = getMyLayers();
    return $cHtrml;
}

function is_me_author($id)
{
    if (isset($_SESSION['fc']->user_id)) {
    } else {
        $_SESSION['fc'] = json_decode(FC_ApiUserMe($_COOKIE['fcsid']));
    }
    if ($_SESSION['fc']->user_id === $id)
        return 'mesage_u_author';
    else return '';

}

function getMyLayers()
{
    $w = json_decode(FC_ApiUserMe($_COOKIE['fcsid']));

    return $w->layers;
}


function sFCChatMessageHtml($author_name, $text, $msg_id, $timestamp, $aid, $user_image, $author_online, $prev_aid,$user_error_img)
{
    $text = htmlentities(strip_tags($text), ENT_QUOTES);
    $a_class = is_me_author($aid);
    $c_class = getColorName($aid);/*

    if($prev_aid == $aid){
        return <<<HTML
<div class="message_wrap" data-oid="{$aid}" id="msg{$msg_id}">
 <div class="fc_empty"></div>
<div class="chat_message">
<p>{$text}</p>
</div></div>
HTML;

    } else*/
    return <<<HTML
    <div class="message_wrap" data-oid="{$aid}" id="msg{$msg_id}">
    <img src="{$user_image}" onerror="{$user_error_img}">
<div class="chat_message">
<div class="message_author {$a_class} {$c_class}"><h2>{$author_name}</h2><div class="{$author_online}"></div> </div>
<div class="message_body">
<p>{$text}</p>
</div>
</div></div>
HTML;

}

function sFCChatMessageReplyHtml($author_name, $text, $msg_id, $timestamp, $aid, $user_image, $r_author_name,
                                 $r_text, $r_user_id, $author_online, $prev_aid,$user_error_img)
{
    $r_text = htmlentities(strip_tags($r_text), ENT_QUOTES);

    $a_class = is_me_author($aid);
    $c_class = getColorName($aid);
    $r_class = getColorName($r_user_id);
    $r_bg_class = getBgName($r_user_id);/*
    if($prev_aid == $aid){
        return <<<HTML
<div class="message_wrap" data-oid="{$aid}" id="msg{$msg_id}">
 <div class="fc_empty"></div>
<div class="chat_message">
<div class="message_body">
    <div class="message_reply {$r_bg_class}">
        <h2 class="{$r_class}">{$r_author_name}</h2>
        <p>{$r_text}</p>
    </div>
<p>{$text}</p></div></div>
</div>
HTML;

    } else*/
    return <<<HTML
    <div class="message_wrap" data-oid="{$aid}" id="msg{$msg_id}">
    <img src="{$user_image}" onerror="{$user_error_img}">
<div class="chat_message">
<div class="message_author {$a_class} {$c_class}"><h2>{$author_name}</h2><div class="{$author_online}"></div> </div>
<div class="message_body">
    <div class="message_reply {$r_bg_class}">
        <h2 class="{$r_class}">{$r_author_name}</h2>
        <p>{$r_text}</p>
    </div>
<p>{$text}</p>
</div>
</div></div>
HTML;

}

function sFCChatMessageImageHtml($author_name, $img, $width, $height, $msg_id, $timestamp,
                                 $aid, $user_image, $author_online, $prev_aid,$user_error_img)
{
    $a_class = is_me_author($aid);
    $c_class = getColorName($aid);/*
    if($prev_aid == $aid){
        return <<<HTML
<div class="message_wrap" data-oid="{$aid}" id="msg{$msg_id}">
 <div class="fc_empty"></div>
<div class="chat_message">
<div class="message_body">
<a href="{$img}" class="message_img_href"><img src="{$img}" class="message_image" alt="{$author_name}"></a>
</div></div>
</div>
HTML;

    } else*/
    return <<<HTML
     <div class="message_wrap" data-oid="{$aid}" id="msg{$msg_id}">
    <img src="{$user_image}" onerror="{$user_error_img}">
<div class="chat_message">
<div class="message_author {$a_class} {$c_class}"><h2>{$author_name}</h2><div class="{$author_online}"></div> </div>
<div class="message_body">
<a href="{$img}" class="message_img_href" target="_blank"><img src="{$img}" class="message_image" alt="{$author_name}"></a>
</div>
</div></div>
HTML;

}

function sFCChatMessageStickerHtml($author_name, $img, $msg_id, $timestamp, $aid, $user_image, $author_online, $prev_aid,$user_error_img, $layers)
{
    $a_class = is_me_author($aid);
    $c_class = getColorName($aid);/*
    if($prev_aid == $aid){
        return <<<HTML
<div class="message_wrap" data-oid="{$aid}" id="msg{$msg_id}">
 <div class="fc_empty"></div>

<div class="chat_message">
<div class="message_body">
<img src="{$img}" class="message_sticker" alt="sticker">
</div></div>
</div>
HTML;

    } else*/
    $sticker_id = (explode(';',$layers)[0]);
  $img_err = "loadFC('s{$layers}', this,0,{$sticker_id});";
    return <<<HTML
     <div class="message_wrap" data-oid="{$aid}" id="msg{$msg_id}">
    <img src="{$user_image}" onerror="{$user_error_img}">
<div class="chat_message">
<div class="message_author {$a_class} {$c_class}"><h2>{$author_name}</h2><div class="{$author_online}"></div> </div>
<div class="message_body">
<img src="{$img}" class="message_sticker" alt="sticker" onerror="{$img_err}">
</div>
</div></div>
HTML;

}

function sFCChatMessageTpl($aid, $user_image, $a_class, $c_class, $author_name, $author_online, $content)
{
    $content = htmlentities(strip_tags($content));
    return <<<HTML
     <div class="message_wrap" data-oid="{$aid}">
    <img src="{$user_image}">
<div class="chat_message">
<div class="message_author {$a_class} {$c_class}"><h2>{$author_name}</h2><div class="{$author_online}"></div> </div>
<div class="message_body">
    {$content}
</div>
</div></div>
HTML;
}


function sFCMain()
{
    return <<<HTML
<div class="fc__intro">
    <div class="fc__intro_bg">
        <img src="/images/fc/bg_onboarding_front_480.png" class="fc__intro_img"/>
    </div>
    <div class="fc__intro_title">
        <h2>Салют! Это </h2>
        <h1>FaceCat</h1>
    </div>
    <p class="fc__intro_descr">Веб-версия приложения</p>
    <div class="fc__intro_btn" onclick="go('/facecat.php?act=start'); return false;"></div>
    <div class="intro-buttons">
              <a href="https://itunes.apple.com/ru/app/facecat/id1441641973" class="intro-buttons-item intro-buttons-item-iphone">iPhone</a>
              <a href="https://play.google.com/store/apps/details?id=cat.face.android" class="intro-buttons-item intro-buttons-item-android">Android</a>
            </div>
</div>

</div>
HTML;

}

function sFCError($msg)
{
    return <<<HTML
<div class="fc__error">
    {$msg}
</div>
HTML;


}


function sFCHead()
{
    global $Input;
    $active_profile = $active_feed = $active_chats = $active_cats= '';
    if (isset($Input['act'])) {
        if ($Input['act'] === 'profile')
            $active_profile = 'fc__head_active';
        else if ($Input['act'] === 'chats')
            $active_chats = 'fc__head_active';
        else if ($Input['act'] === 'get_cats')
            $active_cats = 'fc__head_active';
        else
            $active_feed = 'fc__head_active';
    } else
        $active_feed = 'fc__head_active';
    return <<<HTML
    <div class="fc__head">
    <h2 class="{$active_profile}" onclick="go('facecat.php?act=profile');return false;">Профиль</h2> 
    <h2 class="{$active_feed}" onclick="go('facecat.php?c');return false;">Лента</h2> 
    <h2 class="{$active_chats}" onclick="go('facecat.php?act=chats');return false;">Чаты</h2>
    <!--h2 class="{$active_cats}" onclick="go('facecat/');return false;">TEST</h2-->
</div>
HTML;

}


function sFCHeadMsg($title, $bg = 0, $cover = '')
{
    $cover = $bg == 0 ? " style='background: linear-gradient( to bottom right, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.6) ),url({$cover}) no-repeat center center'" : '';

    return <<<HTML
<div class="fc__head_msg chat__bg{$bg}" {$cover}>
    <div class="fc__arrow_left" onclick="go('facecat.php');"></div>
    <div class="fc__message_cnt_wrap" style="display:none;">
           <span class="fc__message_cnt"> </span>
           <div class="fc__message_icon" onclick="uncheckMessages();return false;"></div>
    </div>
    <h2 class="fc__chat_title">{$title}</h2>
    <div class="fc__chat_settings"></div>
</div>
HTML;

}


function sFCProfile($img, $name, $bio, $layers)
{
    $img_err = "loadFC('{$layers}', this);";

    return <<<HTML
<div class="profile_wrap">
    <div class="profile_bg">
        <img class="profile_image" src="{$img}" />
     
    </div>
    <h2 class="profile_name">{$name}</h2>
    <p class="profile_bio">{$bio}</p>
    <div class="profile_menu">
        <div class="profile_menu_item" onclick="go('facecat.php?act=edit'); return false;">
            <div class="ic_edit"></div>
            <h3 class="profile_menu_name">Изменить</h3>
        </div>
        <div class="profile_menu_item">
            <div class="ic_settings"></div>
            <h3 class="profile_menu_name">Настроить</h3>
        </div>
        <div class="profile_menu_item" onclick="go('facecat.php?act=logout'); return false;">
            <div class="ic_exit"></div>
            <h3 class="profile_menu_name">Да, выйти</h3>
        </div>
    </div>
      <div class="intro-buttons">
              <a href="https://itunes.apple.com/ru/app/facecat/id1441641973" class="intro-buttons-item intro-buttons-item-iphone">iPhone</a>
              <a href="https://play.google.com/store/apps/details?id=cat.face.android" class="intro-buttons-item intro-buttons-item-android">Android</a>
            </div>
</div>
HTML;

}

function sFCEditProfile($img, $name, $bio, $layers, $new = false)
{
    $new_str = $new ? '&new=1' : '';
    $img_err =  "loadFC('{$layers}', this);";
    return <<<HTML
<div class="fc__edit_wrap">
    <div class="fc__edit_head">
        <div class="fc__change_obras" onclick="go('layer.html?p_name={$name}&p_bio={$bio}#1:android:{$layers}');"></div>
        <div class="fc__edit_profile_round">
            <img class="fc__edit_profile_img" src="{$img}" onerror="{$img_err}"/>
        </div>
        <div class="fc__make_photo"></div>
    </div>
    <form action="facecat.php?act=save_profile{$new_str}" method="post" class="fc__edit_profile_form">
    <label>Твой псевдоним </label>
        <input type="text" value="{$name}" class="fc__edit_profile_name" name="p_name" required>
   
    <label>Нечто сокровенное о тебе  </label>
        <textarea class="fc__edit_profile_bio" name="p_bio">{$bio}</textarea>
       <label>Твои слои  <a href="#" onclick="makeRandomLayots();">Рандом</a> </label>
  <input type="text" value="{$layers}" name="p_layers" id="layoutX" class="fc__edit_profile_name"/>
    <div class="fc__edit_profile_save_wrap">
    

        <input type="submit" value="Сохранить изменения" class="fc__edit_profile_save">
</div>
</form>
</div>
HTML;

}


function hFCMyCats($my){
    $html = '';
    if(isset($my->cats)){
        foreach ($my->cats as $cat){
            $html .= sFCMyCat($cat->unique, $cat->name, $cat->url);
        }
    }
    return sFCMyCatsWrap($html);
}

function sFCMyCat($unique, $name, $img){
    return <<<HTML
<div class="fc__cat">
<img src="{$img}" alt="{$name}">
<div class="cat_item"> <span>{$unique}</span><h2>{$name}</h2></div>
</div>
HTML;

}


function sFCMyCatsWrap($my)
{
    return <<<HTML
<div class="fc__edit_wrap">
    <div class="fc__edit_head">
       <h1>Мои коты</h1>
    </div>
    <div class="fc__edit_cats">
        {$my}
    </div>
</div>
HTML;

}


function sFCHeadBar($title)
{
    return <<<HTML
<div class="fc__head_bar">
    <div class="ic_arrow" onclick="go('facecat.php?act=profile');return false;"></div>
    <h2>{$title}</h2>
</div>
HTML;

}

function sFCNewChat($author_img, $author_name, $bg)
{
    return <<<HTML
<div class="new_chat__wrap chat__bg{$bg}">
    <div class="new_chat__menu">
         <div class="new_chat__menu--close" onclick="go('facecat.php?c');"></div>
         <div class="new_chat__menu--photo"></div>
         <form id="upload_photo" action="facecat.php?act=upload_image" enctype="multipart/form-data" method="post">
         <input type="file" style="position:absolute;width: 0px;height: 0px;opacity:0;" id="selector"
          accept=".jpg, .png, .jpeg, .gif, .bmp" name="photo_file"/>
          </form>
          <input id="photo_number" type="text" value="{$bg}" style="display:none"/>
    </div>
    <div class="loader" style="display: none"></div>
    <input class="new_chat__title" placeholder="Новый чат">
    <div class="new_chat__footer">
        <div class="new_chat__footer_left">
            <img src="{$author_img}" class="new_chat__author"/>
            <div class="new_chat__footer_name">
                <h3 class="new_chat__footer_name--h3">{$author_name}</h3>
                <div class="new_chat__footer_name--icon"></div>
            </div>
        </div>
        <div class="new_chat__footer_right">
            <div class="new_chat__button color_title-{$bg}" onclick="createNewChat();">Начать</div>
        </div>
    </div>
</div>
HTML;

}


function getColorName($id)
{
    return 'color-' . ($id % 6);
}

function getBgName($id)
{
    return 'bgreply-' . ($id % 6);
}


function sPreload()
{
    return <<<HTML
<!-- Скрытое поле с количеством загружаемых строк -->
<input type="hidden" value='32' id="loaded_max" />
<!-- -->
HTML;

}

function sFCChatsJS($type = '', $cl = 'fc__body', $cl2 = 'chat__link')
{
    return <<<JS
var loading = false;
$(window).scroll(function(){
if((($(window).scrollTop()+$(window).height())+900)>=$(document).height()){
if(loading == false){
loading = true;
$.get("facecat.php?act=a_load{$type}&from="+$('#loaded_max').val(), function(loaded){
$('.{$cl}').append(loaded);
controlSize();
$('#loaded_max').val(parseInt($('#loaded_max').val())+32);
loading = false;
});
}
}
});
$(document).ready(function() {
$('#loaded_max').val(32);
});
updateMyChats("{$type}","{$cl}","{$cl2}");
JS;

}
