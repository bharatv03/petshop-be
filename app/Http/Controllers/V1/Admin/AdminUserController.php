<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1\Admin;

use App\Http\{Controllers\ApiController, Requests\AdminUserUpdateRequest};
use App\Helpers\{CommonHelper, Auth\TokenHelper, Auth\UserHelper};
use App\Repositories\UserRepositoryInterface;
use Illuminate\{Support\Str,HTTP\JsonResponse};

class AdminUserController extends ApiController
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @OA\Post(
     *      path="/api/v1/user/user-edit/{uuid}",
     *      operationId="useEdit",
     *      tags={"Admin"},
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
    public function userEdit(AdminUserUpdateRequest $request, $uuid): JsonResponse
    {
        $input = $request->safe()->all();
        $input['password'] = bcrypt($input['password']);
        $user = $this->userRepository->create($input);

        if ($user) {
            $success = [
                'user' => $user,
            ];
    
            return $this->sendResponse($success, __('message.user.register'), HTTP_OK);
        }
        
    }
}