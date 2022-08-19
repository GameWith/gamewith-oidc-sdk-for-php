<?php
namespace GameWith\Oidc\Tests\Util;

use GameWith\Oidc\Exception\JsonErrorException;
use GameWith\Oidc\Util\Json;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Class JsonTest
 * @package GameWith\Oidc\Tests\Util
 */
class JsonTest extends TestCase
{
    /**
     * @throws JsonErrorException
     */
    public function testEncode()
    {
        $this->assertEquals(
            '["a","b","c"]',
            Json::encode(['a', 'b', 'c'])
        );
        $this->assertEquals(
            "[\n    \"\u3042\"\n]",
            Json::encode(
                ['あ'],
                JSON_PRETTY_PRINT
            )
        );
        $this->assertEquals(
            "[\n    \"あ\"\n]",
            Json::encode(
                ['あ'],
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            )
        );
        $this->assertEquals(
            "[\n    \"あ\"\n]",
            Json::encode(
                ['あ'],
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE,
                1
            )
        );
    }

    /**
     * @throws JsonErrorException
     */
    public function testEncodeFailure()
    {
        $data = [
            'a' => [
                'b'=> [
                    'c' => []
                ]
            ]
        ];
        $this->expectException(JsonErrorException::class);
        Json::encode($data, 0, 0);
    }

    /**
     * @throws JsonErrorException
     */
    public function testDecode()
    {
        $arr = ['name' => 'hoge'];
        $obj = new \stdClass();
        $obj->name = 'hoge';

        $this->assertEquals(
            $obj,
            Json::decode('{"name":"hoge"}')
        );
        $this->assertEquals(
            $obj,
            Json::decode('{"name":"hoge"}', false)
        );
        $this->assertEquals(
            $arr,
            Json::decode('{"name":"hoge"}', true)
        );
        // depth and options
        $this->assertEquals(
            ['a' => '123456789012345678901234567890'],
            Json::decode('{"a":123456789012345678901234567890}', true, 512, JSON_BIGINT_AS_STRING)
        );
    }

    /**
     * @dataProvider providerDecodeFailure
     * @param $params
     * @throws JsonErrorException
     */
    public function testDecodeFailure($params)
    {
        $this->expectException(JsonErrorException::class);
        Json::decode($params['json'], $params['assoc'] ?? false, $params['depth'] ?? 512, $params['options'] ?? 0);
    }

    public function providerDecodeFailure()
    {
        return [
            [
                [
                    'json' => '{"name": "hoge}'
                ],
            ],
            [
                [
                    'json'  => '{"name": "hoge"}',
                    'depth' => 1
                ],
            ]
        ];
    }
}
