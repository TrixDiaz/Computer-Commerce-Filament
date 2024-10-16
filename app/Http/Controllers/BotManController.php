<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Http\Request;

class BotManController extends Controller
{
    protected $questions = [
        'What services do you offer?' => 'We offer a wide range of services including web development, mobile app development, and digital marketing.',
        'How can I contact support?' => 'You can contact our support team via email at support@example.com or call us at 1-800-123-4567.',
        'What are your business hours?' => 'Our business hours are Monday to Friday, 9 AM to 5 PM EST.',
        'Do you offer custom solutions?' => 'Yes, we provide custom solutions tailored to your specific needs. Please contact our sales team for more information.',
        'Chat with a live person' => 'I apologize, but live chat is currently unavailable. Is there anything else I can help you with?'
    ];

    public function handle(Request $request)
    {
        $message = $request->input('message');

        if (array_key_exists($message, $this->questions)) {
            return response()->json(['message' => $this->questions[$message]]);
        }

        return response()->json(['message' => "I'm sorry, I didn't understand that. Here are some questions you can ask: " . implode(', ', array_keys($this->questions))]);
    }
}
