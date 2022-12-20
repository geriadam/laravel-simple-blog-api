<?php

namespace App\Http\Controllers\API;

use App\Traits\ResponseAPI;
use App\Http\Controllers\Controller;
use App\Http\Requests\SignupRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Constants\ResponseMessages;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    use ResponseAPI;

    public function register(SignupRequest $request)
    {
        DB::begintransaction();

        try {
            $request->request->add(['password' => Hash::make($request->password)]);
            $user = User::create($request->all());
            $user->assignRole(User::USER_ROLE_WRITER);
            DB::commit();
            return $this->sendResponse($user, ResponseMessages::RESPONSE_API_CREATE, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError(ResponseMessages::RESPONSE_API_FAILED_CREATE, $e->getMessage());
        }
    }

    public function login(LoginRequest $request)
    {
        $credentials = request(['email', 'password']);

        if (!Auth::attempt($credentials)) {
            return $this->sendError("Login Failed", [], Response::HTTP_UNAUTHORIZED);
        }

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        if ($request->remember_me) {
            $token->expires_at = Carbon::now()->addWeeks(1);
        }

        $token->save();

        $data = [
            'data' => $user->toArray(),
            'role' => $user->roles,
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString()
        ];

        return $this->sendResponse($data, "Login Successfully", Response::HTTP_OK);
    }

    public function logout(Request $request)
    {
        DB::begintransaction();

        try {
            $token = $request->user()->token();
            $token->revoke();
            DB::commit();
            return $this->sendResponse(null, "Logout Successfully", Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError("Logout Failed", $e->getMessage());
        }
    }

    public function user(Request $request)
    {
        $user = auth('api')->user();

        $data = [
            'data' => $user->toArray(),
            'role' => $user->roles,
        ];

        return $this->sendResponse($data, ResponseMessages::RESPONSE_API_INDEX, Response::HTTP_OK);
    }

}
