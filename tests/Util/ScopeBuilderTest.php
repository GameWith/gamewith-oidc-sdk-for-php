<?php
namespace GameWith\Oidc\Tests\Util;

use GameWith\Oidc\Util\ScopeBuilder;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Class ScopeBuilderTest
 * @package GameWith\Oidc\Tests\Util
 */
class ScopeBuilderTest extends TestCase
{
    /**
     * @dataProvider providerMake
     * @param array $scopes
     */
    public function testMake(array $scopes)
    {
        $this->assertInstanceOf(
            ScopeBuilder::class,
            ScopeBuilder::make(...$scopes)
        );
    }

    public function providerMake()
    {
        return [
            [['openid']],
            [['profile']],
            [['openid', 'profile']],
        ];
    }

    /**
     * @dataProvider providerMakeFailure
     * @param array $scopes
     */
    public function testMakeFailure(array $scopes)
    {
        $this->expectException(\UnexpectedValueException::class);
        ScopeBuilder::make(...$scopes);
    }

    public function providerMakeFailure()
    {
        return [
            [['']],
            [['openid ']],
            [[' openid']],
            [['alert(1)']],
            [['&state=xxx']],
            [['?state=xxx']],
            [['openid&code_challenge=none']],
            [['openid', '']],
            [['openid', 'alert(1)']],
            [['openid', '&state=xxx']],
            [['openid', '?state=xxx']],
            [['openid', 'profile&code_challenge=none']],
        ];
    }

    public function testIsEmpty()
    {
        $sb = ScopeBuilder::make();
        $this->assertTrue($sb->isEmpty());
        $sb->add('openid');
        $this->assertFalse($sb->isEmpty());
    }

    /**
     * @dataProvider providerValidate
     * @param $scope
     * @param $expected
     */
    public function testValidate($scope, $expected)
    {
        $sb = ScopeBuilder::make();
        $this->assertEquals($expected, $sb->Validate($scope));
    }

    public function providerValidate()
    {
        return [
            ['', false],
            ['alert(1)', false],
            ['&state=xxx', false],
            ['?state=xxx', false],
            ['openid&code_challenge=none', false],
            ['openid profile', false],
            ['abc1', true],
            ['openid', true],
            ['profile', true],
            ['profile.name', true],
            ['profile-name', true],
            ['profile_name', true],
        ];
    }

    public function testExists()
    {
        {
            $sb = ScopeBuilder::make();
            $this->assertFalse($sb->exists('openid'));
            $this->assertFalse($sb->exists('profile'));
        }
        {
            $sb = ScopeBuilder::make();
            $sb->add('openid')->add('profile');
            $this->assertTrue($sb->exists('openid'));
            $this->assertTrue($sb->exists('profile'));
        }
        {
            $sb = ScopeBuilder::make();
            $sb->add('openid', 'profile');
            $this->assertTrue($sb->exists('openid'));
            $this->assertTrue($sb->exists('profile'));
        }
    }

    public function testBuild()
    {
        $sb = ScopeBuilder::make();
        $this->assertEquals('', $sb->build());
        $sb->add('openid');
        $this->assertEquals('openid', $sb->build());
        $sb->add('openid');
        $this->assertEquals('openid', $sb->build());
        $sb->add('profile');
        $this->assertEquals('openid profile', $sb->build());
        $sb->add('profile');
        $this->assertEquals('openid profile', $sb->build());
    }
}
