<?php

namespace Mitoop\Fxzb;

class Signer implements SignerInterface
{
    public function __construct(protected string $appId, protected string $secret) {}

    public function getSignatureFields(array $params, bool $attach = false): array
    {
        $signatureFields = [
            'appId' => $this->appId,
            'timestamp' => (int) (microtime(true) * 1000),
            'nonce' => bin2hex(random_bytes(16)),
            'sign' => $this->makeSignature($params),
        ];

        if ($attach) {
            return array_merge($params, $signatureFields);
        }

        return $signatureFields;
    }

    public function verify(array $params, string $signature): bool
    {
        return $this->makeSignature($params) === $signature;
    }

    protected function makeSignature(array $params): string
    {
        $filtered = array_filter($params, fn ($v) => $v !== '' && $v !== null);

        $flattened = $this->flatten($filtered);

        ksort($flattened, SORT_STRING);

        $queryParts = [];
        foreach ($flattened as $key => $value) {
            $queryParts[] = $key.'='.$value;
        }

        $sign = implode('&', $queryParts);

        return md5($sign.$this->secret);
    }

    protected function flatten(array $params, string $prefix = ''): array
    {
        $result = [];

        foreach ($params as $key => $value) {
            if (is_numeric($key)) {
                $newKey = $prefix.'['.$key.']';
            } else {
                $newKey = $prefix === '' ? $key : $prefix.'.'.$key;
            }

            if (is_array($value)) {
                $result += $this->flatten($value, $newKey);
            } else {
                $result[$newKey] = $value;
            }
        }

        return $result;
    }
}
