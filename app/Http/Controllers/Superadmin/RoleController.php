<?php

namespace App\Http\Controllers\Superadmin;
use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    
    protected $role;


    public function guard()
    {
        return Auth::guard();
    }


    public function __construct()
    {
        $this->middleware('auth:api');
        $this->role = $this->guard()->user();
    }

    public function roleUser()
    {
        return Auth::user()->roles_user == "Superadmin";
    }



     //Rest Api to get all  role
    //URL: http://127.0.0.1:8000/api/superadmin/roles
    public function getAllRoles(){

        if (Auth::guard()->check() &&  Auth::user()->roles_user != "Superadmin") {
            return response()->json([
                "status" => false,
                "reload" => false,
                "redirect_to" => route('login'),
                "title" => "AVERTISSEMENT",
                "message" => "Vous n'êtes pas autorisé. Vous n'êtes pas un superadministrateur",
            ]);
        } else {


        $roles= Role::all();
        return response()->json([
            "status" => "success",
            'type' => 'bearer',
            'role' => $roles
        ],200);
    }


    }



     //Rest Api to store role
    //URL: http://127.0.0.1:8000/api/superadmin/add-role
    public function storeRole(Request $request){

        if (Auth::guard()->check() &&  Auth::user()->roles_user != "Superadmin") {
            return response()->json([
                "status" => false,
                "reload" => false,
                "redirect_to" => route('login'),
                "title" => "AVERTISSEMENT",
                "message" => "Vous n'êtes pas autorisé. Vous n'êtes pas un superadministrateur",
            ]);
        } else {


        $messages = [
            "nom_role.required" => "Le nom du rôle est requis",
            "nom_role.max" => "Le nom du rôle est trop long",
            "nom_role.unique" => "ce type de role existe déjà dans la base",
        ];

        $validator = Validator::make($request->all(), [
            "nom_role" => "bail|required|max:50|unique:roles,nom_role",
        ], $messages);

        if ($validator->fails()) return response()->json([
            "status" => "error",
            "message" => $validator->errors()->first()
        ]);

        $role = new Role();
        $role->nom_role = $request->nom_role;
        $role->status_role = true;
        if($role->save()){
            return response()->json([
                "status" => "success",
                'type' => 'bearer',
                "message" => "le rôle ".$role->nom_role." "."a été crée avec succès."
            ], 200);
        }else{
            return response()->json([
                "status" => "error",
                'type' => 'bearer',
                "message" => "Erreur Serveur"
            ], 500);
        }


    }

    }

    //Rest Api to get one role
    //URL: http://127.0.0.1:8000/api/superadmin/get-one-role/{id}
    public function getOneRole(Request $request){
       
        if (Auth::guard()->check() &&  Auth::user()->roles_user != "superadmin") {
            return response()->json([
                "status" => false,
                "reload" => false,
                "redirect_to" => route('login'),
                "title" => "AVERTISSEMENT",
                "message" => "Vous n'êtes pas autorisé. Vous n'êtes pas un superadministrateur",
            ]);
        } else {


        $id_role=$request->id_role;
        $role= Role::find($id_role);
        if($role){
            return response()->json([
                "status" => "success",
                'type' => 'bearer',
                'role' => $role
            ],200);
        }else{
            return response()->json([
                "status" => "error",
                'type' => 'bearer',
                'role' => $role,
                'message' => "role non trouvé"
            ],404);
        }
    }
}

    //Rest Api to update one role
    //URL: http://127.0.0.1:8000/api/update-role/{id}
    public function updateRole(Request $request){

        if (Auth::guard()->check() &&  Auth::user()->roles_user != "superadmin") {
            return response()->json([
                "status" => false,
                "reload" => false,
                "redirect_to" => route('login'),
                "title" => "AVERTISSEMENT",
                "message" => "Vous n'êtes pas autorisé. Vous n'êtes pas un superadministrateur",
            ]);
        } else {


        $role =$this->getOneRole($request);
        if($role->getData()->role){
            $id_role=$role->getData()->role->id_role;
            $messages = [
              
                "nom_role.required" => "Le nom du rôle est requis",
                "nom_role.unique" => "ce type de role existe déjà dans la base",
            ];
            $validator = Validator::make($request->all(), [
                "nom_role" => "bail|required|unique:roles,nom_role",
            ], $messages);
    
            DB::table('roles')->where('id_role', $id_role)->update([
                'nom_role'=>$request->nom_role
            ]);
            $role =$this->getOneRole($request);
            return response()->json([
                "status" => "success",
                'type' => 'bearer',
                'role' => $role->getData(),
                'message' => "role updated successfully"
            ],200);
        }else{
            return response()->json([
                "status" => "error",
                'type' => 'bearer',
                'message' => "role not found"
            ],404);
        }
    }


    }

    //Rest Api to delete one role
    //URL: http://127.0.0.1:8000/api/superadmin/delete-role/{id}
    public function deleteRole(Request $request){

        if (Auth::guard()->check() &&  Auth::user()->roles_user != "superadmin") {
            return response()->json([
                "status" => false,
                "reload" => false,
                "redirect_to" => route('login'),
                "title" => "AVERTISSEMENT",
                "message" => "Vous n'êtes pas autorisé. Vous n'êtes pas un superadministrateur",
            ]);
        } else {
        $role =$this->getOneRole($request);
        if($role->getData()->role){
            $id_role=$role->getData()->role->id_role;
            $role= Role::find($id_role);
            $role->delete();
            return $this->getAllRoles();
        }else{
            return response()->json([
                "status" => "error",
                'type' => 'bearer',
                'message' => "role not found"
            ],404);
        }
    }
    
}


}
