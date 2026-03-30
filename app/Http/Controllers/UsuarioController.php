<?php

namespace App\Http\Controllers;

use App\Accidente;
use App\CauInm;
use App\CauInmTipo;
use App\User;
use Illuminate\Http\Request;
use Validator;

class UsuarioController extends Controller
{

    public function getListado()
    {

        return view('usuario.listado');
    }


    public function postTabla(Request $request)
    {
        $mostrando = $request->input('mostrando');
        $b         = $request->input('busqueda');
        $result    = User::where('name', 'like', '%' . $b . '%')
            ->orWhere('email', 'like', '%' . $b . '%')
            ->paginate($mostrando);
        return response()->json($result);
    }

    public function getCrear()
    {
        return view('usuario.crear');
    }

    public function postCrear(Request $request)
    {
        $name         = $request->input('d.name');
        $email        = $request->input('d.email');
        $password     = $request->input('d.password');
        $password_rep = $request->input('d.password_rep');
        $tipo         = $request->input('d.tipo');
        $vista        = $request->input('d.vista');

        $validator = Validator::make($request->input('d'), [
            'name'     => 'required|unique:users',
            'email'    => 'required|unique:users',
            'password' => 'required',
            'tipo'     => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['txt' => 'Error de Validación', 'tipo' => 2, 'err' => $validator->messages()]);
        }

        $usu           = new User;
        $usu->name     = $name;
        $usu->email    = $email;
        $usu->password = bcrypt($password);
        $usu->tipo     = $tipo;
        $usu->vista    = $vista;

        if ($usu->save()) {
            return response()->json(['txt' => 'Usuario creado', 'tipo' => 1]);
        } else {
            return response()->json(['txt' => 'Error al crear usuario', 'tipo' => 2]);
        }

    }

    public function postGuardar(Request $request)
    {
        $id       = $request->input('d.id');
        $name     = $request->input('d.name');
        $email    = $request->input('d.email');
        $tipo     = $request->input('d.tipo');
        $password = $request->input('d.password');
        $vista    = $request->input('d.vista');

        $validator = Validator::make($request->input('d'), [
            'name'     => 'required',
            'email'    => 'required',
            'password' => 'required',
            'tipo'     => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['txt' => 'Error de Validación', 'tipo' => 2, 'err' => $validator->messages()]);
        }


        $usu           = User::find($id);
        $usu->name     = $name;
        $usu->email    = $email;
        $usu->password = bcrypt($password);
        $usu->tipo     = $tipo;
        $usu->vista    = $vista;
        if ($usu->save()) {
            return response()->json(['txt' => 'Modificaciones guardadas', 'tipo' => 1]);
        } else {
            return response()->json(['txt' => 'Error al guardar modificaciones', 'tipo' => 2]);
        }
    }

    public function postGuardarTabla(Request $request)
    {
        $id       = $request->input('d.id');
        $tipo     = $request->input('d.tipo');
        $vista    = $request->input('d.vista');

        $usu           = User::find($id);
        $usu->tipo     = $tipo;
        $usu->vista    = $vista;
        if ($usu->save()) {
            return response()->json(['txt' => 'Modificaciones guardadas', 'tipo' => 1]);
        } else {
            return response()->json(['txt' => 'Error al guardar modificaciones', 'tipo' => 2]);
        }
    }

    public function getEditar($id)
    {
        return view('usuario.editar', ['id' => $id]);
    }

    public function postDatos(Request $request)
    {
        $id  = $request->input('id');
        $usu = User::find($id);
        return response()->json(['d' => $usu]);
    }

    public function postEliminar(Request $request){
        $id  = $request->input('id');

        $tot = Accidente::where('user_id', $id)->count();

        if($tot > 0){
            return response()->json(['txt' => 'Hay datos en accidentes con el usuario vinculado', 'tipo' => 2]);
        }
        $usu = User::find($id);
        if ($usu->delete()) {
            return response()->json(['txt' => 'Usuario Eliminado', 'tipo' => 1]);
        } else {
            return response()->json(['txt' => 'Error al eliminar Usuario', 'tipo' => 2]);
        }
    }
    
}


