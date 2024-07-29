<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Str;


class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */


    // Client creado desde seeders
     private $clientId = 100;
     private $clientSecret = "wsBa0mp4jwSTYssUGHX5xoqD9IC0X95Gfpg0w3uY";

     // Usuario creado desde seeders
     private $userName = "usuario@email.com";
     private $userPassword = "12345678";


     public function test_ObtenerTokenConClientIdValido()
    {
            

        $response = $this->post('/oauth/token',[
            "username" => $this -> userName,
            "password" => $this -> userPassword,
            "grant_type" => "password",
            "client_id" => $this -> clientId,
            "client_secret" => $this -> clientSecret
        ]);

        // Validamos status 200
        $response->assertStatus(200);

        // Validamos que recibamos los campos de Json correspondientes
        $response->assertJsonStructure([
            "token_type",
            "expires_in",
            "access_token",
            "refresh_token"
        ]);

        // Validamos que el campo "token_type" tenga el valor "Bearer"
        $response->assertJsonFragment([
            "token_type" => "Bearer"
        ]);

    }

    public function test_ObtenerTokenConClientIdInvalido()
    {
         
        $response = $this->post('/oauth/token',[
            "grant_type" => "password",
            "client_id" => "234",
            "client_secret" => Str::Random(8)
        ]);

        // Validamos obtener status 401
        $response->assertStatus(401);

        // Validanos JSON obtenido
        $response->assertJsonFragment([
            "error" => "invalid_client",
            "error_description" => "Client authentication failed",
            "message" => "Client authentication failed"
        ]);
    }

    public function test_ValidarTokenSinEnviarToken()
    {
        $response = $this->get('/api/v1/validate');

        // Validamos obtener status 500
        $response->assertStatus(500);
        
    }

    public function test_ValidarTokenConTokenInvalido()
    {
        // Enviamos un string random como Token
        $response = $this->get('/api/v1/validate',[
            [ "Authorization" => "Bearer " . Str::Random(40)]
        ]);

        // Validamos obtener Status 500
        $response->assertStatus(500);
        
    }

    public function test_ValidarTokenConTokenValido()
    {
        // Obtenemos Token
        $tokenResponse = $this->post('/oauth/token',[
            "username" => $this -> userName,
            "password" => $this -> userPassword,
            "grant_type" => "password",
            "client_id" => $this -> clientId,
            "client_secret" => $this -> clientSecret
        ]);

        // Pasamos JSON obtenido a Array
        $token = json_decode($tokenResponse -> content(),true);
        
        // Enviamos peticion para validar token

        $response = $this->get('/api/v1/validate',
            [ "Authorization" => "Bearer " . $token ['access_token']]
        );

        // Validamos obtener status 200
        $response->assertStatus(200);
        
    }

    public function test_LogoutSinToken()
    {
        // Enviamos peticion sin Token
        $response = $this->get('/api/v1/logout');

        // Validamos obtener Status 500
        $response->assertStatus(500);
        
    }

    public function test_LogoutConTokenInvalido()
    {
        // Enviamos un string random como Token
        $response = $this->get('/api/v1/logout',[
            [ "Authorization" => "Bearer " . Str::Random(40)]
        ]);

        // Validamos obtener status 500
        $response->assertStatus(500);
        
    }

    public function test_LogoutConTokenValido()
    {
        $tokenResponse = $this->post('/oauth/token',[
            "username" => $this -> userName,
            "password" => $this -> userPassword,
            "grant_type" => "password",
            "client_id" => $this -> clientId,
            "client_secret" => $this -> clientSecret
        ]);

        // Pasamos JSON obtenido a Array
        $token = json_decode($tokenResponse -> content(),true);
        
        // Enviamos peticion para validar token
        $response = $this->get('/api/v1/logout',
            [ "Authorization" => "Bearer " . $token ['access_token']]
        );

        // Validamos obtener status 200
        $response->assertStatus(200);

        // Validamos JSON de respuesta
        $response->assertJsonFragment(
            ['message' => 'Token Revoked']
        );
        
    }
}
