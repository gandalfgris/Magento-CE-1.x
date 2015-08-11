<?php
require_once "AFS/SEARCH/afs_text_helper.php";
require_once "AFS/SEARCH/afs_reply_helper.php";
require_once "AFS/SEARCH/afs_promote_reply_helper.php";

/** @brief Factory for reply helper. */
class AfsReplyHelperFactory
{
    private $visitor = null;

    /** @brief Constructs new factory instance.
     * @param $visitor [in] visitor used to format title and client data texts.
     */
    public function __construct(AfsTextVisitorInterface $visitor=null)
    {
        $this->visitor = $visitor;
    }

    /** @brief Creates appropriate reply helper.
     *
     * @param $feed [in] name of the feed reply.
     * @param $reply [in] JSON decoded reply used to initialize the helper.
     *
     * @return standard or Promote reply helper.
     */
    public function create($feed, $reply)
    {
        if ('Promote' == $feed) {
            return new AfsPromoteReplyHelper($reply);
        } else {
            return new AfsReplyHelper($reply, $this->visitor);
        }
    }

    /** @brief Creates list of reply helpers.
     *
     * @param $feed [in] name of the feed reply.
     * @param $replies [in] JSON decoded object which may contain replies.
     *
     * @return list of reply helpers.
     */
    public function create_replies($feed, $replies)
    {
        $result = array();
        if (property_exists($replies, 'reply')) {
            foreach ($replies->reply as $reply)
                $result[] = $this->create($feed, $reply);
        }
        return $result;
    }
}


