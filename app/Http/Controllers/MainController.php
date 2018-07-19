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
        $ref = null;
        if (isset($payload['entry'])) {
            $data = $payload['entry'][0]['messaging'][0];

            if (key_exists('referral', $data) && key_exists('ref', $data['referral'])) {
                $ref = $data['referral']['ref'];
            } elseif (key_exists('postback', $data) && key_exists('referral', $data['postback'])) {
                $ref = $data['postback']['referral'];
            } elseif (key_exists('optin', $data) && key_exists('ref', $data['optin'])) {
                $ref = $data['optin']['ref'];
            }
        }

        if (!$ref) {
            return;
        }

        /** @var BotMan $bot */
        $bot = resolve('bot');
        $bot->listen();
        $bot->userStorage()->save(['ref' => $ref]);

        $get_started = false;
        if ($get_started) {
            $bot->reply('Hello, here is your voucher code: '.$this->getCoupon($ref));
            $question = Question::create('Want to do another test? '.$this->getLink($ref))
                ->addButton(new Button('Yes'))
                ->addButton(new Button('No'));
        } else {
            $question = Question::create('Hello, here is your voucher code: '.$this->getCoupon($ref))
                ->addButton(new Button('Yes'))
                ->addButton(new Button('No'));
        }

        $conversation = new CouponConversation();
        $conversation->question = $question;
        $conversation->url = $this->getLink($ref);
        $bot->startConversation($conversation);
    }

    protected function getCoupon() {
        return '[COUPON]';
    }

    protected function getLink($ref) {
        return 'https://www.techtrendr.com/?'.$ref;
    }
}