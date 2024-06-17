<?php
namespace App\Http\Controllers;

use Auth;
use Illuminate\Support\Facades\Validator;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Symfony\Component\HttpFoundation\Response;
use JWTAuth;
use Illuminate\Http\JsonResponse;

class JWTController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(
            'auth:api', ['except' => ['login', 'loginAdmin', 'loginEcommerce', 'register']]
        );
    }


    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:100',
            'surname' => 'required|string|min:2|max:100',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $user = User::create([
            'name' => $request->name,
            'surname' => $request->surname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'type_user' => $request->type_user,
        ]);

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user 
        ], 201);
    }

    public function loginAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        if (!$token = auth('api')->attempt(['email' => $request->email, 'password'=>$request->password, 'state' => 1, 'type_user' => 2 ])) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    
    public function loginEcommerce(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        if (!$token = auth('api')->attempt(['email' => $request->email, 'password'=>$request->password, 'state' => 1 ])) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }


    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();
        return response()->json(['message' => 'User Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }


    public function perfile()
    {
        return response()->json(auth('api')->user());
    }



    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return JsonResponse
     */
    protected function respondWithToken(string $token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => auth('api')->user(),
        ]);
    }
}



