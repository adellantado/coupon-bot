<?php

namespace App\Http\Controllers;


use App\Http\CouponConversation;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Http\Request;

class MainController extends Controller
{

    public function listen(Request $request) {
        $payload= json_decode($request->getContent(), true);
        if (isset($payload['entry'])) {
            $data = $payload['entry'][0]['messaging'][0];
        }

        /** @var BotMan $bot */
        $bot = resolve('bot');
        $bot->listen();
        $bot->userStorage()->save(['ref' => 'test']);

        $get_started = false;
        if ($get_started) {
            $bot->reply('Hello, here is your voucher code: '.$this->getCoupon());
            $question = Question::create('Want to do another test?')
                ->addButton(new Button('Yes'))
                ->addButton(new Button('No'));
        } else {
            $question = Question::create('Hello, here is your voucher code: '.$this->getCoupon())
                ->addButton(new Button('Yes'))
                ->addButton(new Button('No'));
        }

        $conversation = new CouponConversation();
        $conversation->question = $question;
        $conversation->url = $this->getLink();
        $bot->startConversation($conversation);
    }

    protected function getCoupon() {
        return '[COUPON]';
    }

    protected function getLink() {
        return 'http://google.com/';
    }
}