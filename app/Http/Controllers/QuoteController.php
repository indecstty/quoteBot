<?php
 
namespace App\Http\Controllers;
 
use App\Quote;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
 
 
class QuoteController extends Controller{
 
 
    public function index(){
        $quotes  = Quote::orderBy('created_at', 'desc')->get();
		return view('admin', ['quotes' => $quotes, 'name' => "Moro"]);
    }
	
	public function delete($id){
        Quote::destroy($id);
		return redirect(url('bots/quoteBot'));
    }
 
    public function processRequest(Request $request){
 
        $input = $request->all();
        if(isset($input['message']['text'])){
            if(!empty($input['message']['text']) && $input['message']['text'] === '/addquote' || $input['message']['text'] === '/addquote@IndecsTampereQuoteBot'){
                $request = file_get_contents('https://api.telegram.org/bot'.env('BOT_TOKEN').'/sendMessage?chat_id='.$input['message']['chat']['id'].'&text='.rawurlencode('/addquote syntax: /addquote "Quote" - Person').'&reply_to_message_id='.$input['message']['message_id']);
                return;
            }
        
            if(!empty($input['message']['text']) && $input['message']['text'] === '/randomquote' || $input['message']['text'] === '/randomquote@IndecsTampereQuoteBot'){
                if(Quote::where(['chat' => $input['message']['chat']['id']])->count() == 0){
                    $request = file_get_contents('https://api.telegram.org/bot'.env('BOT_TOKEN').'/sendMessage?chat_id='.$input['message']['chat']['id'].'&text='.rawurlencode('No quotes found!').'&reply_to_message_id='.$input['message']['message_id']);
                    return;
                }
                
                $quote = Quote::where('chat', '=', $input['message']['chat']['id'])->get()->random(1);
                $quotestring = $quote->quote . ' - ' . $quote->by;
                $request = file_get_contents('https://api.telegram.org/bot'.env('BOT_TOKEN').'/sendMessage?chat_id='.$input['message']['chat']['id'].'&text='.rawurlencode($quotestring).'&reply_to_message_id='.$input['message']['message_id']);
                return;
            }
        }
        if(!empty($input['message']['text'])){
            $text_parts = explode(' ', $input['message']['text'], 2);
            $command = $text_parts[0];
        
			
            if($command == '/addquote' || $command == '/addquote@IndecsTampereQuoteBot'){
				
                $matches = array();
                if(preg_match('/(\".+\")\s*-\s*(\S+.*)/i', $text_parts[1], $matches) != 1){
                    $request = file_get_contents('https://api.telegram.org/bot'.env('BOT_TOKEN').'/sendMessage?chat_id='.$input['message']['chat']['id'].'&text='.rawurlencode('Invalid command. Correct syntax: /addquote "Quote" - Person').'&reply_to_message_id='.$input['message']['message_id']);
                    return;
                }
				$ca = $input['message']['chat']['id'];
				// file_get_contents('https://api.telegram.org/bot'.env('BOT_TOKEN').'/sendMessage?chat_id='.$input['message']['chat']['id'].'&text='.rawurlencode($ca).'&reply_to_message_id='.$input['message']['message_id']);
				$quote = new Quote;
            
                $quote->quote = $matches[1];
                $quote->by = $matches[2];
                $quote->from = $input['message']['from']['id'];
                $quote->chat = $input['message']['chat']['id'];
            
                $quote->save();
            
                $request = file_get_contents('https://api.telegram.org/bot'.env('BOT_TOKEN').'/sendMessage?chat_id='.$input['message']['chat']['id'].'&text='.rawurlencode('Quote saved!').'&reply_to_message_id='.$input['message']['message_id']);
        
            } elseif($command == '/randomquote' || $command == '/randomquote@IndecsTampereQuoteBot'){
                $by = $text_parts[1];
                if(Quote::where(['chat' => $input['message']['chat']['id'], 'by' => $by])->count() == 0){
                    $request = file_get_contents('https://api.telegram.org/bot'.env('BOT_TOKEN').'/sendMessage?chat_id='.$input['message']['chat']['id'].'&text='.rawurlencode('No quotes found for '. $by . '!').'&reply_to_message_id='.$input['message']['message_id']);
                    return;
                }
                $quote = Quote::where(['chat' => $input['message']['chat']['id'], 'by' => $by])->get()->random(1);
                $quotestring = $quote->quote . ' - ' . $quote->by;
                $request = file_get_contents('https://api.telegram.org/bot'.env('BOT_TOKEN').'/sendMessage?chat_id='.$input['message']['chat']['id'].'&text='.rawurlencode($quotestring).'&reply_to_message_id='.$input['message']['message_id']);
                return;
            }
        }
        return;
 
    }
}
