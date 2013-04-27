<?php
/**
 * Provides the essential behaiors of directory object.
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

namespace Tox\System\IO;

/**
 * Represents the essential behaiors of directory object.
 *
 * @package tox.filesystem
 * @author  Trainxy Ho <trainxy@gmail.com>
 */
class Directory
{

    /**
     * Stores path of the file.
     *
     * @var string
     */
    protected $path;

    /**
     * Stores privileges of the file.
     * Sample : 0644, 1777
     *
     * @var string
     */
    private $privileges;

    /**
     * Stores file names (including their paths) under the directory.
     *
     * @var array
     */
    private $files;

    /**
     * CONSTRUCT FUNCTOIN
     *
     * @param string $path Path of directory.
     */
    public function __construct($path)
    {
        $this->path = $path;
        if (!file_exists($path)) {
            return false;
        }
        return $this;
    }

    /**
     * Creates or overwrites the specified file.
     *
     * @param  sting $path The path and name of the file to create.
     * @return self
     *
     * @throws UnauthorizedAccessException   The caller does not have
     *                                          the required permission.
     * @throws IllegalPathException          Path is a zero-length string,
     *                                          or contains invalid characters.
     */
    public static function create($path, $mode = '0700', $recursive = true)
    {
        // TODO Validates the path with data validator.
        if ('' == $path) {
            throw new IllegalPathException();
        }
        if (file_exists($path)) {
            throw new ExistPathException($path);
        } else {
            try {
                mkdir($path, $mode, $recursive);
            } catch (FailedToMakeDirectoryException $e) {
                throw new $e($path);
            }
        }
    }

    /**
     * Gets privileges of the file.
     *
     * @return string
     */
    protected function getPrivileges()
    {
        $this->privileges = substr(sprintf('%o', fileperms($this->path)), -4);
        return $this->privileges;
    }

    /**
     * Gets file names (including their paths) under the directory.
     *
     * @return array
     */
    protected function getFiles()
    {
        $this->files = $this->recursiveScanDir($this->path);
        return $this->files;
    }

    /**
     * Get all files of directory.
     *
     * @param  string $path  Path name will be scan.
     * @return array
     */
    protected function recursiveScanDir($path)
    {
        $files = array();
        if (is_dir($path)) {
            $items = scandir($path);
            for($ii = 0, $jj = count($items); $ii < $jj; $ii++) {
                if (!in_array($items[$ii], array('.', '..'))) {
                    if (is_dir($path . DIRECTORY_SEPARATOR . $items[$ii])) {
                        $files[$items[$ii]] = $this->recursiveScanDir($path . DIRECTORY_SEPARATOR . $items[$ii]);
                    } else {
                        $files[] = $items[$ii];
                    }
                }
            }
        }
        return $files;
    }

    /**
     * Destroy the file object, the file will be unlink.
     *
     * @param  $recursive Flag of recursive delete. true to remove directories,
                          subdirectories, and files in path; otherwise, false.
     * @return bool
     *
     * @throws NotEmptyDirectoryException  The directory is not empty.
     * @throws UnauthorizedAccessException The caller does not have
     *                                        the required permission.
     */
    public function destroy($recursive = false)
    {
        $items = scandir($this->path);
        if ($recursive) {
            $this->recursiveDel($this->path);
        } else {
            if ($items != array('.', '..')) {
                throw new NotEmptyDirectoryException($this->path);
            } else {
                try {
                    rmdir($this->path);
                } catch (UnauthorizedAccessException $e) {
                    throw new $e($this->path);
                }
            }
        }
    }

    public function recursiveDel($path)
    {
        $items = scandir($path);
        foreach ($items as $item) {
            $item = $path . DIRECTORY_SEPARATOR . $item;
            if (is_dir($item)) {
                $this->recursiveDel($item);
            } elseif (is_file($item)) {
                try {
                    unlink($item);
                } catch (UnauthorizedAccessException $e) {
                    throw new $e($items);
                }
            }
        }
        try {
            rmdir($path);
        } catch (UnauthorizedAccessException $e) {
            throw new $e($path);
        }
    }

    /**
     * Be invoked on retrieving a magic property.
     *
     * @param  string $prop Name of magic property.
     * @return mixed
     */
    public function __get($prop)
    {
        return call_user_func(array($this, 'get' . ucfirst($prop)));
    }
}
// vi:ft=php fenc=utf-8 ff=unix ts=4 sts=4 et sw=4 fen fdm=indent fdl=1 tw=120
