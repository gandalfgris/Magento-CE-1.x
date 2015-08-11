<?php

require_once 'AFS/afs_connector.php';


/** @brief AFS ACP connector.
 *
 * AFS auto complete connnector. */
class AfsAcpConnector extends AfsConnector
{
    public function __construct($host, AfsService $service, $scheme=AFS_SCHEME_HTTP)
    {
        parent::__construct($host, $service, $scheme);
        if ($scheme != AFS_SCHEME_HTTP)
            throw InvalidArgumentException('ACP connector support only HTTP connection');
        $this->build_reply_as_associative_array();
    }

    /** @brief Retrieves web service name.
     * @return always return 'acp';
     */
    protected function get_web_service_name()
    {
        return 'acp';
    }

    /** @internal
     * @brief Overload default implemantation with something easiest to handle 
     * for ACP.
     *
     * @param $message [in] Error message.
     * @param $details [in] Error details.
     *
     * @return Associated array with error and details.
     */
    protected function build_error($message, $details)
    {
        return array('error' => $message, 'details' => $details);
    }
}
