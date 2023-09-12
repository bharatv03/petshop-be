<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1\Admin;

use App\Http\{Controllers\ApiController, 
    Requests\AdminLoginRequest, Requests\AdminRegisterationRequest};
use App\Helpers\{CommonHelper, Auth\TokenHelper, Auth\UserHelper};
use App\Repositories\UserRepositoryInterface;
use Illuminate\{Support\Str,HTTP\JsonResponse,Support\Facades\DB,
    Database\QueryException};

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
        try {
            $user = $this->userRepository->create($input);
            $success = [
                'user' => $user,
            ];
            return $this->sendResponse($success, __('message.admin.register'), HTTP_OK);
        } catch (QueryException $e) {
            DB::rollBack();
            $this->sendResponse('Database error: ' . $e->getMessage, HTTP_INTERNAL_SERVER_ERROR);
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
    public function login(AdminLoginRequest $request): JsonResponse
    {
        $input = $request->safe()->only(['email', 'password']);
        $remember = $request->remember;
        $input['is_admin'] = true;
        $response = CommonHelper::LoginAttempt($input, $remember);

        if(isset($response['error']))
            return $this->sendError($response['error'], HTTP_UNPROCESSABLE_ENTITY);
        else
            return $this->sendResponse($response, __('message.admin.login'), HTTP_OK);
    }
}