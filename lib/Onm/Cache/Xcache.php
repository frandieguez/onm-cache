<?php
/**
 * Defines the Onm\Cache\XcacheCache
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
 * Xcache cache driver.
 *
 * @package Onm_Cache
 */
class XcacheCache extends AbstractCache
{
    /**
     * {@inheritdoc}
     */
    public function getIds()
    {
        $this->checkAuth();
        $keys = array();

        for ($i = 0, $count = xcache_count(XC_TYPE_VAR); $i < $count; $i++) {
            $entries = xcache_list(XC_TYPE_VAR, $i);

            if (is_array($entries['cache_list'])) {
                foreach ($entries['cache_list'] as $entry) {
                    $keys[] = $entry['name'];
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
        return $this->doContains($id) ? unserialize(xcache_get($id)) : false;
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
        return xcache_isset($id);
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
        return xcache_set($id, serialize($data), (int) $lifeTime);
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
        return xcache_unset($id);
    }

    /**
     * Checks that xcache.admin.enable_auth is Off
     *
     * @throws \BadMethodCallException When xcache.admin.enable_auth is On
     * @return void
     */
    protected function checkAuth()
    {
        if (ini_get('xcache.admin.enable_auth')) {
            throw new \BadMethodCallException(
                'To use all features of \Onm\Common\Cache\XcacheCache, '
                .'you must set "xcache.admin.enable_auth" to '
                .'"Off" in your php.ini.'
            );
        }
    }
}
