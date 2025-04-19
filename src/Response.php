<?php

namespace Mitoop\Fxzb;

use Illuminate\Http\Client\Response as IlluminateResponse;

class Response
{
    public function __construct(protected IlluminateResponse $response) {}

    public function ok(): bool
    {
        if (! $this->response->successful()) {
            return false;
        }

        $data = $this->response->json();

        if (isset($data['code']) && isset($data['message'])) {
            return false;
        }

        return true;
    }

    public function __call($method, $parameters): mixed
    {
        return $this->response->$method(...$parameters);
    }
}
