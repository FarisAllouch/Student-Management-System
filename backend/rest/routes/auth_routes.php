<?php

require_once __DIR__ . '/../services/AuthService.class.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

Flight::set('auth_service', new AuthService());

Flight::group('/auth', function() {

    /**
     * @OA\Post(
     *      path="/auth/login",
     *      tags={"auth"},
     *      summary="Login to system using email and password",
     *      @OA\RequestBody(
     *          description="Credentials",
     *          required=true,
     *          @OA\JsonContent(
     *              required={"Email", "Paswword"},
     *              @OA\Property(property="Email", type="string", example="example@example.com", description="User email address"),
     *              @OA\Property(property="Password", type="string", example="somepassword", description="User password"),
     *          )
     *      ),
     *      @OA\Response(
     *           response=200,
     *           description="User data and JWT"
     *      )
     * )
     */
    Flight::route('POST /login', function() {
        $payload = Flight::request()->data->getData();

        $user = Flight::get('auth_service')->get_user_by_email($payload['Email']);

        if(!$user || !password_verify($payload['Password'], $user['Password']))
            Flight::halt(500, "Invalid email or password");

        // Remove sensitive data
        unset($user['Password']);

        $jwt_payload = [
            'user' => $user,
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24) //valid for a day
        ];

        $token = JWT::encode(
            $jwt_payload,
            JWT_SECRET,
            'HS256'
        );

        // Set user in Flight's context
        Flight::set('user', $user);

        Flight::json(
            array_merge((array)$user, ['token' => $token])
        );
    });

    /**
     * @OA\Post(
     *      path="/auth/logout",
     *      tags={"auth"},
     *      summary="Logout from the system",
     *      security={
     *          {"ApiKey": {}}
     *      },
     *      @OA\Response(
     *           response=200,
     *           description="Success response or exception if unable to verify jwt token"
     *      )
     * )
     */
    Flight::route('POST /logout', function(){
        try {
            $token = Flight::request()->getHeader("Authentication");
            if(!$token) 
                Flight::halt(401, "Missing authentication header");

            $decoded_token = JWT::decode($token, new Key(JWT_SECRET, 'HS256'));

            Flight::json([
                'jwt_decoded' => $decoded_token,
                'user' => $decoded_token->user
            ]);
        } catch (\Exception $e) {
            Flight::halt(401, $e->getMessage());
        }
    });
});