<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\ModelNotFoundException;

trait GitHubAPI
{
    private $baseUrl = "https://api.github.com/";

    private function callApi($action, $url, $data=null)
    {
        $response = 'Http'::$action($this->baseUrl.$url, $data);
        $this->checkResponse($response);
        $responseData = $response->json();
        return $responseData;
    }

    protected function checkResponse($response)
    {
        if($response->status() === 404) {
            throw new ModelNotFoundException();
        }
    }

    public function searchProfile($username)
    {
        $url = 'users/'.$username;
        return $this->callApi('get', $url);
    }

}
