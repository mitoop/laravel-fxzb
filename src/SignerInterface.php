<?php

namespace Mitoop\Fxzb;

interface SignerInterface
{
    public function getSignatureFields(array $params, bool $attach = false): array;

    public function verify(array $params, string $signature): bool;
}
