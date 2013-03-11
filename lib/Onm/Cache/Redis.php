<?php
/**
 * Defines the Onm\Cache\Redis class
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
 * Redis cache driver.
 *
 * @package Onm_Cache
 */
class Redis extends AbstractCache
{
    /**
     * The Redis server connection
     * @var Redis
     */
    private $redis;

    /**
     * Initializes the cache server layer
     *
     * @param array $options options to change initialization of the cache layer
     *
     * @return void
     **/
    public function __construct($options)
    {
        // Check if Predis library is installed
        if (!class_exists('\Predis\Client')) {
            throw new \Exception('Predis library not installed');
        }

        // is_string($options)
        // TODO: For now only direct connection to Redis is supported
        if (true) {
            // $redis = new \Predis\Client($options);
            $redis = new \Predis\Client();

            $this->setRedis($redis);
        }

        return $this;
    }

    /**
     * Sets the redis instance to use.
     *
     * @param Memcache $memcache
     */
    public function setRedis(\Predis\Client $redis)
    {
        $this->redis = $redis;
    }

    /**
     * Gets the redis instance used by the cache.
     *
     * @return Memcache
     */
    public function getRedis()
    {
        return $this->redis;
    }

    /**
     * {@inheritdoc}
     */
    public function getIds()
    {
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
        $data = $this->redis->get($id);

        $dataUnserialized = @unserialize($data);
        if ($data !== false || $str === 'b:0;') {
            return $data;
        } else {
            return $dataUnserialized;
        }
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
        return (bool) $this->redis->exists($id);
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
    protected function doSave($id, $data, $lifeTime = -1)
    {
        if (!is_string($data)) {
            $data = serialize($data);
        }

        $saved = $this->redis->set($id, $data);

        // Set the expire time for this key if valid lifeTime
        if ($lifeTime > -1) {
            $this->redis->expire($id, $lifeTime);
        }
        return $saved;
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
        return $this->redis->delete($id);
    }
}
