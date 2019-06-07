<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UserController extends Controller
{
    public function prueba(Request $request)
    {
        return "Accion prueba controlador de usuarios";
    }

    public function register(Request $request)
    {

        // Recoger datos del usuario
        $json = $request->input('json',null);
        $params = json_decode($json); // Objeto
        $params_array = json_decode($json, true); // Array

        if(!empty($params) && !empty($params_array))
        {
            // Limpiar datos
            $params_array = array_map('trim',$params_array);

            // Validar datos
            $validate = \Validator::make($params_array,[
                'name'      => 'required',
                'email'     => 'required|email|unique:users',
                'password'  => 'required',
                'role'      => 'required',
            ]);

            if($validate->fails())
            {
                // La validacion ha fallado
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'El usuario no se ha creado correctamente',
                    'errors' => $validate->errors(),
                );
            }
            else
            {
                // La validacion pasa correctamente

                // Cifrar contrasena
                $pwd = password_hash($params->password, PASSWORD_BCRYPT, ['cost'=> 4]);

                // Crear usuario 
                $user = new User();
                $user->name = $params_array['name'];
                $user->email = $params_array['email'];
                $user->password = $pwd;
                $user->role = $params_array['role'];

                // Guardar usuario
                $user->save();

                $data = array(
                    'status'    => 'success',
                    'code'      => 200,
                    'message'   => 'Usuario creado correctamente',
                    'user'      => $user,      
                );
            }

        }
        else
        {
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'Los dato enviados no son correctos',
            );
        }

        return response()->json($data, 400);


        $data = array(
            'status' => 'error',
            'code' => 404,
            'message' => 'El usuario no se ha creado correctamente'
        );

        return response()->json($data,$data['code']);
    }

    public function login(Request $request)
    {
        return "Accion del login";
    }
}
