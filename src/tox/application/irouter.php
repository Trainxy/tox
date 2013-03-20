<?php
/**
 * Defines the behaviors of applications routers.
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

namespace Tox\Application;

/**
 * Announces the behaviors of applications routers.
 *
 * @package tox.application
 * @author  Snakevil Zen <zsnakevil@gmail.com>
 */
interface IRouter
{
    /**
     * CONSTRUCT FUNCTION
     *
     * @param array[] $routes OPTIONAL. Initial routing rules.
     */
    public function __construct($routes = array());

    /**
     * Analysis routing token from applications input.
     *
     * @param  IInput $input Applications input.
     * @return IToken
     */
    public function analyse(IInput $input);

    /**
     * Imports extra routing rules.
     *
     * @param  array[]  $routes  Routing rules to be imported.
     * @param  boolean  $prepend OPTIONAL. Whether prepending the rules to
     *                           existant. FALSE defaults.
     * @return self
     */
    public function import($routes, $prepend = false);
}

// vi:ft=php fenc=utf-8 ff=unix ts=4 sts=4 et sw=4 fen fdm=indent fdl=1 tw=120
