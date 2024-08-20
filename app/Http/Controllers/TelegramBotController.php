<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use Illuminate\Http\Request;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Conversations\Conversation;

class TelegramBotController extends Controller
{
    protected $botman;

    public function __construct()
    {
        // Initialize BotMan here
        $config = config('botman');
        DriverManager::loadDriver(\BotMan\Drivers\Telegram\TelegramDriver::class);
        $this->botman = BotManFactory::create($config);
    }

    public function handle(Request $request)
    {
        // When the user types 'Start', show the welcome message and menu
        $this->botman->hears('Start', function (BotMan $bot) {
            $question = Question::create("Welcome! Please select an option:")
                ->addButton(Button::create('About Us')->value('about_us'))
                ->addButton(Button::create('Dev Group')->value('dev_group'))
                ->addButton(Button::create('Writers Group')->value('writers_group'))
                ->addButton(Button::create('Designer Group')->value('designer_group'))
                ->addButton(Button::create('Earn')->value('earn'))
                ->addButton(Button::create('Twitter')->value('twitter'))
                ->addButton(Button::create('Select State for WhatsApp Group')->value('select_state')); // Add the state selection button
    
            $bot->reply('Welcome! Please select an option from the menu below:');
            $bot->reply($question); // Sends the menu with buttons
        });
    
        // Handle the state selection button
        $this->botman->hears('select_state', function (BotMan $bot) {
            $states = [
                'California', 'New York', 'Texas', 'Florida', 'Illinois',
                'Pennsylvania', 'Ohio', 'Georgia', 'North Carolina', 'Michigan',
                'Virginia', 'New Jersey', 'Washington', 'Arizona', 'Massachusetts',
                'Tennessee', 'Indiana', 'Missouri', 'Maryland', 'Wisconsin'
                // Add more states here
            ];
    
            // Dynamically create buttons for each state
            $stateQuestion = Question::create("Please select your state:");
            foreach ($states as $state) {
                $stateQuestion->addButton(Button::create($state)->value($state));
            }
    
            $bot->reply($stateQuestion); // Sends state buttons
        });
    
        // Handle any state selection using a wildcard to capture the state
        $this->botman->hears('{state}', function (BotMan $bot, $state) {
            $links = [
                'California' => 'https://wa.me/1234567890',
                'New York' => 'https://wa.me/0987654321',
                'Texas' => 'https://wa.me/1122334455',
                // Add more states and their WhatsApp group links here
            ];
    
            // Check if the selected state has a WhatsApp link
            if (array_key_exists($state, $links)) {
                $bot->reply("Here is the WhatsApp group link for $state: " . $links[$state]);
            } else {
                $bot->reply("State not recognized. Please enter a valid state.");
            }
        });
    
        // Handle About Us option
        $this->botman->hears('about_us', function (BotMan $bot) {
            $bot->reply('About us: [Your About Us Info]');
        });
    
        // Handle Dev Group option
        $this->botman->hears('dev_group', function (BotMan $bot) {
            $bot->reply('Join the Dev Group here: [Dev Group Link]');
        });
    
        // Handle Writers Group option
        $this->botman->hears('writers_group', function (BotMan $bot) {
            $bot->reply('Join the Writers Group here: [Writers Group Link]');
        });
    
        // Handle Designer Group option
        $this->botman->hears('designer_group', function (BotMan $bot) {
            $bot->reply('Join the Designer Group here: [Designer Group Link]');
        });
    
        // Handle Earn option
        $this->botman->hears('earn', function (BotMan $bot) {
            $bot->reply('Earn money here: https://earn.superteam.fun/');
        });
    
        // Handle Twitter option
        $this->botman->hears('twitter', function (BotMan $bot) {
            $bot->reply('Follow us on Twitter: [Twitter Account Link]');
        });
    
        // Handle the incoming request
        $this->botman->listen();
    }
    
    
    
}
