<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1\Admin;

use App\Http\{Controllers\ApiController, 
    Requests\AdminLoginRequest, Requests\AdminRegisterationRequest};
use App\Helpers\CommonHelper;
use App\Repositories\{UserRepository, JwtTokenRepository};
use Illuminate\{Support\Str, HTTP\JsonResponse, Database\QueryException};
use Illuminate\Http\Request;

class AdminAuthController extends ApiController
{
    protected $userRepository, $jwtTokenRepository;

    public function __construct(UserRepository $userRepository, JwtTokenRepository $jwtTokenRepository)
    {
        $this->userRepository = $userRepository;
        $this->jwtTokenRepository = $jwtTokenRepository;
    }

    /**
     * @OA\Post(
     *      path="/api/v1/admin/create",
     *      operationId="admin.register",
     *      tags={"Admin"},
     *      summary="Register a new admin user",
     *      description="Register a new admin user and return user details",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *              required={"first_name","last_name","email","password","password_confirmation",
     *              "avatar","address","phone_number"},
     *              @OA\Property(property="first_name", type="string"),
     *              @OA\Property(property="last_name", type="string"),
     *              @OA\Property(property="email", type="string", format="email"),
     *              @OA\Property(property="password", type="string", format="password"),
     *              @OA\Property(property="password_confirmation", type="string"),
     *              @OA\Property(property="avatar", type="file"),
     *              @OA\Property(property="address", type="string"),
     *              @OA\Property(property="phone_number", type="string"),
     *              @OA\Property(property="is_marketing", type="boolean"),
     *            ),
     *         ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful registration",
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Content not found"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Server Error"
     *      ),
     * )
     */
    public function register(AdminRegisterationRequest $request): JsonResponse
    {
        $input = $request->safe()->all();

        $input['uuid'] = (string) Str::uuid();
        $input['is_admin'] = true;
        $input['password'] = bcrypt($input['password']);
        try {
            $user = $this->userRepository->create($input);
            $success = [
                'user' => $user,
            ];
            return $this->sendResponse($success, __('message.admin.register'), HTTP_OK);
        } catch (QueryException $e) {
            $this->sendResponse('Database error: '. __('message.db.query_error'), HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/v1/admin/login",
     *      operationId="admin.login",
     *      tags={"Admin"},
     *      summary="Admin Login",
     *      description="Login a user and return Token",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *              required={"email","password"},
     *              @OA\Property(property="email", type="string", format="email"),
     *              @OA\Property(property="password", type="string", format="password"),
     *            ),
     *         ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Login successfull",
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Content not found"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Server Error"
     *      ),
     * )
     */
    public function login(AdminLoginRequest $request): JsonResponse
    {
        $input = $request->safe()->only(['email', 'password']);
        $remember = $request->remember;
        $input['is_admin'] = true;
        $response = CommonHelper::LoginAttempt($input, $remember, $this->jwtTokenRepository);

        if(isset($response['error']))
            return $this->sendError($response['error'], HTTP_UNPROCESSABLE_ENTITY);
        else
            return $this->sendResponse($response, __('message.admin.login'), HTTP_OK);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/admin/logout",
     *      operationId="admin.logout",
     *      tags={"Admin"},
     *      summary="Admin Logout",
     *      description="Logout a admin user and update expire date in DB",
     *      security={{"bearer_token":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Login successfull",
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Content not found"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Server Error"
     *      ),
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        $uniqueId = $request->uuidHeader.$request->tokenId;
        $response = CommonHelper::Logout($uniqueId, $this->jwtTokenRepository);
        return $this->sendResponse($response, __('message.user.logout'), HTTP_OK);
    }
}