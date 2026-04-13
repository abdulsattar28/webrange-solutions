<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class QuoteController extends Controller
{
    public function index()
    {
        $url = "http://quotes.toscrape.com/";

        // cURL request
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $html = curl_exec($ch);
    // echo $html;
        curl_close($ch);

        // extract quotes (simple regex)
preg_match_all('/<div class="quote"(.*?)<\/div>/s', $html, $matches);
        $data = [];
        foreach ($matches[1] as $block) {

            preg_match('/<span class="text"[^>]*>(.*?)<\/span>/', $block, $quote);
            preg_match('/<small class="author"[^>]*>(.*?)<\/small>/', $block, $author);

            $data[] = [
                'quote' => $quote[1] ?? '',
                'author' => $author[1] ?? ''
            ];
        }

        return response()->json($data);
    }
}
