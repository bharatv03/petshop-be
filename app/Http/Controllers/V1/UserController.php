<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\{Controllers\ApiController, Requests\UserUpdateRequest};
use App\Helpers\CommonHelper;
use App\Repositories\UserRepository;
use Illuminate\{HTTP\JsonResponse, Database\QueryException, Http\Request};
use App\GridClass\UserGrid;


class UserController extends ApiController
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/user/",
     *      operationId="index",
     *      tags={"User"},
     *      summary="View a new user details",
     *      description="View User details",
     *      @OA\Response(
     *          response=200,
     *          description="User details return successfully",
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
    public function index(Request $request): JsonResponse
    {
        try {
            $gridObj = new UserGrid;
            $uuid = $request->uuidHeader;
            $getUserDetails = CommonHelper::GetUserDetails($this->userRepository, $uuid);
            if(isset($getUserDetails['success']))
                return $this->sendResponse($getUserDetails['data'], $getUserDetails['success'], HTTP_OK);
            else
                return $this->sendError($getUserDetails['error'], HTTP_OK);

        } catch (QueryException $e) {
            $this->sendResponse('Database error: ' . __('message.db.query_error'), HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }

    /**
     * @OA\Put(
     *      path="/api/v1/user/edit/",
     *      operationId="userEdit",
     *      tags={"User"},
     *      summary="Update selected user details based on loggedin user",
     *      description="Update selected user details based on loggedin user",
     *      security={{"bearer_token":{}}},
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
     *              @OA\Property(property="avatar", type="string"),
     *              @OA\Property(property="address", type="string"),
     *              @OA\Property(property="phone_number", type="string"),
     *              @OA\Property(property="is_marketing", type="boolean"),
     *            ),
     *         ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successfully updated",
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
    public function userEdit(UserUpdateRequest $request): JsonResponse
    {
        $input = $request->safe()->all();
        $input['password'] = bcrypt($input['password']);
        
        try {
            $user = $this->userRepository->updateByUuid($input, $request->uuidHeader);
            $success = [
                'user' => $user,
            ];
            
            return $this->sendResponse($success, __('message.user.edit'), HTTP_OK);
        } catch (QueryException $e) {
            $this->sendResponse('Database error: ' . __('message.db.query_error'), HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/user/delete",
     *      operationId="userDelete",
     *      tags={"User"},
     *      summary="Deletion of non admin user",
     *      description="Deletion of non admin user",
     *      security={{"bearer_token":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="'User deleted successfully!",
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
    public function userDelete(Request $request): JsonResponse
    {
        try {
            $deleteUser = CommonHelper::DeleteUser($request->uuidHeader, $this->userRepository);

            if(isset($deleteUser['success']))
                return $this->sendResponse([], $deleteUser['success'], HTTP_OK);
            else
                return $this->sendError($deleteUser['error'], HTTP_OK);
        } catch (QueryException $e) {
            $this->sendResponse('Database error: ' . __('message.db.query_error'), HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }
}