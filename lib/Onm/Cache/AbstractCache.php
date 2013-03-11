<?php
/**
 * Defines the Onm\Cache\AbstractCache abstract class
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
 * Base class for cache driver implementations.
 *
 * @package Onm_Cache
 */
abstract class AbstractCache implements CacheInterface
{
    /**
     * The namespace to prefix all cache ids with
     * @var string
     **/
    private $namespace = '';

    /**
     * Set the namespace to prefix all cache ids with
     *
     * @param  string $namespace
     * @return void
     */
    public function setNamespace($namespace)
    {
        $this->namespace = (string) $namespace;
    }

    /**
     * {@inheritdoc}
     *
     * @param  string $id cache id The id of the cache entry to fetch.
     * @return string The cached data or FALSE, if no cache entry
     *                exists for the given id.
     */
    public function fetch($id)
    {
        return $this->doFetch($this->getNamespacedId($id));
    }

    /**
     * {@inheritdoc}
     *
     * @param  string  $id cache id The cache id of the entry to check for.
     * @return boolean TRUE if a cache entry exists for
     *                 the given cache id, FALSE otherwise.
     */
    public function contains($id)
    {
        return $this->doContains($this->getNamespacedId($id));
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
    public function save($id, $data, $lifeTime = 0)
    {
        return $this->doSave($this->getNamespacedId($id), $data, $lifeTime);
    }

    /**
     * {@inheritdoc}
     *
     * @param  string  $id cache id
     * @return boolean TRUE if the cache entry was successfully deleted,
     *                 FALSE otherwise.
     */
    public function delete($id)
    {
        $id = $this->getNamespacedId($id);

        if (strpos($id, '*') !== false) {
            return $this->deleteByRegex('/'.str_replace('*', '.*', $id).'/');
        }

        return $this->doDelete($id);
    }

    /**
     * Deletes all cache entries.
     *
     * @return array $deleted  Array of the deleted cache ids
     */
    public function deleteAll()
    {
        $ids = $this->getIds();

        foreach ($ids as $id) {
            $this->delete($id);
        }

        return $ids;
    }

    /**
     * Deletes cache entries where the id matches a PHP regular expressions
     *
     * @param  string $regex
     * @return array  $deleted  Array of the deleted cache ids
     */
    public function deleteByRegex($regex)
    {
        $deleted = array();

        $ids = $this->getIds();

        foreach ($ids as $id) {
            if (preg_match($regex, $id)) {
                $this->delete($id);
                $deleted[] = $id;
            }
        }

        return $deleted;
    }

    /**
     * Deletes cache entries where the id has the passed prefix
     *
     * @param  string $prefix
     * @return array  $deleted  Array of the deleted cache ids
     */
    public function deleteByPrefix($prefix)
    {
        $deleted = array();

        $prefix = $this->getNamespacedId($prefix);
        $ids = $this->getIds();

        foreach ($ids as $id) {
            if (strpos($id, $prefix) === 0) {
                $this->delete($id);
                $deleted[] = $id;
            }
        }

        return $deleted;
    }

    /**
     * Deletes cache entries where the id has the passed suffix
     *
     * @param  string $suffix
     * @return array  $deleted  Array of the deleted cache ids
     */
    public function deleteBySuffix($suffix)
    {
        $deleted = array();

        $ids = $this->getIds();

        foreach ($ids as $id) {
            if (substr($id, -1 * strlen($suffix)) === $suffix) {
                $this->delete($id);
                $deleted[] = $id;
            }
        }

        return $deleted;
    }

    /**
     * Prefix the passed id with the configured namespace value
     *
     * @param  string $id The id to namespace
     * @return string $id The namespaced id
     */
    private function getNamespacedId($id)
    {
        return $this->namespace . $id;
    }

    /**
     * Fetches an entry from the cache.
     *
     * @param  string $id cache id The id of the cache entry to fetch.
     * @return string The cached data or FALSE, if no cache entry
     *                exists for the given id.
     */
    abstract protected function doFetch($id);

    /**
     * Test if an entry exists in the cache.
     *
     * @param  string  $id cache id The cache id of the entry to check for.
     * @return boolean TRUE if a cache entry exists for
     *                 the given cache id, FALSE otherwise.
     */
    abstract protected function doContains($id);

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
    abstract protected function doSave($id, $data, $lifeTime = false);

    /**
     * Deletes a cache entry.
     *
     * @param  string  $id cache id
     * @return boolean TRUE if the cache entry was successfully deleted,
     *                 FALSE otherwise.
     */
    abstract protected function doDelete($id);

    /**
     * Get an array of all the cache ids stored
     *
     * @return array $ids
     */
    abstract public function getIds();
}
