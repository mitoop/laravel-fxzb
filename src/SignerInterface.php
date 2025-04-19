<?php

namespace Mitoop\Fxzb;

interface SignerInterface
{
    public function attach(array $params): array;

    public function verify(array $params, string $signature): bool;
}
