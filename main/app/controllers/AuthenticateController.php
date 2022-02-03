<?php
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthenticateController extends \BaseController {
    /**
     * @api {post} ea/login Logowanie się do usługi.
     * @apiName Login
     * @apiGroup Autoryzacja
     *
     * @apiParam {String} login Login do konta
     * @apiParam {String} password Hasło do konta
     * @apiParam {String} api_key Klucz api do modułu
     *
     * @apiSuccessExample Zakończone powodzeniem
     *     HTTP 200
     *     {
     *       "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXUyJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6XC9cL2lkZWF3LnRlc3RcL2FwaVwvZWFcL2xvZ2luIiwiaWF0IjoiMTU3MTQwMzUzNiIsImV4cCI6IjE1NzE0MDcxMzYiLCJuYmYiOiIxNTcxNDAzNTM2IiwianRpIjoiMWE0N2EzYTMzNDM4YjA3ZWI5MmRkMDcwODk1ZWE1ZDUifQ.ZmVjYjlhZjk4M2FlYzU5NmZhMzA2MWIyNGI4YjExYmU0ZWUyMTNlYzM1NDcwNzVhNWE2ZjRjMTE3NDJlZjMzOA"
     *     }
     *
     * @apiErrorExample Nieprawidłowy login lub hasło
     *     HTTP 401
     *     {
     *       "error": "invalid_credentials"
     *     }
     *
     * @apiErrorExample Brak przesłanego api key
     *     HTTP 400
     *     {
     *       "error": "api_key_required"
     *     }
     *
     * @apiErrorExample Błędny api key
     *     HTTP 400
     *     {
     *       "error": "api_key_invalid"
     *     }
     */

    public function authenticate()
    {
        // grab credentials from the request
        $credentials = Input::only('login', 'password');

        $validator = Validator::make($credentials ,
            array(
                'login' => 'required',
                'password' => 'required'
            )
        );

        if($validator -> fails()){
            return Response::json(['error' => 'invalid_credentials'], 401);
        }

        try {
            $user = ApiUser::where('login',$credentials['login'])->first();

            if($user){
                if(!Hash::check(Input::get('password'), $user->password)){
                    return Response::json(['error' => 'invalid_credentials'], 401);
                }
            }else{
                return Response::json(['error' => 'invalid_credentials'], 401);
            }

            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::fromUser($user)) {
                return Response::json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return Response::json(['error' => 'could_not_create_token'], 500);
        }

        // all good so return the token
        return Response::json(compact('token'));
    }

    /**
     * @api {post} ea/refresh-token Odświeżenie tokenu.
     * @apiName Odświeżenie tokenu
     * @apiGroup Autoryzacja
     *
     * @apiHeader {String} Authorization Bearer: Token sesji
     *
     * @apiParam {String} api_key Klucz api do modułu
     *
     * @apiSuccessExample Zakończone powodzeniem
     *     HTTP 200
     *     {
     *       "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXUyJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6XC9cL2lkZWF3LnRlc3RcL2FwaVwvZWFcL2xvZ2luIiwiaWF0IjoiMTU3MTQwMzUzNiIsImV4cCI6IjE1NzE0MDcxMzYiLCJuYmYiOiIxNTcxNDAzNTM2IiwianRpIjoiMWE0N2EzYTMzNDM4YjA3ZWI5MmRkMDcwODk1ZWE1ZDUifQ.ZmVjYjlhZjk4M2FlYzU5NmZhMzA2MWIyNGI4YjExYmU0ZWUyMTNlYzM1NDcwNzVhNWE2ZjRjMTE3NDJlZjMzOA"
     *     }
     *
     * @apiErrorExample Brakujący token
     *     HTTP 400
     *     {
     *       "error": "token_not_provided"
     *     }
     *
     * @apiErrorExample Wygasły token
     *     HTTP 401
     *     {
     *       "error": "token_invalid"
     *     }
     *
     * @apiErrorExample Brak przesłanego api key
     *     HTTP 400
     *     {
     *       "error": "api_key_required"
     *     }
     *
     * @apiErrorExample Błędny api key
     *     HTTP 400
     *     {
     *       "error": "api_key_invalid"
     *     }
     */

    public function refresh()
    {
        $token = JWTAuth::refresh(JWTAuth::getToken());

        return Response::json(compact('token'));
    }
}