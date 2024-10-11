<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;

class BotManController extends Controller
{
    protected $questions = [
        'What services do you offer?' => 'We offer a wide range of services including web development, mobile app development, and digital marketing.',
        'How can I contact support?' => 'You can contact our support team via email at support@example.com or call us at 1-800-123-4567.',
        'What are your business hours?' => 'Our business hours are Monday to Friday, 9 AM to 5 PM EST.',
        'Do you offer custom solutions?' => 'Yes, we provide custom solutions tailored to your specific needs. Please contact our sales team for more information.',
        'Chat with a live person' => 'live_chat'
    ];

    public function handle()
    {
        $botman = app('botman');

        $botman->hears('start', function (BotMan $bot) {
            $this->showQuestions($bot);
        });

        foreach ($this->questions as $question => $answer) {
            $botman->hears($question, function (BotMan $bot) use ($question, $answer) {
                if ($answer === 'live_chat') {
                    $this->activateLiveChat($bot);
                } else {
                    $bot->reply($answer);
                    $this->showQuestions($bot);
                }
            });
        }

        $botman->fallback(function (BotMan $bot) {
            $bot->reply("I'm sorry, I didn't understand that. Here are some questions you can ask:");
            $this->showQuestions($bot);
        });

        $botman->listen();
    }

    protected function showQuestions(BotMan $bot)
    {
        $question = Question::create('What would you like to know?')
            ->fallback('Unable to show questions')
            ->callbackId('ask_questions');

        foreach ($this->questions as $text => $answer) {
            $question->addButton(Button::create($text)->value($text));
        }

        $bot->ask($question, function ($answer, $bot) {
            if ($answer->isInteractiveMessageReply()) {
                $selectedQuestion = $answer->getText();
                if ($this->questions[$selectedQuestion] === 'live_chat') {
                    $this->activateLiveChat($bot);
                } else {
                    $bot->reply($this->questions[$selectedQuestion]);
                    $this->showQuestions($bot);
                }
            }
        });
    }

    protected function activateLiveChat(BotMan $bot)
    {
        $bot->reply("Certainly! I'm connecting you with a live person now. Please wait a moment.");
        
        // Send a message to activate Drift chat on the frontend
        $bot->reply("activate_drift_chat");
        
        // Optional: Add a delay before showing the final message
        $bot->typesAndWaits(2);
        $bot->reply("A live chat window should appear shortly. If it doesn't, please refresh the page and try again.");
    }
}
