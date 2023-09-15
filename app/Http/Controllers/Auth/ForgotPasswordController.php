<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Str;
use App\Repositories\UserRepository;
use App\Http\Controllers\ApiController;
use App\Http\Requests\ForgetPasswordRequest;
use App\Repositories\PasswordResetTokenRepository;

class ForgotPasswordController extends ApiController
{
    protected $passwordResetTokenRepository;
    protected $userRepository;

    public function __construct(PasswordResetTokenRepository $passwordResetTokenRepository,
    UserRepository $userRepository)
    {
        $this->passwordResetTokenRepository = $passwordResetTokenRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @OA\Post(
     *      path="/api/v1/user/forgot-password",
     *      operationId="sendResetLinkEmail",
     *      tags={"User"},
     *      summary="User forgot password",
     *      description="Apply to return token if the email valid to reset the password",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *              required={"email"},
     *              @OA\Property(property="email", type="string", format="email"),
     *            ),
     *         ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Password token sent successfully!",
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
    public function sendResetLinkEmail(ForgetPasswordRequest $request)
    {
        $user = $this->userRepository->getByFieldSingleRecord('email', $request->email);

        if (!$user) {
            return $this->sendResponse(__('message.user.email_not_found'), HTTP_NOT_FOUND);
        }

        $checkToken = $this->passwordResetTokenRepository->checkToken(['email' => $request->email]);
        if (!$checkToken) {
            // Generate a unique token
            $token = Str::random(60);
            $data = ['email' => $request->email, 'token' => $token];

            $this->passwordResetTokenRepository->addToken($data);
        } else {
            $data = ['email' => $request->email, 'token' => $checkToken->token];
        }
        //Mail code to be added

        return $this->sendResponse($data, __('message.user.password_reset_sent'), HTTP_OK);
    }
}
