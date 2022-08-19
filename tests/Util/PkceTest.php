<?php
namespace GameWith\Oidc\Tests\Util;

use GameWith\Oidc\Util\Pkce;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Class PkceTest
 * @package GameWith\Oidc\Tests\Util
 */
class PkceTest extends TestCase
{
    /**
     * @dataProvider providerCreateCodeChallenge
     * @param $codeVerifier
     * @param $expected
     * @throws \Exception
     */
    public function testCreateCodeChallenge($codeVerifier, $expected)
    {
        $act = Pkce::createCodeChallenge($codeVerifier);
        $this->assertEquals($expected, $act);
    }

    /**
     * @return array
     */
    public function providerCreateCodeChallenge()
    {
        return [
            [
                'a',
                'ypeBEsobvcr6wjGzmiPcTaeG7_gUfE5yuYB3ha_uSLs'
            ],
            [
                'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
                'ZtNPunH49FD35FWYhT5Tv8I7vRKQJ8uxMaL0_9eHjNA'
            ],
            [
                'pFSF1OPCTq19v8QWpnhlxfZ_~dpIXJsltCCv9b976WQYWeFI-UJujAXXh3C6ipK_XRbPTrbw9nklJH-o~nlczH_-k2GvGRYGaYKLBsMCe4jJZe',
                'zqv_jcQ66dct1LUSU9tQDwb6saAhfT5l8oiJUR5_NYI'
            ],
            [
                '4nQ8D.viLbU_WWgze7r7QRmgbAiKHeR0c20JqWEZ2y6zMk0o_5pMX~8Ag0S4JfsnmjEZnf9c0gFrpQJ9jr2TGBke5nEWApW~BqB',
                'BtutP05cSEZXhjeBAkU8E1DGBHUkjL0bjy0F0qlYQM8'
            ],
            [
                'pvPpQqXTjgjEkJly7uCfBMCt-4QLA_FgB4Bo3IGcesqM_h5VTqeWmR_L2ikt3mLQjCZMPXMhWSUPS27X5bNnxclf7cN2RZRsE5fvt1GIw8nh',
                '5hzzcD7XOGvHBfg2as9OH9SlLH4iqFOb66V8c_QbP5A'
            ],
            [
                'XycrrNCOdFYnBKIuiJrKzeXK3Eq2G3tDHCGVER7iY_Qiuub7KrxReevcRRPpiRPYLCGrrwiGwvKZxA7hAPJzsfKc50JxtKS4rr3iQt',
                'K98K_UW7YIb2czh5KIZSzKzquQHJw1u89_XtNP-moas'
            ],
            [
                'uNo5z2Mycjv2uPRDSmgVts2LR01WqWHVc~Iq-6qgEFP6~kD1QhLCD02QlXmsSGIE6HxY5S5~-mqJSOTPJ5k4pTPQL_vSMNakT.DB9jWL2Qolga',
                'GS_BOYqu4S9mRBfh4rbGFmr2WHgg-K5Bsqq2RL1s4Mw'
            ],
            [
                'aLucxKpXwwMJrcgnbXIm9bG4p5Bhg7Kr91Nxg4R.h0qupCLcnNyLJsJyuhQL44w48SPOzH_3bRg-YZMo1i4',
                's2ck_fgZHfmmt5siu-5w-3Hi4GsMGjQevKkwxzV2exM'
            ],
            [
                'gFlHiS9wP6Aw8UW2yI~G~~RIB1FOX5bTtzGU17fXVSw963Ju1gCONjUe2TmDXxF~yBISQyScX',
                'm9Z65iq8Llg544s3Se4VxXw2kdltpHNz3fn-os9C710'
            ],
            [
                's30rY4Xz2RvcpE_H~V2i2lE.8tGPOR~bt7qXjpEokoYBwoXU4VZTRw~FHjPANJuhN08yc9XoEAKGcn4cxy5mFr3KOQVExHREY.HR8c~j9',
                'uqi5ZpipMk-FOkqcrxSpUIiYNnV44iADimcUZeuYmj8'
            ],
            [
                'l8ObQ6wuRmQ3r0-pbT5PDkvMIok_MYa~-6l7lZaw9nP6RTu3J0_JQj',
                'nET9uEiSQya-SRCVw5JYC6MlaNHkOyiOCefB3sB-ZTU'
            ],
            [
                'eXES8HWOAy5TeWv-HaEfnbbw~k_7xXHqffia1YZ4c_h7.UF.bTuQ1-FkG0cew5jc0X',
                'D_TR0xKO9bZGwN1Y_ki3x1vvqkjnl43rzkVmPRhPIYk'
            ]
        ];
    }
}
