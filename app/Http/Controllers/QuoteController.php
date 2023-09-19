<?php

namespace App\Http\Controllers;

use App\Quote;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    public function processRequest(Request $request)
    {
        $input = $request->all();

        // Check if the user has provided a command
        if (isset($input['message']['text'])) {
            $command = $input['message']['text'];

            // Handle the /addquote command
            if (strpos($command, '/addquote') === 0) {
                // Extract the quote and person from the command
                preg_match('/^"([^"]+)"\s*-\s*([^"]+)/i', $command, $matches);

                // Create a new quote and save it to the database
                $quote = new Quote();
                $quote->quote = $matches[1];
                $quote->by = $matches[2];
                $quote->from = $input['message']['from']['id'];
                $quote->chat = $input['message']['chat']['id'];
                $quote->save();

                // Send a response back to the user
                $request = file_get_contents('https://api.telegram.org/bot'.env('BOT_TOKEN').'/sendMessage?chat_id='.$input['message']['chat']['id'].'&text='.rawurlencode('Quote saved!').'&reply_to_message_id='.$input['message']['message_id']);
            } elseif (strpos($command, '/randomquote') === 0) {
                // Handle the /randomquote command
                $by = strpos($command, ' ') === 0 ? $command : substr($command, strpos($command, ' '));

                // Check if there are any quotes for the given person
                if (Quote::where(['chat' => $input['message']['chat']['id'], 'by' => $by])->count() === 0) {
                    $request = file_get_contents('https://api.telegram.org/bot'.env('BOT_TOKEN').'/sendMessage?chat_id='.$input['message']['chat']['id'].'&text='.rawurlencode('No quotes found for '. $by . '!').'&reply_to_message_id='.$input['message']['message_id']);
                    return;
                }

                // Select a random quote for the given person
                $quote = Quote::where(['chat' => $input['message']['chat']['id'], 'by' => $by])->get()->random(1);
                $quotestring = $quote->quote . ' - ' . $quote->by;

                // Send the random quote back to the user
                $request = file_get_contents('https://api.telegram.org/bot'.env('BOT_TOKEN').'/sendMessage?chat_id='.$input['message']['chat']['id'].'&text='.rawurlencode($quotestring).'&reply_to_message_id='.$input['message']['message_id']);
            }
        }

        return;
    }
}