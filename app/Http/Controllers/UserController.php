<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Cookie;

class UserController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $user = auth()->user();

        return view('users.profile', compact('user'));
    }

    public function answers(Request $request)
    {
        if (!$access_token = $request->cookie('access_token')) {
            $access_token = $this->refreshAccessToken();
        }

        $answers = $this->getAnswers($access_token);

        return view('users.answers', compact('answers'));
    }

    public function questions(Request $request)
    {
        if (!$access_token = $request->cookie('access_token')) {
            $access_token = $this->refreshAccessToken();
        }

        $questions = $this->getQuestions($access_token);

        return view('users.questions', compact('questions'));
    }

    /*|========| Private functions |=======|*/

    private function getRefreshedTokens()
    {
        $response = json_decode((string) (new Client)->post(
            env('ACCESS_TOKEN_URL'),
            [
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => auth()->user()->oauth_refresh_token,
                    'client_id' => env('OAUTH_CLIENT_ID'),
                    'client_secret' => env('OAUTH_CLIENT_SECRET'),
                ]
            ]
        )->getBody(), true);

       return $response;
    }

    private function getQuestions(string $access_token)
    {
        $questions = json_decode((new Client)->get(
            env('RESOURCE_SERVER') . '/api/users/me/questions', 
            [
                'headers' => [
                    'Authorization' => "Bearer $access_token" 
                ]
            ]
        )->getBody(), true)['data'];

        return $questions;
    }

    private function getAnswers(string $access_token)
    {
        $answers = json_decode((new Client)->request(
            'GET',
            env('RESOURCE_SERVER') . "/api/users/me/answers",
            [
                'headers' => [
                    'Authorization' => "Bearer $access_token"
                ]
            ]            
        )->getBody(), true)['data'];

        return $answers;
    }

    private function refreshAccessToken()
    {
        [
            'access_token' => $access_token,
            'expires_in' => $expires_in,
            'refresh_token' => $refresh_token,
        ] = $this->getRefreshedTokens();

        auth()->user()->update(
            [
                'oauth_refresh_token' => $refresh_token,
            ]
        );

        Cookie::queue(Cookie::make(
            'access_token',
            $access_token,
            $expires_in/60
        ));

        return $access_token;
    }
}
