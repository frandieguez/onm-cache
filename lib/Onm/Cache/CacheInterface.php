<?php
/**
 * Defines the Onm\Cache\CacheInterface interface class
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
 * Interface for cache drivers.
 *
 * @package Onm_Cache
 */
interface CacheInterface
{
    /**
     * Fetches an entry from the cache.
     *
     * @param  string $id cache id The id of the cache entry to fetch
     * @return string The cached data or FALSE, if no
     *                cache entry exists for the given id.
     */
    public function fetch($id);

    /**
     * Test if an entry exists in the cache.
     *
     * @param  string  $id cache id The cache id of the entry to check for.
     * @return boolean TRUE if a cache entry exists for the
     *                 given cache id, FALSE otherwise.
     */
    public function contains($id);

    /**
     * Puts data into the cache.
     *
     * @param string $id       The cache id.
     * @param string $data     The cache entry/data.
     * @param int    $lifeTime The lifetime. If != false, sets a specific
     *                         lifetime for this cache entry (null => infinite
     *                         lifeTime).
     * @return boolean TRUE if the entry was successfully stored in the
     *                         cache, FALSE otherwise.
     */
    public function save($id, $data, $lifeTime = 0);

    /**
     * Deletes a cache entry.
     *
     * @param  string  $id cache id
     * @return boolean TRUE if the cache entry was successfully deleted,
     *                 FALSE otherwise.
     */
    public function delete($id);
}
