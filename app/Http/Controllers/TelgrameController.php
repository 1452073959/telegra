<?php

namespace App\Http\Controllers;

use App\Jobs\RemoveGrounp;
use App\Models\TelegramHistory;
use App\Models\TelegramUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelgrameController extends Controller
{
    //
    public function index(Request $request)
    {

        $message= $request->all();
        Log::info($message);
        //判断新用户加入
        if (isset($message['message']['new_chat_member'])) {
            $msg=$message['message']['new_chat_member'];
            // 当前用户是新加入的用户，进行相应的处理
            $username = '@'.$msg['first_name']; // 要求回复的用户的用户名
            $chatId = '-1001987792603'; // 群组的 chat_id
            $rand=rand(1000,9999);
            Cache::put($msg['id'],$rand,60);
            $message=    Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => '请在60秒内输入以下内容,'.$rand. $username . ':',
            ]);
            $user=new TelegramUser();
            $user->user_no=$msg['id'];
            $user->user_name=$msg['first_name'];
            $user->chat_ground_id=$chatId;
            $user->add_time=date('Y-m-d H:i:s',time());
            $user->user_status=3;
            $user->save();
            RemoveGrounp::dispatch($user)
                ->delay(now()->addMinutes(1))
            ;

        }
        //判断新消息
        if(isset($message['message']['from'])&&isset($message['text'])){
            $form=$message['message']['from'];
            $value = Cache::get($form['id']);//获取发言者的内容
            $history=new TelegramHistory();
            $history->chat_ground_id=$message['chat']['id'];
            $history->user_no=$form['id'];
            $history->user_name=$form['first_name'];
            $history->send_time=date('Y-m-d H:i:s',time());
            $history->send_text=$message['text'];
            $history->save();
            if($message['text']==$value){
                $res=TelegramUser::where('user_no',$form['id'])->where('user_status',3)->find();
                if($res){
                    $user->user_status=2;
                    $user->save();
                }
            }

        }


        return 'ok';

    }



}
