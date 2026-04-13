<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $response = Http::withoutVerifying()
            ->withHeaders([
                'User-Agent' => 'MyLaravelApp/1.0',
                'Accept' => 'application/json',
            ])
            ->get('https://jsonplaceholder.typicode.com/posts');

        $statusCode = $response->status();

        if (!$response->successful()) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch posts',
                'http_status' => $statusCode
            ], 500);
        }

        $posts = collect($response->json());

        // Optional search
        if ($request->has('search')) {
            $search = strtolower($request->search);

            $posts = $posts->filter(function ($post) use ($search) {
                return str_contains(strtolower($post['title']), $search);
            });
        }

        $posts = $posts->take(10)->map(function ($post) {
            return [
                'title' => $post['title'],
                'body' => $post['body'],
            ];
        });

        return response()->json([
            'status' => true,
            'http_status' => $statusCode,
            'data' => $posts->values()
        ]);
    }
}