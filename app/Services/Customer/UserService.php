<?php
namespace App\Services\Customer;

use App\MicroApi\Items\UserItem;
use App\Shop\Addresses\Address;
use App\Shop\Orders\Order;
use Illuminate\Contracts\Auth\UserProvider;

/**
 * 在 UserService 中提供 MicroUserProvider 作为数据源（MicroUserProvider 底层调用 App\MicroApi\Services\UserService 方法基于微服务接口获取原始数据，这样一来，就屏蔽了底层实现细节，另外，也避免了与微服务接口参数的耦合，我们可以在 App\Services\Customer\UserService 方法中统一处理服务接口入参和出参格式，直接调用 App\MicroApi\Services\UserService 方法的话，如果接口入参和出参有调整，那所有涉及到对应接口调用的地方都要调整，可维护性很差）
 */
class UserService
{
    /**
     * @var UserProvider
     */
    protected $provider;

    public function __construct()
    {
        $this->provider = app('auth')->createUserProvider('micro_user');
    }

    /**
     * 根据用户 ID 获取用户信息
     */
    public function getById(int $id): UserItem
    {
        return $this->provider->retrieveById($id);
    }

    /**
     * 根据注册邮箱获取用户信息
     */
    public function getByEmail(string $email): UserItem
    {
        return $this->provider->retrieveByCredentials(['email' => $email]);
    }

    /**
     * 根据用户 ID 查询 orders 表获取对应分页订单信息
     */
    public function getPaginatedOrdersByUserId($uid, $perPage = 15, $columns = ['*'], $orderBy = 'id')
    {
        return Order::select($columns)->where('customer_id', $uid)->orderBy($orderBy, 'desc')->paginate($perPage);
    }

    /**
     * 根据用户 ID 获取查询 addresses 表对应地址信息
     */
    public function getAddressesByUserId($uid, $columns = ['*'], $orderBy = 'id')
    {
        return Address::select($columns)->where('customer_id', $uid)->orderBy($orderBy, 'desc')->get();
    }
}