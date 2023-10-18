<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

class MovieApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
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
