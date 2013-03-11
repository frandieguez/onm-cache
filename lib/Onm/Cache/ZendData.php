<?php
/**
 * Defines the Onm\Cache\ZendData class
 *
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Onm_Cache
 */
namespace Onm\Cache;

/**
 * Zend Data Cache cache driver.
 *
 * @package Onm_Cache
 */
class ZendData extends AbstractCache
{
    /**
     * Initializes the cache handler class
     *
     * @param array $options options to change initialization of the cache layer
     *
     * @return void
     **/
    public function __construct($options)
    {
        // zend data cache format for namespaces ends in ::
        $this->setNamespace('base::');
    }

    /**
     * {@inheritdoc}
     */
    public function getIds()
    {
        throw new \BadMethodCallException(
            "getIds() is not supported by ZendDataCache"
        );
    }

    /**
     * {@inheritdoc}
     *
     * @param  string $id cache id The id of the cache entry to fetch.
     * @return string The cached data or FALSE, if no cache entry
     *                exists for the given id.
     */
    protected function doFetch($id)
    {
        return zend_shm_cache_fetch($id);
    }

    /**
     * {@inheritdoc}
     *
     * @param  string  $id cache id The cache id of the entry to check for.
     * @return boolean TRUE if a cache entry exists for
     *                 the given cache id, FALSE otherwise.
     */
    protected function doContains($id)
    {
        return (zend_shm_cache_fetch($id) !== false);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $id       The cache id.
     * @param string $data     The cache entry/data.
     * @param int    $lifeTime The lifetime. If != false, sets a specific
     *                         lifetime for this cache entry (null => infinite
     *                         lifeTime).
     * @return boolean TRUE if the entry was successfully stored in the
     *                         cache, FALSE otherwise.
     */
    protected function doSave($id, $data, $lifeTime = 0)
    {
        return zend_shm_cache_store($id, $data, $lifeTime);
    }

    /**
     * {@inheritdoc}
     *
     * @param  string  $id cache id
     * @return boolean TRUE if the cache entry was successfully deleted,
     *                 FALSE otherwise.
     */
    protected function doDelete($id)
    {
        return zend_shm_cache_delete($id);
    }
}
