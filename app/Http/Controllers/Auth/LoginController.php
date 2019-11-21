<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Auth\Events\Registered;
use Cookie;

class LoginController extends Controller
{
    private $client;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->client = new Client;
    }

    public function start()
    {
        return view('login');
    }

    public function redirect(Request $request)
    {
        $request->session()->put('state', $state = \Str::random(40));

        $query = http_build_query([
            'client_id' => env('OAUTH_CLIENT_ID'),
            'redirect_uri' => route('oauth.login'),
            'response_type' => 'code',
            'state' => $state,
        ]);

        return redirect(env('OAUTH_AUTHORIZE_URI').'?'.$query);
    }

    public function login(Request $request)
    {
        $state = $request->session()->pull('state');

        throw_unless(
            strlen($state) > 0 && $state === $request->state,
            \InvalidArgumentException::class
        );

        [
            'access_token' => $access_token,
            'refresh_token' => $refresh_token,
            'expires_in' => $expires_in,
        ] = $this->getUserTokens($request->code);

        $user = $this->createUser($access_token, $refresh_token, $expires_in);

        Cookie::queue(Cookie::make(
            'access_token',
            $access_token,
            $expires_in/60
        ));

        event(new Registered($user));
        auth()->guard()->login($user);

        return redirect()->route('profile', $user);
    }

    /*|========| Private functions |=======|*/

    /** 
     * Get access_token, refresh_token and expires_in
     * 
     * @param string
     * @return array
    **/ 
    public function getUserTokens(string $auth_code)
    {

        $response = $this->client->request(
            'POST',
            env('GET_OAUTH_ACCESS_TOKEN_URI'),
            [
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'client_id' => env('OAUTH_CLIENT_ID'),
                    'client_secret' => env('OAUTH_CLIENT_SECRET'),
                    'redirect_uri' => route('oauth.login'), 
                    'code' => $auth_code,
                ]
            ]
        );   

        $arr = json_decode((string) $response->getBody(), true);

        return [ 
            'access_token' => $arr['access_token'],
            'refresh_token' => $arr['refresh_token'],
            'expires_in' => $arr['expires_in'],
        ];
    }

    public function createUser(
        string $access_token,
        string $refresh_token,
        string $expires_in
    )
    {
        $user_info = json_decode((string) $this->client->request(
            'GET',
            env('RESOURCE_SERVER').'/api/users/me',
            [
                'headers' => [
                    'Authorization' => "Bearer $access_token"
                ]
            ]
        )->getBody(), true)['data'];

        $user = (new User)->create([
            'name' => $user_info['name'],
            'oauth_refresh_token' => $refresh_token,
            'oauth_expires_in' => $expires_in,
        ]);

        return $user;
    }
}
