<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserLoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Display the specified resource.
     * @param  string  $email
     * @param  string  $password
     * @param  string  $name
     * @return \Illuminate\Http\Response
     * @OA\Post(
     *     path="/auth/login",
     *     tags={"Autenticacion"},
     *     summary="Opcion para iniciar sesion",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="name",
     *                          type="string",
     *                          example ="Google Chrome"
     *                      ),
     *                      @OA\Property(
     *                          property="email",
     *                          type="string",
     *                          example="superadmin@example.com"
     *                      ),
     *                      @OA\Property(
     *                          property="password",
     *                          type="string",
     *                          example="12345678"
     *                      )
     *                 ),
     *                 example={
     *                     "name":"Google Chrome",
     *                     "email":"superadmin@example.com",
     *                     "password":"12345678"
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Valid credentials",
     *          @OA\JsonContent(
     *                @OA\Property(property="token", type="string", example="randomtokenasfhajskfhajf398rureuuhfdshk"),
     *                      @OA\Property(property="token_type", type="string", example="Bearer"),
     *                      @OA\Property(property="message", type="string", example="Success"),
     *          )
     *      ),
     *     @OA\Response(
     *         response=422,
     *         description="Usuario o Contraseña no validas",
     *         @OA\MediaType(
     *           mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error.",
     *         @OA\MediaType(
     *           mediaType="application/json",
     *         )
     *     )
     * )
     */
    public function login(UserLoginRequest $request)
    {
        try {
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
