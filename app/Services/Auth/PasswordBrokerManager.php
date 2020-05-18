<?php
namespace App\Services\Auth;

use Illuminate\Auth\Passwords\PasswordBrokerManager as BasePasswordBrokerManager;

/**
*
* 为了覆盖默认的 PasswordBrokerManager，我们在 app/Providers 目录下新增 PasswordResetServiceProvider.php，通过自定义的密码重置服务提供者来注册 PasswordBrokerManager 实例到全局容器
*
*/
class PasswordBrokerManager extends BasePasswordBrokerManager
{
    /**
     * Create a token repository instance based on the given configuration.
     *
     * @param  array  $config
     * @return \Illuminate\Auth\Passwords\TokenRepositoryInterface
     */
    protected function createTokenRepository(array $config)
    {
        return new ServiceTokenRepository();
    }

    /**
     * Resolve the given broker.
     *
     * @param  string  $name
     * @return PasswordBroker
     *
     * @throws InvalidArgumentException
     */
    protected function resolve($name)
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("密码重置器 [{$name}] 未定义");
        }

        return new PasswordBroker(
            $this->createTokenRepository($config),
            $this->app['auth']->createUserProvider($config['provider'] ?? null)
        );
    }
}