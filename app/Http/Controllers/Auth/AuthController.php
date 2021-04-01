<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class AuthController extends Controller
{

    /**
     * Controlador para realizar login no sistema e retornar o jwt.
     * @route Route::post('/login', 'AuthController@login');
     * URI 'login'
     * @method POST
     *
     * @param Request $request
     * @return mixed
     */
    public function login(Request $request)
    {
        $creds = $request->only(['email','password']);
        if(!$token = auth()->attempt($creds)){

            return response()->json([
                "status" => false,
                "status_code" => 401,
                "message" => "E-mail ou Senha Inválidos"
            ] , 401 );

        }

        return $this->respondWithToken($token);
    }

    /**
     * Controlador para realizar logout do sistema.
     * @route Route::middleware('auth:web')->post('/logout', 'Auth\AuthController@logout');
     * URI 'logout'
     * @method POST
     *
     * @return mixed
     */
    public function logout()
    {

        try {

            auth()->logout();

            return response()->json([
                'return' => true,
                'status' => 200,
                'message' => "Usuário deslogado com sucesso !"
            ]);

        } catch (TokenExpiredException $exception) {
            return response()->json(array(
                'return' => false,
                'status' => 200,
                'message' => "Sessão expirada !"));
        }

    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
        ]);
    }

}
