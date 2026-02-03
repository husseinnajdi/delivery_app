<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use App\Models\User;

class JwtMiddleware
{
    public function handle(Request $request,Closure $next){
        $header=$request->header('Authorization');
        if(!$header ||!str_starts_with($header,'Bearer ')){
            return response()->json(['error'=>'Token not provided'],401);
        }
        $token=str_replace('Bearer ','',$header);

        try{
            $decoded=JWT::decode($token,new key(config('jwt.key'),'HS256'));
            $user_id=$decoded->id;
            $user=User::find($user_id);

            if(!$user){
                return response()->json(['error'=>'User not found'],401);
            }
            $request->merge(['auth_user'=>$user]);
        }catch(Exception $e){
            return response()->json(['error'=>'Invalid token: '.$e->getMessage()],401);
        }
        return $next($request);
    }
}