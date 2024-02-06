<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $this->validateLogin($request);

            if (Auth::attempt($request->only('email', 'password'))) {
                $response = [
                    'success' => true,
                    'token' => $request->user()->createToken($request->name)->plainTextToken,
                    'token_type' => 'Bearer',
                    'message' => 'success'
                ];

                return response()->json($response);
            }

            throw ValidationException::withMessages([
                'email' => 'Credenciales no válidas.', // 'email' => ['Invalid credentials.'],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validación fallida', // 'Validation failed',
                'errors' => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Unauthorized',
            ], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function validateLogin(Request $request)
    {
        return $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'name' => 'required',
        ]);
    }

    public function me(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                throw new \Exception('User not authenticated');
            }

            return response()->json([
                'success' => true,
                'message' => 'Success',
                'user' => $user,
                // 'permissions' => $user->getAllPermissions(), // Obtén todos los permisos del usuario
                // 'roles' => $user->getRoleNames(), // Obtén todos los roles del usuario
                // 'roles_and_permissions' => $this->getUserRolesAndPermissions($user->id),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
                'request' => $request,
            ], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function logout()
    {
        $user = Auth::user();
        return response()->json([
            'success' => true,
            'data' => $user,
            'message' => 'muy bien'
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'success' => true,
            'data' => 'mis datos refresh',
            'message' => 'muy bien'
        ]);
    }
}
