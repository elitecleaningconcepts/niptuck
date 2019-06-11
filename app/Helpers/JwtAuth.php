<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\User;

class JwtAuth{

    public $key;

    public function __construct(){
        $this->key = "esto_es_una_clave_super_secreta_123456789";
    }

    public function singup($email, $password, $getToken = null){

        //Buscar si exite el usuario con sus respectivas contrasenas
        $user = User::where([
            'email' => $email,
            'password' => $password
        ])->first();

        //Comprobar si son correctas(objeto)
        $singup = false;

        if(is_object($user)){
            $singup = true;
        }

        //Generar el token con los datos del usuario identificado
        if($singup){
            $token = array(
                'sub'       => $user->id,
                'email'     => $user->email,
                'name'      => $user->name,
                'role'      => $user->role,
                'iat'       => time(),
                'exp'       => time() + (7 * 24 * 60 * 60),
            );

            $jwt = JWT::encode($token,$this->key, 'HS256');
            $decode = JWT::decode($jwt, $this->key, ['HS256']);

            //Devolver los datos codificados o el token en funcion de un parametro
            if(is_null($getToken)){
                $data = $jwt;
            }else{
                $data = $decode;
            }
        }else{

            $data = array(
                'status' => 400,
                'message' => 'Login incorrecto',
            );

        }

        return $data;

    }

    public function checkToken($jwt, $getIdentity = false){

        $auth = false;

        try{

            $jwt = str_replace('"','',$jwt);
            $decode = JWT::decode($jwt, $this->key, ['HS256']);

        }catch(\UnexpectedValueException $e){

            $auth = false;

        }catch(\Domainception $e){

            $auth = false;

        }

        if(!empty($decode) && is_object($decode) && isset($decode->sub)){

            $auth = true;

        }else{

            $auth = false;
        }

        if($getIdentity){

            return $decode;

        }

        return $auth;

    }

}