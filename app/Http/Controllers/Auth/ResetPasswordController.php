<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\{Controllers\ApiController, 
    Requests\ResetPasswordRequest};
use App\Repositories\{PasswordResetTokenRepository, UserRepository};

class ResetPasswordController extends ApiController
{
    protected $passwordTokenRepository, $userRepository;

    public function __construct(UserRepository $userRepository, PasswordResetTokenRepository $passwordTokenRepository, )
    {
        $this->passwordTokenRepository = $passwordTokenRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @OA\Post(
     *      path="/api/v1/user/reset-password-token",
     *      operationId="v",
     *      tags={"User"},
     *      summary="User forgot password",
     *      description="Apply to return token if the email valid to reset the password",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *              required={"email","password","password_confirmation","token"},
     *              @OA\Property(property="email", type="string", format="email"),
     *              @OA\Property(property="password", type="string", format="password"),
     *              @OA\Property(property="password_confirmation", type="string", format="password"),
     *              @OA\Property(property="token", type="string", format="string"),
     *            ),
     *         ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Password updated successfully",
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
    public function resetPassword(ResetPasswordRequest $request)
    {
        $input = $request->safe()->only(['email','token']);
        $checkToken = $this->passwordTokenRepository->checkToken($input);
        
        if (!$checkToken) {
            return $this->sendResponse([], __('message.user.invalid_token'), HTTP_UNAUTHORIZED);
        }
        $data['password'] = bcrypt($request->password);
        $where['email'] = $input['email'];
        
        $updatePassword = $this->userRepository->updateDataWhere($where, $data);
        $deleteToken = $this->passwordTokenRepository->deleteToken($where);
        return $this->sendResponse([], __('message.user.password_reset'), HTTP_OK);
    }
}

