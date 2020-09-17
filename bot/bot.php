<?php

function bot_sendMessage($user_id) {
  $users_get_response = vkApi_usersGet($user_id);
  $user = array_pop($users_get_response);
  $dbconn = pg_connect("host=ec2-174-129-195-73.compute-1.amazonaws.com dbname=de3o71t0kn82m user=blqkouolnwjrgc password=a0dc61470ac0bfd600c4cd2bc2a6529c539cf2dc7fb2902ed11aa647e8276051")
    or die('Could not connect: ' . pg_last_error());
    $query = pg_query($dbconn , "select count(*) from anec");
    $row = pg_fetch_row($query);
    $row = html_entity_decode(strip_tags($row[0], ''));// html_entity_decode(htmlentities($row, ENT_QUOTES, 'UTF-8'), ENT_QUOTES , 'ISO-8859-15');
    echo $row;

    $maxquery = pg_query($dbconn , "select count(*) from anec");
    $row = pg_fetch_row($maxquery);
    $maxid = $row[0];
    $id = rand(1,$maxid);
//    $query = mssql_query("select CAST(anec as Text) as anec from t.anecdots where id = " . $id, $link);
//    $row = mssql_fetch_row($query);
    $query = pg_query($dbconn , "select CAST(text as Text) as anec from anec where id = " . $id);
    $row = pg_fetch_row($query);
//    $row = $row[0];
    $row = html_entity_decode(strip_tags($row[0], ''));// html_entity_decode(htmlentities($row, ENT_QUOTES, 'UTF-8'), ENT_QUOTES , 'ISO-8859-15');
//    $row =  iconv("", "UTF-8", $row[0]);
//    echo $row;
    $msg = $row;
//  $msg = "Привет, {$user['first_name']}!";

//  $photo = _bot_uploadPhoto($user_id, BOT_IMAGES_DIRECTORY.'/cat.jpeg');

  $voice_message_file_name = yandexApi_getVoice($msg);
  $doc = _bot_uploadVoiceMessage($user_id, $voice_message_file_name);

  $attachments = array(
//    'photo'.$photo['owner_id'].'_'.$photo['id'],
    'doc'.$doc['owner_id'].'_'.$doc['id'],
  );

  vkApi_messagesSend($user_id, $msg, $attachments);
//  vkApi_messagesSend($user_id, $msg);
}

function _bot_uploadPhoto($user_id, $file_name) {
  $upload_server_response = vkApi_photosGetMessagesUploadServer($user_id);
  $upload_response = vkApi_upload($upload_server_response['upload_url'], $file_name);

  $photo = $upload_response['photo'];
  $server = $upload_response['server'];
  $hash = $upload_response['hash'];

  $save_response = vkApi_photosSaveMessagesPhoto($photo, $server, $hash);
  $photo = array_pop($save_response);

  return $photo;
}

function _bot_uploadVoiceMessage($user_id, $file_name) {
  $upload_server_response = vkApi_docsGetMessagesUploadServer($user_id, 'audio_message');
  $upload_response = vkApi_upload($upload_server_response['upload_url'], $file_name);

  $file = $upload_response['file'];

  $save_response = vkApi_docsSave($file, 'Voice message');
  $doc = array_pop($save_response);

  return $doc;
}
