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

        /** @var BotMan $bot */
        $bot = resolve('bot');
        $bot->listen();

        $ref = null;
        $optin = false;
        $payload= json_decode($request->getContent(), true);
        if (isset($payload['entry'])) {
            $data = $payload['entry'][0]['messaging'][0];

            if (key_exists('referral', $data) && key_exists('ref', $data['referral'])) {
                $ref = $data['referral']['ref'];
            } elseif (key_exists('postback', $data) && key_exists('referral', $data['postback'])) {
                $ref = $data['postback']['referral'];
            } elseif (key_exists('optin', $data) && key_exists('ref', $data['optin'])) {
                $optin = true;
                $ref = $data['optin']['ref'];
            }
        }
        if (!$ref) {
            return;
        }

        $bot->userStorage()->save(['ref' => $ref]);
        if ($optin) {
            $bot->reply('Hello, here is your voucher code: '.$this->getCoupon($ref));
            $question = Question::create('Want to do another test? '.$this->getLink($ref))
                ->addButton(Button::create('Yes')->value('Yes'))
                ->addButton(Button::create('No')->value('No'));
        } else {
            $question = Question::create('Hello, here is your voucher code: '.$this->getCoupon($ref))
                ->addButton(Button::create('Yes')->value('Yes'))
                ->addButton(Button::create('No')->value('No'));
        }

        $conversation = new CouponConversation();
        $conversation->question = $question;
        $conversation->url = $this->getLink($ref);
        $bot->startConversation($conversation);
    }

    protected function getCoupon($ref) {
        return '[COUPON]';
    }

    protected function getLink($ref) {
        $ref = str_replace('_', '&', $ref);
        return 'https://www.techtrendr.com/?'.$ref;
    }
}