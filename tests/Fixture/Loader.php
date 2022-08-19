<?php

namespace GameWith\Oidc\Tests\Fixture;

use phpseclib3\Crypt\PublicKeyLoader;

class Loader
{
    public static function load(string $path)
    {
        return file_get_contents(sprintf("%s/assets/%s", __DIR__, $path));
    }

    public static function loadJson(string $path): array
    {
        $json = self::load($path);
        return json_decode($json, true);
    }

    public static function loadPublicKey(string $path)
    {
        return PublicKeyLoader::load(self::load($path));
    }
}
