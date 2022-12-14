<?php

namespace GameWith\Oidc\Tests\Util;

use GameWith\Oidc\Util\Base64Url;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

class Base64UrlTest extends TestCase
{
    /**
     * @dataProvider providerEncode
     * @param $expected
     * @param $text
     */
    public function testEncode($expected, $text)
    {
        $actual = Base64Url::encode($text);
        $this->assertEquals($expected, $actual);
    }

    public function providerEncode()
    {
        return [
            ['OTEwZjIwZWExZThjNTRmZjc1MzI2OWNhNjhkOWJkNWFkM2RkN2FmNjA5ZjhmNzM5YjExYzAzOTQ4NmVmNWYyNg', '910f20ea1e8c54ff753269ca68d9bd5ad3dd7af609f8f739b11c039486ef5f26'],
            ['ZTIyZDdhMjhkNDA5NDA4NzNmNDU1MTMxY2MyMmI1MjllMzJmMmE2MjMyMjFjNDkyZjQwYzM2MzYwYzI0ZTkyZA', 'e22d7a28d40940873f455131cc22b529e32f2a623221c492f40c36360c24e92d'],
            ['NTg2YzY2MzAyZjE2MjY5ODRiMzY0NmQ1OWY1MGZhNzFhYjBjMDUwMTBjMmY1MjViMjQwYjM3NzJiZjQ3MjIwZQ', '586c66302f1626984b3646d59f50fa71ab0c05010c2f525b240b3772bf47220e'],
            ['MTljOWI0ZWRhNGY4YmE2NmI5YzgxODVhOTNiY2QyNjFlZTk0ZWM2NDVkNTRiNzU1YjM0MjlhMTU2ZDAzZTYyZg', '19c9b4eda4f8ba66b9c8185a93bcd261ee94ec645d54b755b3429a156d03e62f'],
            ['MjUzNTI5MjA0MDYzNTA5YmI4MjVhMDA5ZDFlNjA4MDNhMGY0ODQwYWNjYjAzYWQyMzY4NjdhNGUyYzZjMDVhZg', '253529204063509bb825a009d1e60803a0f4840accb03ad236867a4e2c6c05af'],
            ['YzJkZjY4MDlkOTQ4MDE4MDkzNzY0ZDE4ZDc5ODcyZGQ3ZTZkNWY3MjRjZjYwNmY5NzU1ZTE0Yzc1YWJhNjM4Zg', 'c2df6809d948018093764d18d79872dd7e6d5f724cf606f9755e14c75aba638f'],
            ['M2JiOGU3Njk4YWM1NmIwMjhlZjg0YzkwNDIzNmI2NWZmZWVhNzVlMTBkNDViNDI2OWM1NjgyYWRkMmUxMGJjYw', '3bb8e7698ac56b028ef84c904236b65ffeea75e10d45b4269c5682add2e10bcc'],
            ['OTVkNDQ2NWE5NjM5ZmZjZWQ3OWFjZTBlM2I1YjcwOTZlNWNiNmJhNGJhZDUyYzRkMGVlMzE5ODhkMTY5MDZmZA', '95d4465a9639ffced79ace0e3b5b7096e5cb6ba4bad52c4d0ee31988d16906fd'],
            ['Y2MxNGJkMGI5MjhhNzAwMThjYWNiMzliMmJiOTlmZmVhMjc0NTI4OTE2MWE0NDU2MThmYTc1YjdmOWI1ZjIyNw', 'cc14bd0b928a70018cacb39b2bb99ffea2745289161a445618fa75b7f9b5f227'],
            ['OTdkNDhhNTY4MjczYzNhODg3Y2U0ZDNmMTA2N2FlN2Y1NGFiZjliYmMwOTc1NWRiMjA3YzQ2YzVjNGNkYTgzNg', '97d48a568273c3a887ce4d3f1067ae7f54abf9bbc09755db207c46c5c4cda836'],
            ['MGNiMGVlMDQwNDE4YWZhYjM5NjIyZDk2NWVkNDU2OWZiZTNmZWM2M2M5ZGZiMzhhOGI0NjA3NTQ5MTY4MjExOA', '0cb0ee040418afab39622d965ed4569fbe3fec63c9dfb38a8b46075491682118'],
            ['Mjc1YzdkYTk2Mjk5Mjc5ZTZhNTZiYTg0ZDk4OGU2MmI0MGU4NGY3YjA1YjkwMmRiZTIzNzkxYzdhOGY0NTU1Yw', '275c7da96299279e6a56ba84d988e62b40e84f7b05b902dbe23791c7a8f4555c'],
            ['ZGY2NGUzYmQ1NGIxYjJjOWQ3ODcwYWNkNThlMTAzZDczMTIyNjhjNTNlOGVlNTQzMzIzODc3OTdmMzI1OTNhMw', 'df64e3bd54b1b2c9d7870acd58e103d7312268c53e8ee54332387797f32593a3'],
            ['ZDlmMGUwZjllZTk3ZTI4ZTAxYzQ3NjA0NDM3NmVlYjQ0MjU1YjFhYjg1ZjMxN2VmZmIyNmNmMTNjNzliYzY2Mw', 'd9f0e0f9ee97e28e01c476044376eeb44255b1ab85f317effb26cf13c79bc663'],
            ['MzYzOTI4NDMyMGI1NDM4N2E2NDY5ZTRmM2VmZDIyMDBiYTM3OWY2NTRkNDFiYTU0ODE5MDQ3M2Q0Njk2NzJiYg', '3639284320b54387a6469e4f3efd2200ba379f654d41ba548190473d469672bb'],
            ['YWE1NDM4ODgxZThhOTU5NDQ3ZDE3ODZjZjZkNzIzMWQ2MzJlYjQ2ZDFmNDg1ODc3YWE2NGQzY2Y3NDVjMTcxYQ', 'aa5438881e8a959447d1786cf6d7231d632eb46d1f485877aa64d3cf745c171a'],
            ['ZjlkNDZmNzJmNGFjMjI4MjllZTZkMjM5M2E4NTA5ZDJmYjhmMzc2NTM1MWQ5N2ZjNTA3NTFmMTZiZDY4NmQ1YQ', 'f9d46f72f4ac22829ee6d2393a8509d2fb8f3765351d97fc50751f16bd686d5a'],
            ['N2FKUWF-UmZiZ2xJbEpXOFFlMWFxVEsxUG1lSlhaME5SUUZpRzQtMjdFZGhzM2xGLnVtZDhXNC5iSUlCVlBFQ3VyZnNKclNSS0JOTWJ4WHR4dmVKTjRVODkwT0wzSDlIOUVsVUNFTA', '7aJQa~RfbglIlJW8Qe1aqTK1PmeJXZ0NRQFiG4-27Edhs3lF.umd8W4.bIIBVPECurfsJrSRKBNMbxXtxveJN4U890OL3H9H9ElUCEL'],
            ['dHNOd3FDTmpYUDhmdVdUMmppTVplUkV1UjdVbG55TktwMUJyVXE1d0sxcy1UUg', 'tsNwqCNjXP8fuWT2jiMZeREuR7UlnyNKp1BrUq5wK1s-TR'],
            ['VHZBaWlIRkptbHE0NXJ6R1IyXzUzdn5TY3oxZ1p2MFVnMEVvZ0ZQNEJhajdCcERlSmN5bkQuZ2V2SGpUUlNUV1ZveEVZSHpWM2V3NEJOQTh0eTh0MlRtblNhUmxfQXRkMThsTDdlUURJUy1XMTJwU1V3RjBnbklP', 'TvAiiHFJmlq45rzGR2_53v~Scz1gZv0Ug0EogFP4Baj7BpDeJcynD.gevHjTRSTWVoxEYHzV3ew4BNA8ty8t2TmnSaRl_Atd18lL7eQDIS-W12pSUwF0gnIO'],
            ['SmlOR25vTXpSaEJUSFpYQ1JJZDhSNXVkeTRWNFBGeEc5a2gxbWVfLXcyM0g2ODVNT3EyMw', 'JiNGnoMzRhBTHZXCRId8R5udy4V4PFxG9kh1me_-w23H685MOq23'],
            ['WGNhLS04U0FTSzd4SVlCMnphaWhqQWpWVE1xQ1dMbFlzYWJlQWN3NlBpMW5kYWwzM0Y4MzRwbE8uYi4', 'Xca--8SASK7xIYB2zaihjAjVTMqCWLlYsabeAcw6Pi1ndal33F834plO.b.'],
            ['c3AwUmI1Z1VJbFQ0Vkp0MVl-cDBxMXFScG41VlBDWV95RkticklrNEVmVDVNdmZqNTdfLmhXLUtHck50Ry1ZM0JtajZlNkZFZC5-MUowUzg3dW85X2V5bHd2Nnd-TW9G', 'sp0Rb5gUIlT4VJt1Y~p0q1qRpn5VPCY_yFKbrIk4EfT5Mvfj57_.hW-KGrNtG-Y3Bmj6e6FEd.~1J0S87uo9_eylwv6w~MoF'],
            ['M0cwSVR1QXB1cFo1dUZLWVZ3dGlwOGdrOVUzWDhqNWN2bWJqNjhZcE5RRXFRfkVLdDE5X3lZWjFWbm0tQkppVE5tRXYtSWhERlhrNHd0MA', '3G0ITuApupZ5uFKYVwtip8gk9U3X8j5cvmbj68YpNQEqQ~EKt19_yYZ1Vnm-BJiTNmEv-IhDFXk4wt0'],
            ['U1JoX1d5U2VhZFhWQ2NXYlJzMGF-cHNFb0swTGw4RzNQZk5fYnFNWWhzcVJubks', 'SRh_WySeadXVCcWbRs0a~psEoK0Ll8G3PfN_bqMYhsqRnnK'],
            ['RkZMOWpNaDMxdXJka0xwdG1EeFZqVXdqd29TNDkyUks2US5fWUhTSkM4cH5KNmhKeXNZWWhETkFabkVCRTdRd2R6V0FYeC01eC5DTWhGR0syMUlUZmFYNE9tanFpOXI3dE5MLUJBMlVVVEl0VkliSXZ0ZWFMOXA2Qm1TWkFR', 'FFL9jMh31urdkLptmDxVjUwjwoS492RK6Q._YHSJC8p~J6hJysYYhDNAZnEBE7QwdzWAXx-5x.CMhFGK21ITfaX4Omjqi9r7tNL-BA2UUTItVIbIvteaL9p6BmSZAQ']
        ];
    }

    /**
     * @dataProvider providerDecode
     * @param $expected
     * @param $text
     */
    public function testDecode($expected, $text)
    {
        $actual = Base64Url::decode($text);
        $this->assertEquals($expected, $actual);
    }

    public function providerDecode()
    {
        return [
            ['910f20ea1e8c54ff753269ca68d9bd5ad3dd7af609f8f739b11c039486ef5f26', 'OTEwZjIwZWExZThjNTRmZjc1MzI2OWNhNjhkOWJkNWFkM2RkN2FmNjA5ZjhmNzM5YjExYzAzOTQ4NmVmNWYyNg'],
            ['e22d7a28d40940873f455131cc22b529e32f2a623221c492f40c36360c24e92d', 'ZTIyZDdhMjhkNDA5NDA4NzNmNDU1MTMxY2MyMmI1MjllMzJmMmE2MjMyMjFjNDkyZjQwYzM2MzYwYzI0ZTkyZA'],
            ['586c66302f1626984b3646d59f50fa71ab0c05010c2f525b240b3772bf47220e', 'NTg2YzY2MzAyZjE2MjY5ODRiMzY0NmQ1OWY1MGZhNzFhYjBjMDUwMTBjMmY1MjViMjQwYjM3NzJiZjQ3MjIwZQ'],
            ['19c9b4eda4f8ba66b9c8185a93bcd261ee94ec645d54b755b3429a156d03e62f', 'MTljOWI0ZWRhNGY4YmE2NmI5YzgxODVhOTNiY2QyNjFlZTk0ZWM2NDVkNTRiNzU1YjM0MjlhMTU2ZDAzZTYyZg'],
            ['253529204063509bb825a009d1e60803a0f4840accb03ad236867a4e2c6c05af', 'MjUzNTI5MjA0MDYzNTA5YmI4MjVhMDA5ZDFlNjA4MDNhMGY0ODQwYWNjYjAzYWQyMzY4NjdhNGUyYzZjMDVhZg'],
            ['c2df6809d948018093764d18d79872dd7e6d5f724cf606f9755e14c75aba638f', 'YzJkZjY4MDlkOTQ4MDE4MDkzNzY0ZDE4ZDc5ODcyZGQ3ZTZkNWY3MjRjZjYwNmY5NzU1ZTE0Yzc1YWJhNjM4Zg'],
            ['3bb8e7698ac56b028ef84c904236b65ffeea75e10d45b4269c5682add2e10bcc', 'M2JiOGU3Njk4YWM1NmIwMjhlZjg0YzkwNDIzNmI2NWZmZWVhNzVlMTBkNDViNDI2OWM1NjgyYWRkMmUxMGJjYw'],
            ['95d4465a9639ffced79ace0e3b5b7096e5cb6ba4bad52c4d0ee31988d16906fd', 'OTVkNDQ2NWE5NjM5ZmZjZWQ3OWFjZTBlM2I1YjcwOTZlNWNiNmJhNGJhZDUyYzRkMGVlMzE5ODhkMTY5MDZmZA'],
            ['cc14bd0b928a70018cacb39b2bb99ffea2745289161a445618fa75b7f9b5f227', 'Y2MxNGJkMGI5MjhhNzAwMThjYWNiMzliMmJiOTlmZmVhMjc0NTI4OTE2MWE0NDU2MThmYTc1YjdmOWI1ZjIyNw'],
            ['97d48a568273c3a887ce4d3f1067ae7f54abf9bbc09755db207c46c5c4cda836', 'OTdkNDhhNTY4MjczYzNhODg3Y2U0ZDNmMTA2N2FlN2Y1NGFiZjliYmMwOTc1NWRiMjA3YzQ2YzVjNGNkYTgzNg'],
            ['0cb0ee040418afab39622d965ed4569fbe3fec63c9dfb38a8b46075491682118', 'MGNiMGVlMDQwNDE4YWZhYjM5NjIyZDk2NWVkNDU2OWZiZTNmZWM2M2M5ZGZiMzhhOGI0NjA3NTQ5MTY4MjExOA'],
            ['275c7da96299279e6a56ba84d988e62b40e84f7b05b902dbe23791c7a8f4555c', 'Mjc1YzdkYTk2Mjk5Mjc5ZTZhNTZiYTg0ZDk4OGU2MmI0MGU4NGY3YjA1YjkwMmRiZTIzNzkxYzdhOGY0NTU1Yw'],
            ['df64e3bd54b1b2c9d7870acd58e103d7312268c53e8ee54332387797f32593a3', 'ZGY2NGUzYmQ1NGIxYjJjOWQ3ODcwYWNkNThlMTAzZDczMTIyNjhjNTNlOGVlNTQzMzIzODc3OTdmMzI1OTNhMw'],
            ['d9f0e0f9ee97e28e01c476044376eeb44255b1ab85f317effb26cf13c79bc663', 'ZDlmMGUwZjllZTk3ZTI4ZTAxYzQ3NjA0NDM3NmVlYjQ0MjU1YjFhYjg1ZjMxN2VmZmIyNmNmMTNjNzliYzY2Mw'],
            ['3639284320b54387a6469e4f3efd2200ba379f654d41ba548190473d469672bb', 'MzYzOTI4NDMyMGI1NDM4N2E2NDY5ZTRmM2VmZDIyMDBiYTM3OWY2NTRkNDFiYTU0ODE5MDQ3M2Q0Njk2NzJiYg'],
            ['aa5438881e8a959447d1786cf6d7231d632eb46d1f485877aa64d3cf745c171a', 'YWE1NDM4ODgxZThhOTU5NDQ3ZDE3ODZjZjZkNzIzMWQ2MzJlYjQ2ZDFmNDg1ODc3YWE2NGQzY2Y3NDVjMTcxYQ'],
            ['f9d46f72f4ac22829ee6d2393a8509d2fb8f3765351d97fc50751f16bd686d5a', 'ZjlkNDZmNzJmNGFjMjI4MjllZTZkMjM5M2E4NTA5ZDJmYjhmMzc2NTM1MWQ5N2ZjNTA3NTFmMTZiZDY4NmQ1YQ'],
            ['7aJQa~RfbglIlJW8Qe1aqTK1PmeJXZ0NRQFiG4-27Edhs3lF.umd8W4.bIIBVPECurfsJrSRKBNMbxXtxveJN4U890OL3H9H9ElUCEL', 'N2FKUWF-UmZiZ2xJbEpXOFFlMWFxVEsxUG1lSlhaME5SUUZpRzQtMjdFZGhzM2xGLnVtZDhXNC5iSUlCVlBFQ3VyZnNKclNSS0JOTWJ4WHR4dmVKTjRVODkwT0wzSDlIOUVsVUNFTA'],
            ['tsNwqCNjXP8fuWT2jiMZeREuR7UlnyNKp1BrUq5wK1s-TR', 'dHNOd3FDTmpYUDhmdVdUMmppTVplUkV1UjdVbG55TktwMUJyVXE1d0sxcy1UUg'],
            ['TvAiiHFJmlq45rzGR2_53v~Scz1gZv0Ug0EogFP4Baj7BpDeJcynD.gevHjTRSTWVoxEYHzV3ew4BNA8ty8t2TmnSaRl_Atd18lL7eQDIS-W12pSUwF0gnIO', 'VHZBaWlIRkptbHE0NXJ6R1IyXzUzdn5TY3oxZ1p2MFVnMEVvZ0ZQNEJhajdCcERlSmN5bkQuZ2V2SGpUUlNUV1ZveEVZSHpWM2V3NEJOQTh0eTh0MlRtblNhUmxfQXRkMThsTDdlUURJUy1XMTJwU1V3RjBnbklP'],
            ['JiNGnoMzRhBTHZXCRId8R5udy4V4PFxG9kh1me_-w23H685MOq23', 'SmlOR25vTXpSaEJUSFpYQ1JJZDhSNXVkeTRWNFBGeEc5a2gxbWVfLXcyM0g2ODVNT3EyMw'],
            ['Xca--8SASK7xIYB2zaihjAjVTMqCWLlYsabeAcw6Pi1ndal33F834plO.b.', 'WGNhLS04U0FTSzd4SVlCMnphaWhqQWpWVE1xQ1dMbFlzYWJlQWN3NlBpMW5kYWwzM0Y4MzRwbE8uYi4'],
            ['sp0Rb5gUIlT4VJt1Y~p0q1qRpn5VPCY_yFKbrIk4EfT5Mvfj57_.hW-KGrNtG-Y3Bmj6e6FEd.~1J0S87uo9_eylwv6w~MoF', 'c3AwUmI1Z1VJbFQ0Vkp0MVl-cDBxMXFScG41VlBDWV95RkticklrNEVmVDVNdmZqNTdfLmhXLUtHck50Ry1ZM0JtajZlNkZFZC5-MUowUzg3dW85X2V5bHd2Nnd-TW9G'],
            ['3G0ITuApupZ5uFKYVwtip8gk9U3X8j5cvmbj68YpNQEqQ~EKt19_yYZ1Vnm-BJiTNmEv-IhDFXk4wt0', 'M0cwSVR1QXB1cFo1dUZLWVZ3dGlwOGdrOVUzWDhqNWN2bWJqNjhZcE5RRXFRfkVLdDE5X3lZWjFWbm0tQkppVE5tRXYtSWhERlhrNHd0MA'],
            ['SRh_WySeadXVCcWbRs0a~psEoK0Ll8G3PfN_bqMYhsqRnnK', 'U1JoX1d5U2VhZFhWQ2NXYlJzMGF-cHNFb0swTGw4RzNQZk5fYnFNWWhzcVJubks'],
            ['FFL9jMh31urdkLptmDxVjUwjwoS492RK6Q._YHSJC8p~J6hJysYYhDNAZnEBE7QwdzWAXx-5x.CMhFGK21ITfaX4Omjqi9r7tNL-BA2UUTItVIbIvteaL9p6BmSZAQ', 'RkZMOWpNaDMxdXJka0xwdG1EeFZqVXdqd29TNDkyUks2US5fWUhTSkM4cH5KNmhKeXNZWWhETkFabkVCRTdRd2R6V0FYeC01eC5DTWhGR0syMUlUZmFYNE9tanFpOXI3dE5MLUJBMlVVVEl0VkliSXZ0ZWFMOXA2Qm1TWkFR']
        ];
    }
}
