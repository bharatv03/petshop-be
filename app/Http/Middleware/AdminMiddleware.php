<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\Auth\UserHelper;
use App\Repositories\UserRepository;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userHelper = new UserHelper();
        $user = $userHelper->getAuthUser($this->userRepository);

        if ($user->is_admin) {
            return $next($request);
        }
        $response = [
            'success' => false,
            'error' => 'unauthorized',
            'data' => [],
            'errors' => [],
        ];
        return response()->json($response, HTTP_UNAUTHORIZED);
    }
}
