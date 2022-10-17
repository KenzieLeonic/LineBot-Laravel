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

class MovieController extends Controller{
    public function index()
    {
        return Movie::all();
    }

//    public function replyMessage(Request $request){
//        $httpClient = new CurlHTTPClient('linebot.channel_access_token');
//        $bot = new LINEBot($httpClient, ['channelSecret' => 'linebot.channel_secret']);
//        $signature = $request->header(LINEBot\Constant\HTTPHeader::LINE_SIGNATURE);
//
//        if (empty($signature)) {
//            abort(400);
//        }
//
//        try {
//            $events = $bot->parseEventRequest($request->getContent(), $signature);
//        } catch (LINEBot\Exception\InvalidSignatureException $e) {
//            abort(400);
//        } catch (LINEBot\Exception\InvalidEventRequestException $e) {
//            abort(400);
//        }
//
//        foreach ($events as $event) {
//            $replyToken = $event->getReplyToken();
//            if (!($event instanceof LINEBot\Event\MessageEvent)) {
//                $response = $bot->replyText($replyToken, "Not a message");
//                continue;
//            }
//
//            if (!($event instanceof LINEBot\Event\MessageEvent\TextMessage) and !($event instanceof LINEBot\Event\MessageEvent\StickerMessage)) {
//                $response = $bot->replyText($replyToken, "Not a text or a sticker");
//                continue;
//            }
//
//            $replyText = "";
//            $movie = null;
//
//            //Get User ID
//            $user_id = $event->getUserId();
//            $profile = $bot->getProfile($user_id);
//            $profile = $profile->getJSONDecodedBody();
//            $displayName = $profile['displayName'];
//            $pictureUrl = $profile['pictureUrl'];
//            $statusMessage = $profile['statusMessage'];
//
//            //give 10 scores
//            if ($event instanceof LINEBot\Event\MessageEvent\TextMessage) {
//                $inputMessage = $event->getText();
//                $textResponses = [
//                    "give me 10 scores",
//                    "greeting",
//                    "hi"
//                ];
//
//                if ($inputMessage === $textResponses[0]) {
//                    $movie = Movie::inRandomOrder()->get()->first();
//
//                    if ($movie != null) {
//                        $multiMessageBuilder = new LINEBot\MessageBuilder\MultiMessageBuilder();
//                        $multiMessageBuilder->add(new LINEBot\MessageBuilder\TextMessageBuilder("ID: " . $movie->id));
//                        $multiMessageBuilder->add(new LINEBot\MessageBuilder\TextMessageBuilder("Name: " . $movie->name));
//                        $multiMessageBuilder->add(new LINEBot\MessageBuilder\TextMessageBuilder("Rate: " . $movie->rate));
//                        $multiMessageBuilder->add(new LINEBot\MessageBuilder\TextMessageBuilder("Type: " . $movie->type));
//                        $multiMessageBuilder->add(new LINEBot\MessageBuilder\TextMessageBuilder("Length Time: " . $movie->length_time));
//                        $response = $bot->replyMessage($replyToken, $multiMessageBuilder);
//                    }
//                }  else if ($inputMessage === $textResponses[1]) {
//                        $response = $bot->replyText($replyToken, "greeting");
//                }  else if ($inputMessage === $textResponses[2]) {
//                        $response = $bot->replyText($replyToken, "hi");
//                }  else {
//                    $inputTextSplit = explode(",", $inputMessage);
//                    if ($inputTextSplit[0] === "add") {
//                        $newMovie = new Movie();
//                        $newMovie->name = $inputTextSplit[1].trim();
//                        $newMovie->rate = $inputTextSplit[2].trim();
//                        $newMovie->type = $inputTextSplit[3].trim();
//                        $newMovie->length_time = $inputTextSplit[4].trim();
//                        $newMovie->save();
//                        $response = $bot->replyText($replyToken, "added".$newMovie->name);
//                        Log::info($newMovie);
//                        } else {
//                            $response = $bot->replyText($replyToken, "input text is not a valid response");
//                        }
//                    }
//                }
//            if ($event instanceof LINEBot\Event\MessageEvent\StickerMessage) {
//                $packageID = $event->getPackageId();
//                $stickerID = $event->getStickerId();
//
//                $multiMessageBuilder = new LINEBot\MessageBuilder\MultiMessageBuilder();
//                $multiMessageBuilder->add(new LINEBot\MessageBuilder\TextMessageBuilder("Package ID: ".$packageID));
//                $multiMessageBuilder->add(new LINEBot\MessageBuilder\TextMessageBuilder("Sticker ID: ".$stickerID));
//                $response = $bot->replyMessage($replyToken, $multiMessageBuilder);
//                continue;
//            }
//            return [];
//            }
//    }

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
