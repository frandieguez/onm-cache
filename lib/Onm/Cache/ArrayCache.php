<?php
/**
 * Defines the Onm\Cache\ArrayCache class
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
 * Array cache driver.
 *
 * @package Onm_Cache
 */
class ArrayCache extends AbstractCache
{
    /**
     * Array where the cache entries will be saved
     *
     * @var array $data
     */
    private $dataArray = array();

    /**
     * {@inheritdoc}
     */
    public function getIds()
    {
        return array_keys($this->dataArray);
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
        if (isset($this->dataArray[$id])) {
            return $this->dataArray[$id];
        }

        return false;
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
        return isset($this->dataArray[$id]);
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
        $this->dataArray[$id] = $data;

        return true;
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
        unset($this->dataArray[$id]);

        return true;
    }
}
