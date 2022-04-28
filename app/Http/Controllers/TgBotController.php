<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Support\Facades\DB;
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

        if(isset($request['callback_query'])){

            $this->send_answerCallbackQuery('answerCallbackQuery', ['callback_query_id'=> $request['callback_query']['id'], 'text' => 'СООБЩЕНИЕ', 'show_alert' => true],$request['callback_query']['from']['id']);
        }


        if (isset($request['callback_query'])) {
            $message = json_decode(file_get_contents('php://input'), true);

            $this->chat_id = $request['callback_query']['from']['id'];
            if ($request['callback_query']['data'] == 'top') {

                $this->popularQuestion($this->chat_id);

            }else if ($request['callback_query']['data'] == 'immigration') {
                $this->changePath($this->chat_id,'search');
                $response = Telegram::sendMessage([
                    'chat_id' => $this->chat_id,
                    'text' => 'Напишіть, будь ласка, назву країни',
                ]);
                //$this->immigrationMenu($this->chat_id);
            }
            else if ($request['callback_query']['data'] == 'useful_contacts') {
                $this->contacts($this->chat_id);
            }
            else if ( str_starts_with($request['callback_query']['data'],'question_')) {
                Log::info($request['callback_query']['data']);
                $this->question($this->chat_id,str_replace('question_','',$request['callback_query']['data']));
            }
            else if ($request['callback_query']['data'] == 'empty') {
                $response = Telegram::sendMessage([
                    'chat_id' => $this->chat_id,
                    'text' => 'К сожалению данный пункт пока-что не работает',
                ]);
            }
            else if ($request['callback_query']['data'] == 'back') {
                $this->mainMenu($this->chat_id);
            }else{
                $this->mainMenu($this->chat_id);
            }
        } else {

             $users = DB::table('bot_users')->where('chat_id',412659845)->get();
            $this->chat_id = $request['message']['from']['id'];

            if(count($users)==0){
                DB::table('bot_users')->insert([
                    'chat_id' => $request['message']['from']['id'],
                    'name' => $request['message']['from']['first_name']." ".$request['message']['from']['last_name']
                ]);
                $this->gender($this->chat_id);

            }

            if($request['message']['text']=="/main_menu"){
                $this->mainMenu($this->chat_id);
            }else if($request['message']['text']=="/contacts"){
                $this->contacts($this->chat_id);
            }else if($users->first()->current_step=='search'){
                $this->immigrationMenu($this->chat_id,$request['message']['text']);
            }else{
                $this->mainMenu($this->chat_id);
            }
        }

        $response = Telegram::getMe();

        $botId = $response->getId();
        $firstName = $response->getFirstName();
        $username = $response->getUsername();


    }
    public function send_answerCallbackQuery($method, $data, $client){
        $data["parse_mode"] = "html";
        $ch = curl_init("https://api.telegram.org/5202270428:AAHN1iCNFiSQC8lSQsh5yhHYQL8vOheQtVU/$method");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_close($ch);
    }
    public function mainMenu($chat_id)
    {
        $this->changePath($chat_id,'Main menu');
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
    public function immigrationMenu($chat_id,$country_name)
    {
        $country = DB::table('bot_country')->where('name','LIKE',$country_name)->get()->first();
        if(is_null($country)){
            $response = Telegram::sendMessage([
                'chat_id' => $chat_id,
                'text' => 'Нажаль сталась помилка і нам не вдалося знайти цю країну. Спробуйте, будь ласка, іншу країну',
            ]);
        }else{
           // $this->changePath($chat_id,'Immigration menu');

            $questions = DB::table('bot_question')->where('country','=',$country->id)->get();
            $question_list=[];
            foreach ($questions as $question){
                $question_list[]=
                    [[ 'text' => $question->title, 'callback_data' => 'question_'.$question->id]];

            }
            $question_list[]=
                [['text' => 'Назад', 'callback_data' => 'back']];
            $inline_keyboard = json_encode([
                'inline_keyboard' => $question_list
            ]);
            $response = Telegram::sendMessage([
                'chat_id' => $chat_id,
                'text' => 'Правила в\'їзду до обраної країни',
                'reply_markup' => $inline_keyboard
            ]);
        }

    }
    public function question($chat_id, $question_id)
    {
        $question = DB::table('bot_question')->where('id','=',$question_id)->get()->first();
        $children_questions = DB::table('bot_question')->where('parent','=',$question_id)->get();
        $question_list=[];
        foreach ($children_questions as $child){
            $question_list[]=
                [[ 'text' => $child->title, 'callback_data' => 'question_'.$child->id]];

        }
        $question_list[]=
            [['text' => 'Назад', 'callback_data' => 'back']];
        $inline_keyboard = json_encode([
            'inline_keyboard' =>  $question_list
        ]);

        $response = Telegram::sendMessage([
            'chat_id' => $chat_id,
            'text' => $question->title.":\n".$question->answer,
            'reply_markup' => $inline_keyboard
        ]);

    }
    public function contacts($chat_id){
        $this->changePath($chat_id,'Contacts');
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
        $this->changePath($chat_id,'Popular questions');
        $questions = DB::table('bot_question')->where('country','=',NULL)->where('parent','=',NULL)->get();
        $question_list=[];
        foreach ($questions as $question){
            $question_list[]=
                [[ 'text' => $question->title, 'callback_data' => 'question_'.$question->id]];

        }
        $question_list[]=
            [['text' => 'Назад', 'callback_data' => 'back']];
        $inline_keyboard = json_encode([
            'inline_keyboard' =>
                $question_list

        ]);
        $response = Telegram::sendMessage([
            'chat_id' => $chat_id,
            'text' => 'Цікаві питання',
            'reply_markup' => $inline_keyboard
        ]);

    }
    public function gender($chat_id)
    {
        $this->changePath($chat_id,'Gender check');
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
        $update = Telegram::getWebhookUpdates();
        $message = $update->getMessage()->getText();
        if (str_contains($message, ['inspire', 'inspirational', 'inspiring'])) {
            $this->telegram->sendMessage()
                ->chatId($update->getMessage()->getChat()->getId())
                ->text('Hallo')
                ->getResult();
        }
        return 'Ok';
    }
    public function changePath($chat_id,$name){
        $user = DB::table('bot_users')->select('full_path')->where('chat_id',$chat_id)->get()->first();

                if(is_null($user->full_path)){
                    $path = [];
                }else{
                    $path= json_decode($user->full_path);
                }

                      $path[]=$name;
         DB::table('bot_users')
            ->where('chat_id', $chat_id)
            ->update([
                'full_path' => json_encode($path),
                'relative_path' => json_encode(array_unique($path)),
                'current_step' => $name
            ]);
    }
}

