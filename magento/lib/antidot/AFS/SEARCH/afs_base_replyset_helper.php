<?php
require_once 'AFS/SEARCH/afs_meta_helper.php';
require_once 'AFS/SEARCH/afs_producer.php';
require_once 'COMMON/afs_helper_base.php';
require_once 'COMMON/afs_helper_format.php';

/** @brief Base class for replyset helpers. */
class AfsBaseReplysetHelper extends AfsHelperBase
{
    protected $meta = null;
    protected $replies = array();

    /** @brief Construct new replyset helper instance.
     *
     * @param $reply_set [in] one reply from decoded json reply.
     * @param $config [in] helper configuration object (see AfsHelperConfiguration).
     * @param $factory [in] used to create appropriate reply helper.
     */
    public function __construct($reply_set, AfsHelperConfiguration $config,
        AfsReplyHelperFactory $factory)
    {
        $this->initialize_meta($reply_set, $config);
        $this->initialize_content($reply_set, $config, $factory);
    }

    protected function initialize_meta($reply_set, AfsHelperConfiguration $config)
    {
        $this->meta = new AfsMetaHelper($reply_set->meta);
    }

    protected function initialize_content($reply_set, AfsHelperConfiguration $config, $factory)
    {
        if (property_exists($reply_set, 'content')) {
            $feed = $this->meta->get_feed();
            $this->replies = $factory->create_replies($feed, $reply_set->content);
        }
    }

    /** @brief Retrieves meta data object.
     * @return instance of @a AfsMetaHelper.
     */
    public function get_meta()
    {
        return $this->meta;
    }

    /** @brief Checks whether reply set contains at least one reply.
     * @return true when one or more reply is defined, false otherwise.
     */
    public function has_reply()
    {
        return ! empty($this->replies);
    }
    /** @brief Retrieve number of replies for current page.
     *
     * you can retrieve total number of replies through
     * <tt>get_meta()->get_total_items()</tt>.
     *
     * @return number of replies for current page.
     */
    public function get_nb_replies()
    {
        return count($this->replies);
    }
    /** @brief Retrieves all replies of current page.
     *
     * You can loop on each reply:
     * @code
     * foreach ($replies->get_replies() as $reply) {
     *   // Work on reply
     * }
     * @endcode
     *
     * @return All replies of current page.
     */
    public function get_replies()
    {
        return $this->replies;
    }

    /** @brief Retrieves replyset as array.
     *
     * All data are store in <tt>key => value</tt> format:
     * @li @c meta: array of meta data (@a AfsMetaHelper::format),
     * @li @c nb_replies: number of replies on the current page.
     * @li @c replies: standard or Promote reply.
     *
     * @return array filled with key and values.
     */
    public function format()
    {
        $result = array('meta' => $this->get_meta()->format(),
            'nb_replies' => $this->get_nb_replies());

        if ($this->has_reply()) {
            $formatted_replies = array();
            foreach ($this->replies as $reply)
                $formatted_replies[] = $reply->format();
            $result['replies'] = $formatted_replies;
        }
        return $result;
    }
}


