<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreUserRequest;
use App\Http\Requests\Api\V1\UpdateUserRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Api\V1\UserOrderCollection;

class UserController extends Controller
{
    use ApiResponser;

    /**
     *
     * @OA\Get(
     *     path="/api/v1/user/orders",
     *     tags={"User"},
     *     summary="List all self-orders",
     *     operationId="getMyOrders",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number of pagination",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of elements at per page when paginating",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sortBy",
     *         in="query",
     *         description="Name of the field for sorting",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="desc",
     *         in="query",
     *         description="Expected order of data to search users",
     *         required=false,
     *         @OA\Schema(
     *             type="boolean",
     *             enum={true, false},
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server error"
     *     )
     * )
     */
    public function getMyOrders(Request $request)
    {
        $user = $this->guard()->user();

        $query = Order::where('user_id', $user->id);

        if ($request->sortBy && Schema::connection("mysql")->hasColumn('orders', $request->sortBy)) {

            if (filter_var($request->desc, FILTER_VALIDATE_BOOLEAN)) {

                $query->orderByDesc($request->sortBy);

            }
            else {

                $query->orderBy($request->sortBy);

            }

        }

        elseif (filter_var($request->desc, FILTER_VALIDATE_BOOLEAN)) {

            $query->latest();

        }

        return new UserOrderCollection($query->paginate($request->limit ?? 10));
    }

    /**
     * @OA\Post(
     *      path="/api/v1/user/create",
     *      tags={"User"},
     *      summary="Create a User account",
     *      operationId="store",
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *         required=true,
     *         description="Request properties",
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"first_name","last_name", "email", "password", "password_confirmation", "address", "phone_number"},
     *                 @OA\Property(
     *                     property="first_name",
     *                     description="User firstname",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="last_name",
     *                     description="User lastname",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     description="User email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     description="User password",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password_confirmation",
     *                     description="User password",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="address",
     *                     description="User main address",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="phone_number",
     *                     description="User main phone number",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="is_marketing",
     *                     description="User marketing preference",
     *                     type="boolean",
     *                     enum={true, false}
     *                 )
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *      ),
     *      @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *      ),
     *      @OA\Response(
     *         response=404,
     *         description="Page not found"
     *      ),
     *      @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity"
     *      ),
     *      @OA\Response(
     *         response=500,
     *         description="Internal Server error"
     *      )
     *  )
     */
    public function store(StoreUserRequest $request)
    {
        $newUser = User::create($request->validated());

        return $this->generalApiResponse(200, [$newUser]);
    }

    /**
     * Update an existing user.
     *
     * @OA\Put(
     *     path="/api/v1/user/edit",
     *     tags={"User"},
     *     summary="Update self account",
     *     operationId="update",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Input data properties",
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"first_name","last_name", "email", "password", "password_confirmation", "address", "phone_number"},
     *                 @OA\Property(
     *                     property="first_name",
     *                     description="User firstname",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="last_name",
     *                     description="User lastname",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     description="User email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     description="User password",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password_confirmation",
     *                     description="User password",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="address",
     *                     description="User main address",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="phone_number",
     *                     description="User main phone number",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="is_marketing",
     *                     description="User marketing preference",
     *                     type="boolean",
     *                     enum={true, false}
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server error"
     *     )
     * )
     */
    public function update(UpdateUserRequest $request)
    {
        $user = $this->guard()->user();

        $user->update($request->validated());

        return $this->generalApiResponse(200, [$user]);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    protected function guard()
    {
        return Auth::guard();
    }
}
