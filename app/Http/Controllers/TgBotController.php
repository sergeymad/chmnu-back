<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Routing\Controller as BaseController;
use Log;

class TgBotController extends BaseController
{

    private $chat_id = 0;
    public $config = [
        'api_key' => "5202270428:AAHN1iCNFiSQC8lSQsh5yhHYQL8vOheQtVU",
        'bot_username' => "UkraineSaveBot"];

    public function handler(Request $request)
    {

        if (isset($request['callback_query'])) {
            $message = json_decode(file_get_contents('php://input'), true);
            Log:info($message);
            $this->chat_id = $request['callback_query']['message']['from']['id'];
            if ($request['callback_query']['data'] == 'top') {

                $this->popularQuestion(412659845);

            }else if ($request['callback_query']['data'] == 'immigration') {
                $this->immigrationMenu(412659845);
            }
            else if ($request['callback_query']['data'] == 'useful_contacts') {
                $this->contacts(412659845);
            }
            else if ($request['callback_query']['data'] == 'question') {
                $this->question(412659845);
            }
            else if ($request['callback_query']['data'] == 'empty') {
                $response = Telegram::sendMessage([
                    'chat_id' => 412659845,
                    'text' => 'К сожалению данный пункт пока-что не работает',
                ]);
            }
            else if ($request['callback_query']['data'] == 'back') {
                $this->mainMenu(412659845);
            }else{
                $this->mainMenu(412659845);
            }
        } else {
            if ($this->chat_id == 0) {

                $this->chat_id = $request['message']['from']['id'];
                $this->gender(412659845);
            }
        }

        $response = Telegram::getMe();

        $botId = $response->getId();
        $firstName = $response->getFirstName();
        $username = $response->getUsername();


    }
    public static function send_answerCallbackQuery($token, $callback_query_id, $text, $show_alert){
        file_get_contents("https://api.telegram.org/bot".$token."/answerCallbackQuery?callback_query_id=".$callback_query_id."&text=".$text."&show_alert=".$show_alert);
    }
    public function mainMenu($chat_id)
    {
        $inline_keyboard = json_encode([
            'inline_keyboard' => [
                [
                    [ 'text' => 'Найпопулярніші питання', 'callback_data' => 'top'],
                ],
                [
                    ['text' => 'Правила в\'їзду до країн', 'callback_data' => 'immigration'],
                ],
                [
                    ['text' => 'Корисні контакти в Україні', 'callback_data' => 'useful_contacts'],
                ],
                [
                    ['text' => 'Виклик 527', 'callback_data' => 'empty'],
                ],
                [
                    ['text' => 'Розпочати чат', 'callback_data' => 'empty'],
                ],
            ]
        ]);
        $response = Telegram::sendMessage([
            'chat_id' => $chat_id,
            'text' => 'Головне меню',
            'reply_markup' => $inline_keyboard
        ]);
    }
    public function immigrationMenu($chat_id)
    {
        $inline_keyboard = json_encode([
            'inline_keyboard' => [

                [
                    ['text' => 'Правила в\'їзду до країни', 'callback_data' => 'question'],
                ],
                [
                    [ 'text' => 'Допомога', 'callback_data' => 'question'],

                ],
                [
                    ['text' => 'Міжнародний захист', 'callback_data' => 'question'],
                ],
                [
                    ['text' => 'Інтеграція', 'callback_data' => 'question'],
                ],
                [
                    ['text' => 'Працевлаштування', 'callback_data' => 'question'],
                ],
                [
                    ['text' => 'Корисні контакти', 'callback_data' => 'question'],
                ],
                [
                    ['text' => 'Назад', 'callback_data' => 'back'],
                ],
            ]
        ]);
        $response = Telegram::sendMessage([
            'chat_id' => $chat_id,
            'text' => 'Правила в\'їзду до країн',
            'reply_markup' => $inline_keyboard
        ]);
    }
    public function question($chat_id)
    {
        $inline_keyboard = json_encode([
            'inline_keyboard' => [
                [
                        ['text' => 'Назад', 'callback_data' => 'back'],
                ],
            ]
        ]);
        $response = Telegram::sendMessage([
            'chat_id' => $chat_id,
            'text' => 'Відповідь на запитання \n Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla odio elit, molestie vel mauris at, blandit imperdiet magna. Cras ac augue purus. Vestibulum eleifend sem id enim pretium, sit amet laoreet lorem laoreet. Phasellus vitae dignissim purus, vulputate suscipit risus. Nam turpis nibh, tincidunt sed pulvinar vel, elementum nec mauris. Ut in commodo tellus, eu porta dolor. Sed ut metus augue. Nulla vestibulum leo nec arcu euismod sollicitudin. Ut porttitor in metus eget tempus. Nunc porttitor vel diam vel mattis. Cras consequat id erat eleifend luctus. Suspendisse potenti.',
            'reply_markup' => $inline_keyboard
        ]);

    }
    public function contacts($chat_id){
        $inline_keyboard = json_encode([
            'inline_keyboard' => [
                [
                    ['text' => 'Назад', 'callback_data' => 'back'],
                ],
            ]
        ]);
        $response = Telegram::sendMessage([
            'chat_id' => $chat_id,
            'text' => 'Корисні контакти \n Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla odio elit, molestie vel mauris at, blandit imperdiet magna. Cras ac augue purus. Vestibulum eleifend sem id enim pretium, sit amet laoreet lorem laoreet. Phasellus vitae dignissim purus, vulputate suscipit risus. Nam turpis nibh, tincidunt sed pulvinar vel, elementum nec mauris. Ut in commodo tellus, eu porta dolor. Sed ut metus augue. Nulla vestibulum leo nec arcu euismod sollicitudin. Ut porttitor in metus eget tempus. Nunc porttitor vel diam vel mattis. Cras consequat id erat eleifend luctus. Suspendisse potenti.',
            'reply_markup' => $inline_keyboard
        ]);
    }
    public function popularQuestion($chat_id)
    {
        $inline_keyboard = json_encode([
            'inline_keyboard' => [
                [
                    [
                        ['text' => 'Як мені ...?', 'callback_data' => 'question'],
                    ],
                    [
                        ['text' => 'Як мені ...?', 'callback_data' => 'question'],
                    ],
                    [
                        ['text' => 'Як мені ...?', 'callback_data' => 'question'],
                    ],
                    [
                        ['text' => 'Назад', 'callback_data' => 'back'],
                    ],
                ],
            ]
        ]);
        $response = Telegram::sendMessage([
            'chat_id' => $chat_id,
            'text' => 'Популярні запитання:',
            'reply_markup' => $inline_keyboard
        ]);

    }

    public function gender($chat_id)
    {
        $inline_keyboard = json_encode([
            'inline_keyboard' => [
                [
                    [ 'text' => 'Пан', 'callback_data' => 'male'],
                    [ 'text' => 'Пані', 'callback_data' => 'female'],

                ],

            ]
        ]);
        $response = Telegram::sendMessage([
            'chat_id' => $chat_id,
            'text' => 'Як до вас звертатись?',
            'reply_markup' => $inline_keyboard
        ]);

        $messageId = $response->getMessageId();
    }

    public function webhookHandler()
    {
        // If you're not using commands system, then you can enable this.
        $update = Telegram::getWebhookUpdates();
        // This fetchs webhook update + processes the update through the commands system.
        #$update = $this->telegram->commandsHandler(true);
        // Commands handler method returns an Update object.
        // So you can further process $update object
        // to however you want.
        // Below is an example
        $message = $update->getMessage()->getText();
        // Triggers when your bot receives text messages like:
        // - Can you inspire me?
        // - Do you have an inspiring quote?
        // - Tell me an inspirational quote
        // - inspire me
        // - Hey bot, tell me an inspiring quote please?
        if (str_contains($message, ['inspire', 'inspirational', 'inspiring'])) {
            $this->telegram->sendMessage()
                ->chatId($update->getMessage()->getChat()->getId())
                ->text('Hallo')
                ->getResult();
        }
        return 'Ok';
    }
}

