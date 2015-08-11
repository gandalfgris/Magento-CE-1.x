<?php
require_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'CurlStub.php';

class SAI_CurlStubTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var SAI_CurlStub
     */
    protected $_curlStub;

    const DEFAULT_RESPONSE = 'default response';
    const DEFAULT_ERRORCODE = CURLE_COULDNT_RESOLVE_HOST;
    const DEFAULT_ERRORMESSAGE = 'CURLE_COULDNT_RESOLVE_HOST';

    const RETURN_RESPONSE = 1;
    const RETURN_ERRORCODE = 2;
    const RETURN_ERRORMESSAGE = 3;
    const RETURN_INFO = 4;


    public function setUp()
    {
        $this->_curlStub = new SAI_CurlStub();
        $this->_curlStub->setResponse(self::DEFAULT_RESPONSE);
        $this->_curlStub->setErrorCode(self::DEFAULT_ERRORCODE);
    }

    public function testSetResponse()
    {
        $actualResponse = $this->_getResponseFromCurl();

        $this->assertEquals(self::DEFAULT_RESPONSE, $actualResponse);
    }

    public function testReturnTransfer()
    {
        $curl = $this->_curlStub;

        $ch = $curl->curl_init();
        $curl->curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $actualResponse = $curl->curl_exec($ch);
        $curl->curl_close($ch);

        $this->assertEquals(self::DEFAULT_RESPONSE, $actualResponse);
    }

    public function testSetResponseForSpecificUrl()
    {
        $curl = $this->_curlStub;

        $expectedResponse = 'page found';
        $url = 'http://www.google.com';
        $requiredOptions = array(
            CURLOPT_URL => $url
        );
        $curl->setResponse($expectedResponse, $requiredOptions);

        // Setting no URL should give default response
        $actualResponse = $this->_getResponseFromCurl();

        $this->assertEquals(self::DEFAULT_RESPONSE, $actualResponse);

        // Setting correct URL should give set up response
        $actualResponse = $this->_getResponseFromCurl($url);

        $this->assertEquals($expectedResponse, $actualResponse);
    }

    public function testSetResponsesForMultipleUrls()
    {
        $curl = $this->_curlStub;

        $expectedResponse1 = 'google page found';
        $url1 = 'http://www.google.com';
        $expectedResponse2 = 'bing page found';
        $url2 = 'http://www.bing.com';

        $requiredOptions1 = array(
            CURLOPT_URL => $url1
        );
        $curl->setResponse($expectedResponse1, $requiredOptions1);
        $requiredOptions2 = array(
            CURLOPT_URL => $url2
        );
        $curl->setResponse($expectedResponse2, $requiredOptions2);

        // Setting wrong URL should give default response
        $actualResponse = $this->_getResponseFromCurl('http://www.yahoo.com');

        $this->assertEquals(self::DEFAULT_RESPONSE, $actualResponse);

        // Setting correct URLs should give corresponding responses
        $actualResponse = $this->_getResponseFromCurl($url1);

        $this->assertEquals($expectedResponse1, $actualResponse);

        $actualResponse = $this->_getResponseFromCurl($url2);

        $this->assertEquals($expectedResponse2, $actualResponse);
    }

    public function testSetResponsesForMultipleOptions()
    {
        $curl = $this->_curlStub;

        $expectedResponse1 = 'google default page found';
        $url1 = 'http://www.google.com';
        $expectedResponse2 = 'google page for chrome found';
        $url2 = $url1;
        $userAgent2 = 'Chrome/22.0.1207.1';
        $expectedResponse3 = 'google page for safari found';
        $url3 = $url1;
        $userAgent3 = 'Safari/537.1';
        $expectedResponse4 = 'google page for chrome from mail site found';
        $url4 = $url1;
        $userAgent4 = $userAgent2;
        $referer4 = 'http://mail.google.com';

        $requiredOptions1 = array(
            CURLOPT_URL => $url1
        );
        $curl->setResponse($expectedResponse1, $requiredOptions1);
        $requiredOptions2 = array(
            CURLOPT_URL => $url2,
            CURLOPT_USERAGENT => $userAgent2
        );
        $curl->setResponse($expectedResponse2, $requiredOptions2);
        $requiredOptions3 = array(
            CURLOPT_URL => $url3,
            CURLOPT_USERAGENT => $userAgent3
        );
        $curl->setResponse($expectedResponse3, $requiredOptions3);
        $requiredOptions4 = array(
            CURLOPT_URL => $url4,
            CURLOPT_USERAGENT => $userAgent4,
            CURLOPT_REFERER => $referer4
        );
        $curl->setResponse($expectedResponse4, $requiredOptions4);

        $actualResponse = $this->_getResponseFromCurl($url1);

        $this->assertEquals($expectedResponse1, $actualResponse);

        $actualResponse = $this->_getResponseFromCurl($url2, $requiredOptions2);

        $this->assertEquals($expectedResponse2, $actualResponse);

        $actualResponse = $this->_getResponseFromCurl($url3, $requiredOptions3);

        $this->assertEquals($expectedResponse3, $actualResponse);

        $actualResponse = $this->_getResponseFromCurl($url4, $requiredOptions4);

        $this->assertEquals($expectedResponse4, $actualResponse);

        $tooSpecificOptions = array(
            CURLOPT_URL => $url1,
            CURLOPT_USERAGENT => "Mozilla/5.0 (Windows NT 6.1; WOW64)"
        );

        $actualResponse = $this->_getResponseFromCurl($url1, $tooSpecificOptions);

        $this->assertEquals($expectedResponse1, $actualResponse);

        $unmatchedOptions = array(
            CURLOPT_REFERER => $referer4
        );

        $actualResponse = $this->_getResponseFromCurl(null, $unmatchedOptions);

        $this->assertEquals(self::DEFAULT_RESPONSE, $actualResponse);

        $actualResponse = $this->_getResponseFromCurl('http://www.yahoo.com', $unmatchedOptions);

        $this->assertEquals(self::DEFAULT_RESPONSE, $actualResponse);
    }

    public function testSetErrorCode()
    {
        $curl = $this->_curlStub;

        $expectedErrorCode1 = CURLE_UNSUPPORTED_PROTOCOL;
        $url1 = 'svn://www.google.com';
        $expectedErrorCode2 = CURLE_OK;
        $url2 = 'http://www.google.com';

        $requiredOptions1 = array(
            CURLOPT_URL => $url1
        );
        $curl->setErrorCode($expectedErrorCode1, $requiredOptions1);
        $requiredOptions2 = array(
            CURLOPT_URL => $url2
        );
        $curl->setErrorCode($expectedErrorCode2, $requiredOptions2);

        // Setting wrong URL should give default error code
        $actualErrorCode = $this->_getErrorCodeFromCurl('http://doesnotexist.google.com');

        $this->assertEquals(self::DEFAULT_ERRORCODE, $actualErrorCode);

        // Setting correct URLs should give corresponding responses
        $actualErrorCode = $this->_getErrorCodeFromCurl($url1);

        $this->assertEquals($expectedErrorCode1, $actualErrorCode);

        $actualErrorCode = $this->_getErrorCodeFromCurl($url2);

        $this->assertEquals($expectedErrorCode2, $actualErrorCode);
    }

    public function testGetErrorMessage()
    {
        $curl = $this->_curlStub;

        $expectedErrorCode1 = CURLE_UNSUPPORTED_PROTOCOL;
        $expectedErrorMessage1 = 'CURLE_UNSUPPORTED_PROTOCOL';
        $url1 = 'svn://www.google.com';
        $expectedErrorCode2 = CURLE_OK;
        $expectedErrorMessage2 = 'CURLE_OK';
        $url2 = 'http://www.google.com';

        $requiredOptions1 = array(
            CURLOPT_URL => $url1
        );
        $curl->setErrorCode($expectedErrorCode1, $requiredOptions1);
        $requiredOptions2 = array(
            CURLOPT_URL => $url2
        );
        $curl->setErrorCode($expectedErrorCode2, $requiredOptions2);

        // Setting wrong URL should give default error code
        $actualErrorMessage = $this->_getErrorMessageFromCurl('http://doesnotexist.google.com');

        $this->assertEquals(self::DEFAULT_ERRORMESSAGE, $actualErrorMessage);

        // Setting correct URLs should give corresponding responses
        $actualErrorMessage = $this->_getErrorMessageFromCurl($url1);

        $this->assertEquals($expectedErrorMessage1, $actualErrorMessage);

        $actualErrorMessage = $this->_getErrorMessageFromCurl($url2);

        $this->assertEquals($expectedErrorMessage2, $actualErrorMessage);
    }

    public function testSetInfo()
    {
        $curl = $this->_curlStub;

        $expectedHttpCode = '404';
        $expectedTotalTime = '2';
        $expectedEffectiveUrl = 'http://www.google.com';

        $explicitInfo = array(
            CURLINFO_HTTP_CODE => $expectedHttpCode,
            CURLINFO_TOTAL_TIME => $expectedTotalTime,
            CURLINFO_EFFECTIVE_URL => $expectedEffectiveUrl
        );
        $url = 'http://www.google.com';

        $requiredOptions = array(
            CURLOPT_URL => $url
        );
        $curl->setInfo($explicitInfo, $requiredOptions);

        $actualHttpCode = $this->_getInfoFromCurl($url, CURLINFO_HTTP_CODE);
        $actualTotalTime = $this->_getInfoFromCurl($url, CURLINFO_TOTAL_TIME);
        $actualEffectiveUrl = $this->_getInfoFromCurl($url, CURLINFO_EFFECTIVE_URL);
        $actualInfoArray = $this->_getInfoFromCurl($url);

        $expectedInfoArray = array(
            'http_code' => $expectedHttpCode,
            'total_time' => $expectedTotalTime,
            'url' => $expectedEffectiveUrl
        );

        $this->assertEquals($expectedHttpCode, $actualHttpCode);
        $this->assertEquals($expectedTotalTime, $actualTotalTime);
        $this->assertEquals($expectedEffectiveUrl, $actualEffectiveUrl);

        foreach($expectedInfoArray as $key => $value)
        {
            $this->assertArrayHasKey($key, $actualInfoArray);
            $this->assertEquals($value, $actualInfoArray[$key]);
        }
    }

    public function testDifferentPrioritiesForDifferentOutput()
    {
        $curl = $this->_curlStub;

        $expectedResponse1 = 'google page for chrome found';
        $url1 = 'http://www.google.com';
        $userAgent1 = 'Chrome/22.0.1207.1';
        $referer1 = 'http://mail.google.com';
        $expectedErrorCode2 = CURLE_FILESIZE_EXCEEDED;
        $url2 = $url1;
        $userAgent2 = $userAgent1;

        $expectedHttpCode3 = '200';
        $explicitInfo3 = array(
            CURLINFO_HTTP_CODE => $expectedHttpCode3
        );
        $url3 = $url1;

        $requiredOptions1 = array(
            CURLOPT_URL => $url1,
            CURLOPT_USERAGENT => $userAgent1,
            CURLOPT_REFERER => $referer1
        );
        $curl->setResponse($expectedResponse1, $requiredOptions1);
        $requiredOptions2 = array(
            CURLOPT_URL => $url2,
            CURLOPT_USERAGENT => $userAgent2
        );
        $curl->setErrorCode($expectedErrorCode2, $requiredOptions2);
        $requiredOptions3 = array(
            CURLOPT_URL => $url3
        );
        $curl->setInfo($explicitInfo3, $requiredOptions3);

        // most specific case
        $actualResponse = $this->_getResponseFromCurl($url1, $requiredOptions1);
        $actualErrorCode = $this->_getErrorCodeFromCurl($url1, $requiredOptions1);
        $actualHttpCode = $this->_getInfoFromCurl($url1, CURLINFO_HTTP_CODE, $requiredOptions1);

        $this->assertEquals($expectedResponse1, $actualResponse);
        $this->assertEquals($expectedErrorCode2, $actualErrorCode);
        $this->assertEquals($expectedHttpCode3, $actualHttpCode);

        // second-most specific case
        $actualResponse = $this->_getResponseFromCurl($url2, $requiredOptions2);
        $actualErrorCode = $this->_getErrorCodeFromCurl($url2, $requiredOptions2);
        $actualHttpCode = $this->_getInfoFromCurl($url2, CURLINFO_HTTP_CODE, $requiredOptions2);

        $this->assertEquals(self::DEFAULT_RESPONSE, $actualResponse);
        $this->assertEquals($expectedErrorCode2, $actualErrorCode);
        $this->assertEquals($expectedHttpCode3, $actualHttpCode);

        // least specific case
        $actualResponse = $this->_getResponseFromCurl($url3, $requiredOptions3);
        $actualErrorCode = $this->_getErrorCodeFromCurl($url3, $requiredOptions3);
        $actualHttpCode = $this->_getInfoFromCurl($url3, CURLINFO_HTTP_CODE, $requiredOptions3);

        $this->assertEquals(self::DEFAULT_RESPONSE, $actualResponse);
        $this->assertEquals(self::DEFAULT_ERRORCODE, $actualErrorCode);
        $this->assertEquals($expectedHttpCode3, $actualHttpCode);

        // fallback
        $actualResponse = $this->_getResponseFromCurl();
        $actualErrorCode = $this->_getErrorCodeFromCurl();
        $actualHttpCode = $this->_getInfoFromCurl(null , CURLINFO_HTTP_CODE);

        $this->assertEquals(self::DEFAULT_RESPONSE, $actualResponse);
        $this->assertEquals(self::DEFAULT_ERRORCODE, $actualErrorCode);
        $this->assertEquals('', $actualHttpCode);
    }

    private function _getResponseFromCurl($url = null, $options = null)
    {
        return $this->_getResultFromCurl($url, $options, self::RETURN_RESPONSE);
    }

    private function _getErrorCodeFromCurl($url = null, $options = null)
    {
        return $this->_getResultFromCurl($url, $options, self::RETURN_ERRORCODE);
    }

    private function _getErrorMessageFromCurl($url = null, $options = null)
    {
        return $this->_getResultFromCurl($url, $options, self::RETURN_ERRORMESSAGE);
    }

    private function _getInfoFromCurl($url = null, $opt = 0, $options = null)
    {
        return $this->_getResultFromCurl($url, $options, self::RETURN_INFO, $opt);
    }

    private function _getResultFromCurl($url, $options, $returnFlag, $opt = 0)
    {
        $curl = $this->_curlStub;
        $ch = $curl->curl_init($url);

        if ($options != null) {
            $curl->curl_setopt_array($ch, $options);
        }

        ob_start();
        $curl->curl_exec($ch);
        $actualResponse = ob_get_clean();

        $result = null;

        switch($returnFlag)
        {
        case self::RETURN_RESPONSE:
            $result = $actualResponse;
            break;
        case self::RETURN_ERRORCODE:
            $result = $curl->curl_errno($ch);
            break;
        case self::RETURN_ERRORMESSAGE:
            $result = $curl->curl_error($ch);
            break;
        case self::RETURN_INFO:
            $result = $curl->curl_getinfo($ch, $opt);
            break;
        }

        $curl->curl_close($ch);

        return $result;
    }
}