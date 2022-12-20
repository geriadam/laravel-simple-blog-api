<?php

namespace App\Http\Controllers\Api;

use App\Traits\ResponseAPI;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Constants\ResponseMessages;
use App\Http\Resources\UserResource;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    use ResponseAPI;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::get();
        return $this->sendResponse(UserResource::collection($users), ResponseMessages::RESPONSE_API_INDEX, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $request->request->add(['password' => Hash::make($request->password ?? User::DEFAULT_PASS)]);
        $user = User::create($request->all());
        $user->assignRole($request->role);

        return $this->sendResponse(new UserResource($user), ResponseMessages::RESPONSE_API_CREATE, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return $this->sendResponse(new UserResource($user), ResponseMessages::RESPONSE_API_INDEX, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        DB::begintransaction();
        try {
            $data = $request->all();

            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                $data = Arr::except($data, ['password']);
            }

            $user->update($data);
            DB::table('model_has_roles')->where('model_id', $user->id)->delete();
            $user->assignRole($request->role);
            DB::commit();
            return $this->sendResponse(new UserResource($user), ResponseMessages::RESPONSE_API_UPDATE, Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError(ResponseMessages::RESPONSE_API_FAILED_UPDATE, $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return $this->sendResponse([], ResponseMessages::RESPONSE_API_DELETE, Response::HTTP_NO_CONTENT);
    }
}
