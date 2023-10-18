<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
class message extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'line:put-message {user_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "called description";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $httpClient = new CurlHTTPClient(config("line.channel_access_token"));
        $bot = new LINEBot($httpClient, ['channelSecret' => config("line.channel_secret")]);

        $user_id = "U0c1c68ab7fed6f53706d9f93ed10767c";
        $message = $this->argument('user_id');
        $bot->pushMessage($user_id, new TextMessageBuilder($message));
        $bot->pushMessage($user_id, new StickerMessageBuilder("446", "1991"));
        return 0;
    }
}
