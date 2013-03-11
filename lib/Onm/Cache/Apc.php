<?php
/**
 * Defines the Onm\Cache\Apc class
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
 * APC cache driver.
 *
 * @package Onm_Cache
 */
class Apc extends AbstractCache
{
    /**
     * Initilizes the APCCache
     *
     * @param $options
     */
    public function __construct($options = array())
    {
        $this->initialize($options);
    }

    /**
     * Initializes this APCCache instance.
     *
     * @param array $options the optiosn to initialize the cache layer
     *
     * @return void
     */
    public function initialize($options = array())
    {
        if (!function_exists('apc_store') || !ini_get('apc.enabled')) {
            throw new \Exception(
                'You must have APC installed and enabled to use APCCache class.'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getIds()
    {
        $ci = apc_cache_info('user');
        $keys = array();

        foreach ($ci['cache_list'] as $entry) {
            $keys[] = $entry['info'];
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
        return apc_fetch($id);
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
        $found = false;

        apc_fetch($id, $found);

        return $found;
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
        return (bool) apc_store($id, $data, (int) $lifeTime);
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
        return apc_delete($id);
    }
}
