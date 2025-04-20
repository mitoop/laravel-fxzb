<?php

namespace Mitoop\Fxzb;

use InvalidArgumentException;

class ClientManager
{
    protected array $clients = [];

    public function service(Service $service)
    {
        $service = strtolower($service->value);

        if (! isset($this->clients[$service])) {
            $appId = config('fxzb.app_id');
            $secret = config("fxzb.services.{$service}.secret");
            $baseUrl = config("fxzb.services.{$service}.base_url");
            $timeout = config('fxzb.http_timeout', 30);

            if (empty($appId) || empty($secret) || empty($baseUrl)) {
                throw new InvalidArgumentException("Invalid config for service [{$service}]");
            }

            $this->clients[$service] = new Client(rtrim($baseUrl, '/'), new Signer($appId, $secret), $timeout);
        }

        return $this->clients[$service];
    }
}
