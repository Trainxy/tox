<?php
/**
 * Provides the essential behaiors of file object.
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
 * Represents the essential behaiors of file object.
 *
 * @package tox.filesystem
 * @author  Trainxy Ho <trainxy@gmail.com>
 */
class File
{

    /**
     * Stores path of the file.
     *
     * @var string
     */
    protected $path;

    /**
     * Stores create time of the file.
     *
     * @var string
     */
    private $ctime;

    /**
     * Stores last update time of the file.
     *
     * @var string
     */
    private $mtime;

    /**
     * Stores size of the file with bit.
     *
     * @var int
     */
    private $size;

    /**
     * Stores owner' group info of the file.
     * This property not provided for Windows.
     *
     * @var array
     */
    private $group;

    /**
     * Stores privileges of the file.
     * Sample : 0644, 1777
     *
     * @var string
     */
    private $privileges;

    /**
     * Stores all data of the file.
     *
     * @var mixed
     */
    protected $content;

    /**
     * CONSTRUCT FUNCTOIN
     *
     * @param string $path Path of file.
     *
     * @throws UnauthorizedAccessException   The caller does not have
     *                                          the required permission.
     * @throws IllegalPathException          Path is a zero-length string,
     *                                          or contains invalid characters.
     */
    public function __construct($path)
    {

    }

    /**
     * Creates or overwrites the specified file.
     *
     * @param  sting $path The path and name of the file to create.
     * @return self
     *
     * @throws UnauthorizedAccessException   The caller does not have
     *                                          the required permission.
     * @throws EmptyPathException            Path is a zero-length string,
     *                                          or contains invalid characters.
     */
    public static function create($path)
    {
        // TODO Validates the path with data validator.
        if ('' == $path) {
            throw new IllegalPathException();
        }

    }

    /**
     * Make a copy of self.
     *
     * @param  string $target    Path of target file.
     * @param  string $override  Flag of override, not override default.
     * @return void
     */
    public function copyTo($target, $override = false)
    {

    }

    /**
     * Move to other place.
     *
     * @param  string $target    Path of target file.
     * @return void
     */
    public function moveTo($target)
    {

    }

    /**
     * Destroy the file object, the file will be unlink.
     *
     * @return false
     */
    public function destroy()
    {

    }

    /**
     * Get all content of this file.
     *
     * @return mixed.
     */
    public function read()
    {

    }

    /**
     * Append context to this file.
     *
     * @param string $context  Content wanna to append.
     * @param string $position Position write flag, value of ('end', 'begin').
     * @return void
     */
    public function append($context, $position = 'end')
    {

    }

    /**
     * Replace all occurrences of the search string with the replacement string, and change the file.
     *
     * @param  string $search   The value being searched for.
     * @param  string $replace  The replacement value that replaced found search values.
     * @return self
     */
    public function replace($search, $replace)
    {

    }

    /**
     * Searches subject for matches to pattern and replaces them with replacement, and change the file.
     *
     * @param  string $pattern  The pattern to search for.
     * @param  string $replace  The replacement value that replaced found search values.
     * @return self
     */
    public function pregReplace($pattern, $replace)
    {

    }

    /**
     * Get last update time of the file.
     *
     * @return string
     */
    protected function getMtime()
    {

    }

    /**
     * Get create time of the file.
     *
     * @return string
     */
    protected function getCtime()
    {

    }

    /**
     * Get size of the file.
     *
     * @return int
     */
    protected function getSize()
    {

    }

    /**
     * Get group info of the file.
     *
     * @return string
     */
    protected function getGroup()
    {

    }

    /**
     * Get privileges of the file.
     *
     * @return string
     */
    protected function getPrivileges()
    {

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
