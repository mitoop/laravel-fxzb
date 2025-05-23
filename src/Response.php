<?php

namespace Mitoop\Fxzb;

use Illuminate\Http\Client\Response as IlluminateResponse;

class Response extends IlluminateResponse
{
    public function ok(): bool
    {
        if (! $this->successful()) {
            return false;
        }

        $data = $this->json();

        if (isset($data['code']) && isset($data['message'])) {
            return false;
        }

        if (isset($data['errCode']) && (int) $data['errCode'] !== 0) {
            return false;
        }

        return true;
    }
}
