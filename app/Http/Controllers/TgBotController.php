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
    public $search_country;
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
            }
            else if ($request['callback_query']['data'] == 'chat') {
                $this->changePath($this->chat_id,'chat');
                $response = Telegram::sendMessage([
                    'chat_id' => $this->chat_id,
                    'text' => 'Напишіть, будь ласка, своє запитання, і ми віповімо вам як тільки буде можливість',
                ]);
            }
            else if ($request['callback_query']['data'] == 'useful_contacts') {
                $this->contacts($this->chat_id);
            }
            else if ( str_starts_with($request['callback_query']['data'],'question_')) {

                $this->question($this->chat_id,str_replace('question_','',$request['callback_query']['data']));
            }
            else if ( str_starts_with($request['callback_query']['data'],'country_question_')) {

                $this->countryQuestion($this->chat_id,str_replace('country_','',$request['callback_query']['data']));
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
            $this->chat_id = $request['message']['from']['id'];

                 $users = DB::table('bot_users')->where('chat_id',$this->chat_id)->get();

            if(count($users)==0){
                DB::table('bot_users')->insert([
                    'chat_id' => $request['message']['from']['id'],
                    'name' => $request['message']['from']['first_name']
                ]);
                $this->gender($this->chat_id);

            }
            else if($request['message']['text']=="/start") {
                DB::table('bot_users')->where('chat_id',$this->chat_id)->delete();
                $this->gender($this->chat_id);
            }
            else if($request['message']['text']=="/main_menu"){
                $this->mainMenu($this->chat_id);
            }else if($request['message']['text']=="/contacts"){
                $this->contacts($this->chat_id);
            }else if($users->first()->current_step=='search'){
                $this->immigrationMenu($this->chat_id,$request['message']['text']);
            }else if($users->first()->current_step=='chat'){
                $this->chat($this->chat_id,$request['message']['text']);
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
                    ['text' => 'Розпочати чат', 'callback_data' => 'chat'],
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
            DB::table('bot_users')
                ->where('chat_id', $chat_id)
                ->update([
                    'current_country' => $country->id,
                ]);
           // $this->changePath($chat_id,'Immigration menu');

            $question_list=[
                [[ 'text' => 'Правила в’їзду до країни', 'callback_data' => 'country_question_0']],
                [[ 'text' => 'Міжнародний захист', 'callback_data' => 'country_question_1']],
                [[ 'text' => 'Працевлаштування ', 'callback_data' => 'country_question_2']],
                [[ 'text' => 'Допомога ', 'callback_data' => 'country_question_3']],
                [[ 'text' => 'Інтеграція ', 'callback_data' => 'country_question_4']],
                [[ 'text' => 'Корисні контакти  ', 'callback_data' => 'country_question_5']],
                [[ 'text' => 'Назад', 'callback_data' => 'back']]
            ];
            $inline_keyboard = json_encode([
                'inline_keyboard' => $question_list
            ]);
            $response = Telegram::sendMessage([
                'chat_id' => $chat_id,
                'text' => 'Доступна інформація про обрану країну',
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
            'reply_markup' => $inline_keyboard,
            'parse_mode' => 'html'
        ]);

    }
    public function countryQuestion($chat_id, $question_id)
    {
        $user = DB::table('bot_users')->where('chat_id',$this->chat_id)->get()->first();
        $question = DB::table('bot_country')->where('id','=',$user->current_country)->get()->first();
        $children_questions = DB::table('bot_question')->where('country','=',$user->current_country)->where('parent','=',$question_id)->get();
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
        $title = [
            'question_0'=> "Правила в’їзду до країни",
            'question_1'=>"Міжнародний захист",
            'question_2'=>"Працевлаштування",
            'question_3'=>"Допомога",
            'question_4'=>"Інтеграція",
            'question_5'=>"Корисні контакти",
        ];
        $response = Telegram::sendMessage([
            'chat_id' => $chat_id,
            'text' => $title[$question_id].":\n".$question->$question_id,
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
    public function chat($chat_id,$message){
        $user = DB::table('bot_users')->where('chat_id',$this->chat_id)->get()->first();
        $chat = DB::table('chats')->where('user_id',$user->id)->get();
        if(count($chat)==0){
            $history=[];
            $history[] = [
                'direction'=>'from',
                'message'=>$message
            ];
            DB::table('chats')->insert([
                'user_id' => $user->id,
                'history' => json_encode($history),
                'lastMessage' => $message

            ]);
        }else{
            $history = json_decode($chat->first()->history,1);
            $history[] = [
                'direction'=>'from',
                'message'=>$message
            ];
            DB::table('chats')
                ->where('user_id',$user->id)
                ->update([
                    'history' => json_encode($history),
                    'lastMessage' => $message
                ]);
        }
    }
    public function getChats(){
        $chats = DB::table('chats')->join('bot_users', 'bot_users.id', '=', 'chats.user_id')->select('*')->get()->all();
        return $chats;
    }
    public function getMessages($id){
        $chats = DB::table('chats')->join('bot_users', 'bot_users.id', '=', 'chats.user_id')->select('*')->where('chats.user_id',$id)->get()->all();
        return $chats;
    }
    public function sendMessage($id,Request $request){
        if($request['message']==""){
            return 0;
        }
        $chat = DB::table('chats')->where('user_id',$id)->get();
        $history = json_decode($chat->first()->history,1);
        $history[] = [
            'direction'=>'to',
            'message'=>$request['message']
        ];
        DB::table('chats')
            ->where('user_id',$id)
            ->update([
                'history' => json_encode($history),
            ]);
        $user = DB::table('bot_users')->where('id',$id)->get()->first();
        $response = Telegram::sendMessage([
            'chat_id' => $user->chat_id,
            'text' => $request['message'],
        ]);
    }
}

