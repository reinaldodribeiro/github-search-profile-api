<?php


namespace App\Guard;


use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWT;

class JwtGuard extends \Tymon\JWTAuth\JWTGuard
{

    public function __construct(JWT $jwt, UserProvider $provider, Request $request)
    {
        parent::__construct($jwt, $provider, $request);
    }

    public function user()
    {
        if ($this->user !== null) {
            return $this->user;
        }
        if ($this->jwt->setRequest($this->request)->getToken() &&
            ($payload = $this->jwt->check(true)) &&
            $this->validateSubject()
        ) {
            return $this->user = $this->provider->retrieveById($payload['sub']->id_user);
        }
    }
}
