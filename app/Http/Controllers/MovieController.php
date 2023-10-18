<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use Illuminate\Support\Facades\Log;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\Exception\InvalidEventRequestException;
use LINE\LINEBot\Exception\InvalidSignatureException;
use LINE\LINEBot\Event\MessageEvent\StickerMessage;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\StickerMessageBuilder;

class MovieController extends Controller{
    public function index()
    {
        return view("login");
    }

    //for push movies
    public function callMovie(Request $request)
    {
        $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(config('line.channel_access_token'));
        $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => config('line.channel_secret')]);

        //get request from database
        Log::info($request);
        $name = $request->get("name");
        $rate = $request->get("rate");
        $type = $request->get("type");
        $time_limit = $request->get("time_limit");

        $movie = new Movie();
        $movie->name = $name;
        $movie->rate = $rate;
        $movie->type = $type;
        $movie->time_limit = $time_limit;

        $user_id = "U0c1c68ab7fed6f53706d9f93ed10767c";
        $bot->pushMessage($user_id, new TextMessageBuilder("someone wanted a movie type ".$movie->type." with rate ".$movie->rate));
        $bot->pushMessage($user_id, new StickerMessageBuilder("446", "1992"));

        $movieAll =  Movie::all();
        return redirect()->back();

    }

    public function replyMessage (Request $request){
        $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(config('line.channel_access_token'));
        $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => config('line.channel_secret')]);

        $signature = $request->header(\LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE);
        if (empty($signature)) {
            abort(400);
        }

        Log::info($request->getContent());

        try {
            $events = $bot->parseEventRequest($request->getContent(), $signature);
        } catch (InvalidSignatureException $e) {
            Log::error('Invalid signature');
            abort(400, 'Invalid signature');
        } catch (InvalidEventRequestException $e) {
            Log::error('Invalid event request');
            abort(400, 'Invalid event request');
        }

        foreach ($events as $event) {
            $replyToken = $event->getReplyToken();
            if (!($event instanceof MessageEvent)) {
                Log::info('Non message event has come');
                continue;
            }
            if ($event instanceof StickerMessage) {
                $packageID = $event->getPackageId();
                $stickerID = $event->getStickerId();
                $multiMessageBuilder = new MultiMessageBuilder();
                $multiMessageBuilder->add(new TextMessageBuilder("packageID: ".$packageID));
                $multiMessageBuilder->add(new TextMessageBuilder("stickerID: ".$stickerID));
                $response = $bot->replyMessage($replyToken, $multiMessageBuilder);
                continue;
            }
            if (!($event instanceof TextMessage)) {
                Log::info('Non text message has come');
                continue;
            }
            $inputText = $event->getText();
            $replyText = '';
            if ($inputText === 'ดูคะแนน') {
                $replyText = 'คะแนนของคุณคือ 100 คะแนน';
            }
            else if ($inputText === 'give me 10 scores') {
                $movie = Movie::inRandomOrder()->get()->first();
                if ($movie != null) {
                    $multiMessageBuilder = new LINEBot\MessageBuilder\MultiMessageBuilder();
                    $multiMessageBuilder->add(new LINEBot\MessageBuilder\TextMessageBuilder("ID: " . $movie->id));
                    $multiMessageBuilder->add(new LINEBot\MessageBuilder\TextMessageBuilder("Name: " . $movie->name));
                    $multiMessageBuilder->add(new LINEBot\MessageBuilder\TextMessageBuilder("Rate: " . $movie->rate));
                    $multiMessageBuilder->add(new LINEBot\MessageBuilder\TextMessageBuilder("Type: " . $movie->type));
                    $multiMessageBuilder->add(new LINEBot\MessageBuilder\TextMessageBuilder("Length Time: " . $movie->length_time));
                    $response = $bot->replyMessage($replyToken, $multiMessageBuilder);
                }
            }

            else {
                Log::info('inputText: ' . $inputText);
            }

            $replyToken = $event->getReplyToken();
            $userId = $event->getUserId();
            $profile = $bot->getProfile($userId);
            $profile = $profile->getJSONDecodedBody();
            $displayName = $profile['displayName'];
            if (isset($profile['pictureUrl'])) {
                $pictureUrl = $profile['pictureUrl'];
            } else {
                $pictureUrl = '';
            }
            if (isset($profile['statusMessage'])) {
                $statusMessage = $profile['statusMessage'];
            } else {
                $statusMessage = '';
            }

            if ($replyText !== '') {
                $reponse = $bot->replyText($replyToken, $replyText);

                Log::info($response->getHTTPStatus().':'.$response->getRawBody());
            } else {
                $multiMessageBuilder = new MultiMessageBuilder();
                $multiMessageBuilder->add(new TextMessageBuilder($displayName));
                $multiMessageBuilder->add(new TextMessageBuilder($statusMessage));
                $multiMessageBuilder->add(new ImageMessageBuilder($pictureUrl, $pictureUrl));
                $response = $bot->replyMessage($replyToken, $multiMessageBuilder);
            }
        }
        return response()->json([]);
    }


}
