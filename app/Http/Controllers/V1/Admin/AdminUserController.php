<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1\Admin;

use App\Http\{Controllers\ApiController, Requests\AdminUserUpdateRequest};
use App\Repositories\UserRepositoryInterface;
use Illuminate\{HTTP\JsonResponse, Database\QueryException};
use App\Helpers\CommonHelper;
use App\GridClass\UserGrid;

class AdminUserController extends ApiController
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @OA\Put(
     *      path="/api/v1/admin/user-edit/",
     *      operationId="useEdit",
     *      tags={"Admin"},
     *      summary="Update selected user details based on uuid",
     *      description="Update selected user details based on uuid",
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
    public function userEdit(AdminUserUpdateRequest $request, $uuid): JsonResponse
    {
        $input = $request->safe()->all();
        $input['password'] = bcrypt($input['password']);
        
        try {
            $user = $this->userRepository->updateByUuid($input, $uuid);
            $success = [
                'user' => $user,
            ];
    
            return $this->sendResponse($success, __('message.user.edit'), HTTP_OK);
        } catch (QueryException $e) {
            $this->sendResponse('Database error: ' . __('message.db.query_error'), HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }

    /**
     * @OA\Get(
     *      path="/api/v1/admin/user-listing",
     *      operationId="userList",
     *      tags={"Admin"},
     *      summary="Display list of users",
     *      description="Display list of users",
     *      security={{"bearer_token":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successfully list successfully listed",
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
    public function userList(): JsonResponse
    {
        $gridObj = new UserGrid();
        try {
            $gridData = CommonHelper::GridManagement($this->userRepository, $gridObj);
            $success = [
                'user' => $gridData,
            ];
    
            return $this->sendResponse($success, __('message.user.list'), HTTP_OK);
        } catch (QueryException $e) {
            $this->sendResponse('Database error: ' . __('message.db.query_error'), HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/admin/user-delete/{uuid}",
     *      operationId="userDelete",
     *      tags={"Admin"},
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
    public function userDelete($uuid): JsonResponse
    {
        try {
            $deleteUser = CommonHelper::DeleteUser($uuid, $this->userRepository);

            if(isset($deleteUser['success']))
                return $this->sendResponse([], $deleteUser['success'], HTTP_OK);
            else
                return $this->sendError($deleteUser['error'], HTTP_OK);
        } catch (QueryException $e) {
            $this->sendResponse('Database error: ' . __('message.db.query_error'), HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }
}