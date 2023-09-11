<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1\Admin;

use App\Http\{Controllers\ApiController, 
    Requests\AdminLoginRequest, Requests\AdminRegisterationRequest};
use App\Helpers\{CommonHelper, Auth\TokenHelper, Auth\UserHelper};
use App\Repositories\UserRepositoryInterface;
use Illuminate\{Support\Str,HTTP\JsonResponse};

class AdminAuthController extends ApiController
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
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
     *          @OA\JsonContent(
     *              @OA\Property(property="token", type="string"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *              @OA\Property(property="errors", type="object"),
     *          ),
     *      ),
     * )
     */
    public function register(AdminRegisterationRequest $request): JsonResponse
    {
        $input = $request->safe()->all();

        $input['uuid'] = (string) Str::uuid();
        $input['is_admin'] = true;
        $input['password'] = bcrypt($input['password']);
        $user = $this->userRepository->create($input);

        if (!isset($user['error'])) {
            $success = [
                'user' => $user,
            ];
    
            return $this->sendResponse($success, __('message.admin.register'), HTTP_OK);
        }else{
            return $this->sendError('Unauthorized', $user, 401);
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
     *          @OA\JsonContent(
     *              @OA\Property(property="token", type="string", example="your-jwt-token"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *              @OA\Property(property="errors", type="object"),
     *          ),
     *      ),
     * )
     */
    public function login(UserLoginRequest $request): JsonResponse
    {
        $input = $request->safe()->only(['email', 'password']);
        $remember = $request->remember;
        $input['is_admin'] = true;
        $success = CommonHelper::LoginAttempt($input, $remember);

        return $this->sendResponse($success, __('message.admin.login'), HTTP_OK);
    }
}