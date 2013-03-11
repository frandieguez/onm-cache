<?php
/**
 * Defines the Onm\Cache\Memcache class
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

use \Memcache as BaseMemcache;

/**
 * Memcache cache driver.
 *
 * @package Onm_Cache
 */
class Memcache extends AbstractCache
{
    /**
     * The memcache server connection
     *
     * @var Memcache
     */
    private $memcache;

    /**
     * Initializes the database layer
     *
     * @param array $options options to change initialization of the cache layer
     *
     * @return void
     **/
    public function __construct($options)
    {
        if (array_key_exists('server', $options)
            && array_key_exists('port', $options)
        ) {
            $memcache = new \Memcache();
            $memcache->connect($options['server'], $options['port']);
            $this->setMemcache($memcache);
        }

        return $this;
    }

    /**
     * Sets the memcache instance to use.
     *
     * @param BaseMemcache $memcache
     */
    public function setMemcache(BaseMemcache $memcache)
    {
        $this->memcache = $memcache;
    }

    /**
     * Gets the memcache instance used by the cache.
     *
     * @return Memcache
     */
    public function getMemcache()
    {
        return $this->memcache;
    }

    /**
     * {@inheritdoc}
     */
    public function getIds()
    {
        $keys = array();
        $allSlabs = $this->memcache->getExtendedStats('slabs');

        foreach ($allSlabs as $server => $slabs) {
            if (is_array($slabs)) {
                foreach (array_keys($slabs) as $slabId) {
                    $dump = @$this->memcache->getExtendedStats(
                        'cachedump',
                        (int) $slabId
                    );

                    if ($dump) {
                        foreach ($dump as $entries) {
                            if ($entries) {
                                $keys = array_merge(
                                    $keys,
                                    array_keys($entries)
                                );
                            }
                        }
                    }
                }
            }
        }

        return $keys;
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
        return $this->memcache->get($id);
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
        return (bool) $this->memcache->get($id);
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
        return $this->memcache->set($id, $data, 0, (int) $lifeTime);
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
        return $this->memcache->delete($id);
    }
}
