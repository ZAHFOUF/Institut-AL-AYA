<?php

// src/Service/Encryptor.php

namespace AlAya\Common\Service;

class Encryptor
{
    private string $key;
    private string $cipher = 'AES-256-CBC';

    public function __construct(string $appSecret)
    {
        // Une clé de 32 caractères pour AES-256
        $this->key = substr(hash('sha256', $appSecret), 0, 32);
    }

    public function encrypt(string $data): string
    {
        $iv = openssl_random_pseudo_bytes(16);
        $encrypted = openssl_encrypt($data, $this->cipher, $this->key, 0, $iv);

        return base64_encode($iv . $encrypted);
    }

    public function decrypt(string $encoded): string
    {
        $data = base64_decode($encoded);
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);

        return openssl_decrypt($encrypted, $this->cipher, $this->key, 0, $iv);
    }
}
