<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;


class LineMessageController extends Controller
{
    /**
     * LINEメッセージの受信
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function webhook(Request $request)
    {
        $signature = $request->header(HTTPHeader::LINE_SIGNATURE);
        if (empty($signature)) {
            return abort(400);
        }

        $httpClient = new CurlHTTPClient($_ENV['LINE_CHANNEL_ACCESS_TOKEN']);
        $bot = new LINEBot($httpClient, ['channelSecret' => $_ENV['LINE_CHANNEL_SECRET']]);
        $events = $bot->parseEventRequest($request->getContent(), $signature);

        collect($events)->each(function ($event) use ($bot) {
            $lineUserId = $event->getUserId();
            $user = User::where('line_user_id', $lineUserId);
            $memberId = uniqid();
            $name = $bot->getProfile($event->getUserId())->getJSONDecodedBody()['displayName'];
            if (!$user->exists()) {
                $user = User::create([
                    'member_id' => $memberId,
                    'line_user_id' => $lineUserId,
                    'name' => $name,
                    'email' => rand().'@mail.com',
                    'password' => rand()
                ]);
                $user->save();
                return $bot->replyText($event->getReplyToken(), "登録したよ！");
            } else {
                $message = 'https://3000-jiyuujin-templatevitere-ns6zih13f1d.ws-us54.gitpod.io/';
                return $bot->replyText($event->getReplyToken(), $message);
            }
            // if ($event instanceof TextMessage) {
            //     return $bot->replyText($event->getReplyToken(), $event->getText());
            // }
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
