<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\{Controllers\ApiController, 
    Requests\UserLoginRequest, Requests\UserRegistrationRequest};
use App\Helpers\{CommonHelper, Auth\TokenHelper, Auth\UserHelper};
use App\Repositories\UserRepositoryInterface;
use Illuminate\{Support\Str,HTTP\JsonResponse,Support\Facades\DB,
    Database\QueryException};


class AuthController extends ApiController
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @OA\Post(
     *      path="/api/v1/user/create",
     *      operationId="register",
     *      tags={"User"},
     *      summary="Register a new user",
     *      description="Register a new user and return user details",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *              required={"first_name","last_name","email","password","password_confirmation",
     *              "address","phone_number"},
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
    public function register(UserRegistrationRequest $request): JsonResponse
    {
        $input = $request->safe()->all();

        $input['uuid'] = (string) Str::uuid();
        $input['password'] = bcrypt($input['password']);
        $user = $this->userRepository->create($input);

        try {
            $success = [
                'user' => $user,
            ];
    
            return $this->sendResponse($success, __('message.user.register'), HTTP_OK);
        } catch (QueryException $e) {
            $this->sendResponse('Database error: ' . $e->getMessage, HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }

    /**
     * @OA\Post(
     *      path="/api/v1/user/login",
     *      operationId="login",
     *      tags={"User"},
     *      summary="User Login",
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
    public function login(UserLoginRequest $request): JsonResponse
    {
        $input = $request->safe()->only(['email', 'password']);
        $remember = $request->remember;
        $input['is_admin'] = false;
        $response = CommonHelper::LoginAttempt($input, $remember);

        if(isset($response['error']))
            return $this->sendError($response['error'], HTTP_UNPROCESSABLE_ENTITY);
        else
            return $this->sendResponse($response, __('message.admin.login'), HTTP_OK);
    }
}