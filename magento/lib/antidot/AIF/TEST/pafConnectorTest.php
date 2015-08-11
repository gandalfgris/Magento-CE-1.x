<?php ob_start();
require_once('AIF/afs_paf_connector.php');


class PafConnectorTest extends PHPUnit_Framework_TestCase
{
    public function testOneShot()
    {
        return;
        $auth = new AfsUserAuthentication('antidot', 'change_on_install', AFS_AUTH_ANTIDOT);
        $service = new AfsService(42);
        $content = '<?xml version="1.0"?><root><uri>http://generated.doc.<?php ob_start();</uri><title>Generated doc</title><content>Generated content</content></root>';
        $doc = new AfsDocument($content);
        $connector = new AfsPafConnector('quigon', $service, 'TestPaF', $auth);
        $result = $connector->upload_doc($doc);
        $this->assertTrue($result->has_result());
        $this->assertFalse($result->in_error());
    }
}


