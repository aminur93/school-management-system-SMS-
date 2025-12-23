<?php

namespace App\Http\Services\Api\V1\Auth;

use App\Helper\ImageUpload;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Nette\Utils\Image;
use Tymon\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AuthServiceImpl implements AuthService
{
    public function login(Request $request)
    {
        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $user = User::where($loginType, $request->login)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw new \DomainException('Invalid credentials');
        }

        // Access token (short lived)
        $accessToken = JWTAuth::claims([
            'type' => 'access'
        ])->fromUser($user);

        // Refresh token (long lived)
        $refreshToken = JWTAuth::claims([
            'type' => 'refresh'
        ])->setTTL(config('jwt.refresh_ttl'))
        ->fromUser($user);

        return $this->respondWithToken($accessToken, $refreshToken);
    }

    public function refreshToken(string $refreshToken)
    {
        try {
            $payload = JWTAuth::setToken($refreshToken)->getPayload();

            if ($payload->get('type') !== 'refresh') {
                throw new \DomainException('Invalid refresh token');
            }

            $userId = $payload->get('sub');
            $user = User::find($userId);

            if (!$user) {
                throw new \DomainException('User not found');
            }

            $newAccessToken = JWTAuth::claims([
                'type' => 'access'
            ])->fromUser($user);

            return $this->respondWithToken($newAccessToken, $refreshToken); 

        } catch (\Exception $e) {

            throw new \DomainException('Refresh token expired or invalid');
        }
    }

    public function logout() : bool
    {
        try {
            
            JWTAuth::invalidate(JWTAuth::getToken());

            if (Auth::check()) {
                
                activity('logout')
                    ->causedBy(Auth::user())
                    ->log('Logout successful');
            }

            return true;

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {

            throw new UnauthorizedHttpException(
                'jwt-auth',
                'Logout failed'
            );
        }
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token, $refreshToken) : array
    {

        // Prepare the response data
        $data = [
            'user' => Auth::user(),
            'access_token' => $token,
            'refresh_token' => $refreshToken,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60, // Token expiration time
            'refresh_expires_in' => config('jwt.refresh_ttl') * 60
        ];

        return $data;
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard();
    }

    public function register(Request $request)
    {
        DB::beginTransaction();

        try {
            
            $user = new User();

            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->user_type = $request->user_type;
            $user->password = Hash::make($request->password);

            if ($request->hasFile('image')) {

                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $request->file('image'),
                    'user-image'
                );

                // DB columns
                $user->image = $imagePath;
                $user->image_url = asset('storage/' . $imagePath);
            }

            $user->save();

            activity('Register')
                ->performedOn($user)
                ->causedBy($user)
                ->withProperties(['attributes' => $request->all()])
                ->log('Register Successful');

            DB::commit();

            return $user;

        } catch (\Throwable $th) {

            DB::rollBack();

            throw $th;
        }
    }
}