<?php

/*
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Written by Marco Boretto <marco.bore@gmail.com>
*/

namespace Longman\TelegramBot\Commands;

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Command;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Entities\File;

class WhoamiCommand extends Command
{
    protected $name = 'whoami';
    protected $description = 'Show your id, name and username';
    protected $usage = '/whoami';
    protected $version = '1.0.0';
    protected $enabled = true;
    protected $public = true;

    public function execute()
    {
        $update = $this->getUpdate();
        $message = $this->getMessage();

        $user_id = $message->getFrom()->getId();
        $chat_id = $message->getChat()->getId();
        $message_id = $message->getMessageId();
        $text = $message->getText(true);

        //send chat action
        Request::sendChatAction(['chat_id' => $chat_id, 'action' => 'typing']);

        $caption = 'Your Id: ' . $message->getFrom()->getId();
        $caption .= "\n" . 'Name: ' . $message->getFrom()->getFirstName()
             . ' ' . $message->getFrom()->getLastName();
        $caption .= "\n" . 'Username: ' . $message->getFrom()->getUsername();

        //Fetch user profile photo
        $limit = 10;
        $offset = null;
        $ServerResponse = Request::getUserProfilePhotos([
            'user_id' => $user_id ,
            'limit' => $limit,
            'offset' => $offset
        ]);

        //Check if the request isOK
        if ($ServerResponse->isOk()) {
            $UserProfilePhoto = $ServerResponse->getResult();
            $totalcount = $UserProfilePhoto->getTotalCount();
        } else {
            $totalcount = 0;
        }

        $data = [];
        $data['chat_id'] = $chat_id;
        $data['reply_to_message_id'] = $message_id;

        if ($totalcount > 0) {
            $photos = $UserProfilePhoto->getPhotos();
            //I pick the latest photo with the hight definition
            $photo = $photos[0][2];
            $file_id = $photo->getFileId();


            $data['photo'] = $file_id;
            $data['caption'] = $caption;

            $result = Request::sendPhoto($data);

            //Download the image pictures
            //Download after send message response to speedup response
            $file_id = $photo->getFileId();
            $ServerResponse = Request::getFile(['file_id' => $file_id]);
            if ($ServerResponse->isOk()) {
                Request::downloadFile($ServerResponse->getResult());
            }

        } else {
            //No Photo just send text
            $data['text'] = $caption;
            $result = Request::sendMessage($data);
        }
        return $result->isOk();
    }
}
