<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\Auth\UserHelper;
use App\Repositories\UserRepositoryInterface;

class AdminMiddleware
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
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
        $user = $userHelper->GetAuthUser($this->userRepository);

        if ($user->is_admin) {
            return $next($request);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
