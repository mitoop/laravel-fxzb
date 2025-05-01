<?php

namespace Mitoop\Fxzb;

use Closure;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Str;

class Client
{
    protected static ?Dispatcher $events = null;

    protected ?GuzzleClient $http = null;

    public function __construct(
        protected string $baseUrl,
        protected SignerInterface $signer,
        protected int $timeout = 30
    ) {}

    public static function setDispatcher(Dispatcher $dispatcher): void
    {
        static::$events = $dispatcher;
    }

    public static function requesting(Closure $callback): void
    {
        static::$events?->listen('mitoop.fxzb.requesting', $callback);
    }

    public static function requested(Closure $callback): void
    {
        static::$events?->listen('mitoop.fxzb.requested', $callback);
    }

    public function get($path, ?array $params = null, array $headers = []): Response
    {
        return $this->sendRequest('get', $path, [
            'headers' => $headers,
            'query' => $this->signer->getSignatureFields($params ?: [], true),
        ]);
    }

    public function post($path, ?array $params = null, array $headers = []): Response
    {
        return $this->sendRequest('post', $path, [
            'headers' => $headers,
            'json' => $params ?: null,
            'query' => $this->signer->getSignatureFields($params ?: []),
        ]);
    }

    protected function sendRequest(string $method, string $path, array $options): Response
    {
        $path = Str::start($path, '/');

        $this->fireEvent('requesting', [$method, $this->baseUrl.$path, $options]);

        $response = $this->getHttp()->$method($path, $options);

        $response = new Response($response);

        $this->fireEvent('requested', [$response]);

        return $response;
    }

    protected function getHttp(): GuzzleClient
    {
        return $this->http ??= new GuzzleClient([
            'base_uri' => $this->baseUrl,
            'verify' => true,
            'http_errors' => false,
            'timeout' => $this->timeout,
            'connect_timeout' => 3,
            'headers' => [
                'Content-Type' => 'application/json; charset=utf-8',
                'Accept' => 'application/json',
            ],
        ]);
    }

    protected function fireEvent($name, array $data = []): ?array
    {
        return static::$events?->dispatch('mitoop.fxzb.'.$name, $data);
    }
}
