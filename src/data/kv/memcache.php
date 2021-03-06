<?php
/**
 * Defines the memcache data source.
 *
 * This file is part of Tox.
 *
 * Tox is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Tox is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tox.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright © 2012-2013 SZen.in
 * @license   GNU General Public License, version 3
 */

namespace Tox\Data\KV;

/**
 * Represents as the memcache data source.
 *
 * @package tox.data.kv
 * @author  Qiang Fu <fuqiang007enter@gmail.com>
 * @since   0.1.0-beta1
 */
class Memcache extends KV
{
    /**
     * Choices of Memcache or Memcached .
     *
     * @var bool
     */
    public $useMemcached;

    /**
     * Memcache the Memcache instance.
     *
     * @var mixed
     */
    protected $cache;

    /**
     * Lists of memcache server configurations.
     *
     * @var array
     */
    protected $servers;

    /**
     * Memcache default expire time
     */
    protected $expireTime;

    /**
     * Memcache persistent id
     */
    protected $field;

    /**
     * Memcache COMPRESSION option
     *
     * @var bool
     */
    protected $compression;

    /**
     * CONSTRUCT FUNCTION
     */
    public function __construct($field = null)
    {
        if (null === $this->useMemcached) {
            $this->useMemcached = true;
        }
        if (null !== $field) {
            $this->field = $field;
        }
    }

    /**
     * Sets the memcache default expire time
     *
     * @param field $expire
     */
    public function setExpireTime($expire)
    {
        if (null !== $expire) {
            $this->expireTime = $expire;
        }
    }

    /**
     * Sets the memcache default expire time
     *
     * @param booler $option
     */
    public function setCompression($option)
    {
        if (!$option) {
            $this->compression = $option;
        } else {
            $this->compression = true;
        }
    }

    /**
     * Gets the memcache default expire time
     *
     * @param booler $option
     */
    public function getCompression()
    {
        return $this->compression;
    }

    /**
     * Gets the memcache default expire time
     *
     * @param field $expire
     */
    public function getExpireTime()
    {
        return $this->expireTime;
    }

    /**
     * It creates the memcache instance and adds memcache servers.
     *
     * @return void
     */
    public function init()
    {
        $servers = $this->getServers();
        $cache = $this->getMemCache();


        if (is_array($servers) && count($servers) > 0) {
            foreach ($servers as $server) {
                if (count($servers)) {
                    foreach ($servers as $server) {
                        if ($this->useMemcached) {
                            $serverList = $cache->getServerList();
                            if (null != $this->field) {
                                if ($this->field === $server->field) {
                                    if (count($serverList) > 0) {
                                        $serverExit = false;
                                        foreach ($serverList as $key => $value) {
                                            if ($value['host'] == $server->host && $value['port'] == $server->port) {
                                                $serverExit = true;
                                            }
                                        }
                                        if (false === $serverExit) {
                                            $cache->addServer($server->host, $server->port, $server->weight);
                                        }
                                    }
                                }
                            } else {
                                if (count($serverList) > 0) {
                                    $serverExit = false;
                                    foreach ($serverList as $key => $value) {
                                        if ($value['host'] == $server->host && $value['port'] == $server->port) {
                                            $serverExit = true;
                                        }
                                    }
                                    if (false === $serverExit) {
                                        $cache->addServer($server->host, $server->port, $server->weight);
                                    }
                                }
                            }
                        } else {
                            $cache->addServer(
                                $server->host,
                                $server->port,
                                $server->persistent,
                                $server->weight,
                                $server->timeout,
                                $server->retryInterval,
                                $server->status
                            );
                        }
                    }
                }
            }
        } else {
            throw new MemcacheConfigNotArrayException(array('config' => ''));
        }
    }

    /**
     * Get instance of the memcache or memcached.
     *
     * @return Memcache|Memcached
     */
    public function getMemCache()
    {
        if ($this->cache !== null) {
            return $this->cache;
        } else {
            return $this->cache = $this->useMemcached ? $this->defaultMemcached() : $this->defaultMemcache();
        }
    }

    /**
     * Get instance of the memcached.
     *
     * @return Memcache|Memcached
     */
    protected function defaultMemcached()
    {
        if (null != $this->field) {
            static $memcached_instances = array();

            if (array_key_exists($this->field, $memcached_instances)) {
                $instance = $memcached_instances[$this->field];
            } else {
                $instance = new \Memcached($this->field);
                $instance->setOption(\Memcached::OPT_PREFIX_KEY, $this->field);
                $instance->setOption(\Memcached::OPT_LIBKETAMA_COMPATIBLE, true);
                if (false === $this->compression) {
                    $instance->setOption(\Memcached::OPT_COMPRESSION, false);
                }
                $memcached_instances[$this->field] = $instance;
            }
            return $instance;
        } else {
            return new \Memcached;
        }
    }

    /**
     * Get instance of the memcache .
     *
     * @return Memcache|Memcached
     */
    protected function defaultMemcache()
    {
        return new \Memcache;
    }

    /**
     * Get memcache server configurations .
     *
     * @return array
     */
    public function getServers()
    {
        return $this->servers;
    }

    /**
     * Set memcache server configurations .
     *
     * @param array $config memcache server configurations value.
     * @return void
     */
    public function setServers($config)
    {
        if ($config['useMemcached'] === true) {
            $memcacheConfig = $config['memcached'];
        } else {
            $this->useMemcached = false;
            $memcacheConfig = $config['memcache'];
        }
        foreach ($memcacheConfig as $c) {
            $this->servers[] = new MemCacheServerConfiguration($c);
        }
    }

    /**
     * Retrieves a value from cache with a specified key.
     *
     * @param  string $key a unique key identifying the cached value.
     * @return string
     */
    protected function getValue($key)
    {
        return $this->cache->get($key);
    }

    /**
     * Retrieves multiple values from cache with the specified keys.
     *
     * @param  array $keys a list of keys identifying the cached values.
     * @return array
     */
    protected function getValues($keys)
    {
        return $this->useMemcached ? $this->cache->getMulti($keys) : $this->cache->get($keys);
    }

    /**
     * Stores a value identified by a key in cache.
     *
     * @param  string  $key    the key identifying the value to be cached.
     * @param  string  $value  the value to be cached.
     * @param  integer $expire the number of seconds in which the cached value
     *                         will expire. 0 means never expire.
     * @return boolean         true if the value is successfully stored into
     *                         cache, false otherwise.
     */
    protected function setValue($key, $value, $expire = 0)
    {

        if ($expire > 0) {
        } elseif (null !== $this->expireTime) {
            $expire = $this->expireTime;
        } else {
            $expire = 0;
        }
        return $this->useMemcached ?
                $this->cache->set($key, $value, $expire) :
                $this->cache->set($key, $value, 0, $expire);
    }

    /**
     * Stores a value identified by a key into cache if the cache does not contain this key.
     *
     * @param  string  $key    the key identifying the value to be cached
     * @param  string  $value  the value to be cached
     * @param  integer $expire the number of seconds in which the cached value
     *                         will expire. 0 means never expire.
     * @return boolean         true if the value is successfully stored into
     *                         cache, false otherwise
     */
    protected function addValue($key, $value, $expire)
    {
        if ($expire > 0) {
            $expire+=time();
        } else {
            $expire = 0;
        }

        return $this->useMemcached ?
                $this->cache->add($key, $value, $expire) :
                $this->cache->add($key, $value, 0, $expire);
    }

    /**
     * Deletes a value with the specified key from cache.
     *
     * @param  string $key the key of the value to be deleted.
     * @return boolean     if no error happens during deletion.
     */
    protected function deleteValue($key)
    {
        return $this->cache->delete($key, 0);
    }

    /**
     * Deletes all values from cache.
     *
     * @return boolean whether the flush operation was successful.
     */
    protected function clearValues()
    {
        return $this->cache->flush();
    }

    /**
     * Stores a value identified by a key in cache.
     *
     * Keys need to verify
     * Value must a string
     *
     * @param  string  $key    the key identifying the value to be cached.
     * @param  string  $value  the value to be cached.
     * @param  integer $expire the number of seconds in which the cached value
     *                         will expire. 0 means never expire.
     * @return boolean         true if the value is successfully stored into
     *                         cache, false otherwise.
     */
    public function setNginxMemcacheValue($key, $value, $expire = 0)
    {
        if (!is_string($value)) {
            throw new MemcacheValueNotStringException(array('value' => $value));
        }
        if (strlen($key) > 250) {
            throw new MemcacheKeyTooLongException(array('key' => $key));
        }
        return $this->useMemcached ?
                $this->cache->set($key, $value, $expire) :
                $this->cache->set($key, $value, 0, $expire);
    }

    /**
     * Retrieves a value from cache with a specified key.
     *
     * @param  string $key a unique key identifying the cached value.
     * @return string
     */
    public function getNginxMemcacheValue($key)
    {
        return $this->cache->get($key);
    }
}

// vi:ft=php fenc=utf-8 ff=unix ts=4 sts=4 et sw=4 fen fdm=indent fdl=1 tw=120
