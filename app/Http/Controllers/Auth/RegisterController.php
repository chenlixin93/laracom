<?php

namespace App\Http\Controllers\Auth;

use App\MicroApi\Exceptions\RpcException;
use App\MicroApi\Items\UserItem;
use App\MicroApi\Services\UserService;
use App\Http\Controllers\Controller;
use App\Shop\Customers\Requests\RegisterCustomerRequest;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{    
    use RegistersUsers;

    protected $redirectTo = '/accounts';

    private $userService;

    /**
     * Create a new controller instance.
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->middleware('guest');
        $this->userService = $userService;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return UserItem
     * @throws RpcException
     */
    protected function create(array $data)
    {
        return $this->userService->create($data);
    }

    /**
     * @param RegisterCustomerRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws RpcException
     */
    public function register(RegisterCustomerRequest $request)
    {
        $data = $request->except('_method', '_token');
        $user = $this->create($data);
        $token = $this->userService->auth($data);  // 获取 Token
        session([md5($token) => $user]);  // 存储用户信息

        return redirect()->route('user.profile')->cookie('jwt-token', $token);
    }
}