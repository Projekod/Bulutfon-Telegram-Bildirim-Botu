<?php
require_once "vendor/autoload.php";
require_once "config.php";

use \Longman\TelegramBot\Request;
use \Longman\TelegramBot\Telegram;

$type = isset($_POST['event_type']) ? $_POST['event_type'] : false;
$caller = isset($_POST['caller']) ? $_POST['caller'] : false; //Arayan
$callee = isset($_POST['callee']) ? $_POST['callee'] : false; //Aranan

$messageInfo = str_replace('{arayanNumara}', $caller, $messageInfo);
$messageInfo = str_replace('{arananNumara}', $callee, $messageInfo);

try {
    $telegram = new Telegram($token, $botName);
    $chatId = getChatId(Request::getUpdates([]), $grupTitle);

    if(isset($messageType[$type]) && $chatId){
        $text = $messageInfo . " " . $messageType[$type];
        Request::sendMessage(['chat_id' => $chatId, 'text' => $text, 'parse_mode' => 'HTML']);
    }else {
        throw new Exception('Undefined offset or Undifined group');
    }

}catch (Longman\TelegramBot\Exception\TelegramException $e) {
    echo $e;
}

function getChatId($updates, $grupTitle){
    $updates = json_decode($updates);
    $results = $updates->result;

    foreach($results as $result){
        if(isset($result->message->chat->title) && $result->message->chat->title == $grupTitle){
            return $result->message->chat->id;
        }
    }

    return false;
}

