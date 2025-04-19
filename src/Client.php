<?php

namespace Mitoop\Fxzb;

use Closure;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Client
{
    protected static ?Dispatcher $events = null;

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

    public function get($path, array $params = [], array $options = []): Response
    {
        return $this->sendRequest('get', $path, $params, $options);
    }

    public function post($path, array $params = [], array $options = []): Response
    {
        return $this->sendRequest('post', $path, $params, $options);
    }

    protected function sendRequest(string $method, string $path, array $params, array $options): Response
    {
        $attach = $this->signer->attach($params);
        $path = Str::start($path, '/');

        $this->fireEvent('requesting', [
            $method,
            $this->baseUrl.$path.'?'.http_build_query($attach),
            $params,
            $options,
        ]);

        $response = Http::baseUrl($this->baseUrl)
            ->timeout($this->timeout)
            ->withOptions(array_merge($options, [
                'query' => $attach,
            ]))
            ->$method($path, strtolower($method) === 'get' ? null : ($params ?: null));

        $this->fireEvent('requested', [$response]);

        return new Response($response);
    }

    protected function fireEvent($name, array $data = []): ?array
    {
        return static::$events?->dispatch('mitoop.fxzb.'.$name, $data);
    }
}
