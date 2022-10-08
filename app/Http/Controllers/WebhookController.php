<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use LINE\LINEBot\Exception\InvalidEventRequestException;
use LINE\LINEBot\Exception\InvalidSignatureException;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use Symfony\Component\Mailer\Event\MessageEvents;
use Illuminate\Support\Facades\Log;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\Event\MessageEvent\StickerMessage;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;


class WebhookController extends Controller
{
    public function index(Request $request){
        $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(config('line.channel_access_token'));
        $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => config('line.channel_secret')]);

        $signature = $request->header(\LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE);
        if(empty($signature)) {
            abort(400);
        }

        Log::info($request->getContent());

        try {
            $events = $bot->parseEventRequest($request->getContent(), $signature);
        } catch (InvalidSignatureException $e){
            Log::error('Invalid signature');
            abort(400,'Invalid Signature');
        } catch (InvalidEventRequestException $e){
            Log::error('Invalid event request');
            abort(400,'Invalid_event request');
        }

        foreach ($events as $event){
//            if(!($event instanceof TextMessage)){
//                Log::info('Non text message has come');
//            }



//            if(!($event instanceof TextMessage)){
//                Log::info('Non text message has come');
//            }
//             if($event instanceof TextMessage)


            $inputText = $event->getText();
            $replyText = '';


            if($event instanceof StickerMessage){
                $replyText = 'PackageId = ' . $event->getPackageId() . ', StickerId = '. $event->getStickerId();
            }

            if($inputText === 'give me 10 scores'){
                $replyText = json_encode(Item::inRandomOrder()->first());
            } else {
                Log::info('inputText: ' . $inputText);
            }
            $replyToken = $event->getReplyToken();
            $userId = $event->getUserId();
            $profile = $bot->getProfile($userId);
            $profile = $profile->getJSONDecodedBody();
            $displayName = $profile['displayName'];
            $pictureUrl = $profile['pictureUrl'];
            $statusMessage = $profile['statusMessage'];

            if ($replyText !== ''){
                $response = $bot->replyText($replyToken, $replyText);

                Log::info( $response->getHTTPStatus().':'.$response->getRawBody());

            } else {
                $multiMessageBuilder = new MultiMessageBuilder();
                $multiMessageBuilder->add(new TextMessageBuilder($displayName));
                $multiMessageBuilder->add(new TextMessageBuilder($statusMessage));
                $multiMessageBuilder->add(new ImageMessageBuilder($pictureUrl, $pictureUrl));
                $response = $bot->replyMessage($replyToken,$multiMessageBuilder);

            }
        }

        return response()->json([]);
    }

    public function liff(){
        $user = Auth::user();

        return view('line.welcome',['user' => $user]);
    }
}
