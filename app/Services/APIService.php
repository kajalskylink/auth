<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class APIService
{
    /**
     * Create a new class instance.
     */
    private mixed $userServiceAPIUrl;
    private mixed $data;
    private mixed $url;
    private mixed $method;

    public function __construct()
    {
        $this->userServiceAPIUrl = env("USER_SERVICE_API_URL");
        $this->data = null;
        $this->url = null;
        $this->method = null;
    }

    public function sendRequest()
    {
        $data = $this->data;
        $header = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'X-Api-Key' => env('USER_SERVICE_API_KEY'),
        ];

        $url = $this->userServiceAPIUrl . $this->url;

         if ($this->method === 'POST') {
            $response = Http::withHeaders($header)->post($url, $data);
        } else if($this->method === 'GET') {
            $response = Http::withHeaders($header)->get($url, $data);
        }else if ($this->method === 'DELETE') {
            $response = Http::withHeaders($header)->delete($url, $data);
        } else {
            throw new \Exception('Unsupported HTTP method: ' . $this->method);
        }
        // Log::info('response', ['response' => $response]);
        return $response;
    }

    public function createUser($data)
    {
        $this->data = $data;
        $this->url = '/api/user-service/register';
        $this->method = 'POST';

        return $this->sendRequest();
    }

    public function checkUser($data)
    {
        $this->data = $data;
        $this->url = '/api/user-service/check-user';
        $this->method = 'GET';

        return $this->sendRequest();
    }

    public function updateToken($data)
    {
        $this->data = $data;
        $this->url = '/api/user-service/update-token';
        $this->method = 'POST';

        return $this->sendRequest();
    }

    public function logout($token)
    {
        $url = $this->userServiceAPIUrl . '/api/user-service/logout';

        return Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
            'X-Api-Key' => env('USER_SERVICE_API_KEY'),
        ])->post($url);
    }

}
