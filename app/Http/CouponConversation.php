<?php

namespace App\Http;


use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;

class CouponConversation extends Conversation {

    public $url;

    public $question;

    public function run() {
        $this->bot->ask($this->question, function(Answer $answer) {
            $text = $answer->getText();
            if ($text == 'Yes') {
                $this->say('Great, ' . $this->url);
            } elseif ($text == 'No') {
                $this->say('Ok.');
            }
        });
    }

}