<?php
require_once "vendor/autoload.php";
require_once "xconfig.php";

use \Longman\TelegramBot\Request;
use \Longman\TelegramBot\Telegram;

$type = isset($_POST['event_type']) ? $_POST['event_type'] : false;
$caller = isset($_POST['caller']) ? $_POST['caller'] : false; //Arayan
$callee = isset($_POST['callee']) ? $_POST['callee'] : false; //Aranan

$messageInfo = str_replace('{arayanNumara}', $caller, $messageInfo);
$messageInfo = str_replace('{arananNumara}', $callee, $messageInfo);

try {
    $telegram = new Telegram($token, $botName);
    if(isset($messageType[$type])){
        $text = $messageInfo . " " . $messageType[$type];
        Request::sendMessage(['chat_id' => $chatId, 'text' => $text]);
        echo '<pre>';
        print_r(Request::getUpdates([]));
    }else {
        echo "ahmet";
        throw new Exception('Undefined offset');
    }
}catch (Longman\TelegramBot\Exception\TelegramException $e) {
    echo $e;
}



