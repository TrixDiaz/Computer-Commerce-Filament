<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Incoming\Answer;

class BotManController extends Controller
{
    public function handle()
    {
        $botman = app('botman');

        $botman->hears('{message}', function($botman, $message) {
          
            if (strtolower($message) == 'hi') {
                $this->askName($botman);
            } else {
                $botman->reply('Write hi for greeting me');
            }
        
        });

        $botman->listen();
    }

    public function askName(BotMan $botman)
    {
        $botman->ask('Hello! What is your name?', function(Answer $answer) {
            $name = $answer->getText();

            $this->say('Nice to meet you ' . $name);
        });
    }
}
