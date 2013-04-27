<?php
/**
 * Defines the test case for Tox\System\IO\Directory.
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

use PHPUnit_Framework_TestCase;
use org\bovigo\vfs\vfsStream;

if (!defined('DIR_VFSSTREAM')) {
    define('DIR_VFSSTREAM', __DIR__ . '/../../../../include/mikey179/vfsStream/src/main/php/org/bovigo/vfs');
}

require_once DIR_VFSSTREAM . '/vfsStream.php';
require_once DIR_VFSSTREAM . '/vfsStreamWrapper.php';
require_once DIR_VFSSTREAM . '/Quota.php';
require_once DIR_VFSSTREAM . '/vfsStreamContent.php';
require_once DIR_VFSSTREAM . '/vfsStreamAbstractContent.php';
require_once DIR_VFSSTREAM . '/vfsStreamContainer.php';
require_once DIR_VFSSTREAM . '/vfsStreamDirectory.php';
require_once DIR_VFSSTREAM . '/vfsStreamFile.php';
require_once DIR_VFSSTREAM . '/vfsStreamContainerIterator.php';

require_once __DIR__ . '/../../../../src/core/assembly.php';
require_once __DIR__ . '/../../../../src/core/exception.php';
require_once __DIR__ . '/../../../../src/system/io/directory.php';
require_once __DIR__ . '/../../../../src/system/io/@exception/illegalpath.php';
require_once __DIR__ . '/../../../../src/system/io/@exception/existpath.php';
require_once __DIR__ . '/../../../../src/system/io/@exception/notemptydirectory.php';

use Tox;

/**
 * Tests Tox\System\IO\Direction.
 *
 * @internal
 *
 * @package tox.system.io
 * @author  Trainxy Ho <trainxy@gmail.com>
 */
class DirectoryTest extends PHPUnit_Framework_TestCase
{

    /**
     * Stores the virtual file system.
     *
     * @var vfsStream
     */
    protected $vfs;

    /**
     * Stores the root folder name.
     *
     * WARNING: Randomize names are used to ignore the `require_once()`
     * machenism.
     *
     * @var string
     */
    protected $root;

    protected $cwd;

    public function setUp()
    {
        $this->cwd = getcwd();
        $this->root = md5(microtime());
        $this->vfs = vfsStream::setUp(
            $this->root,
            0755,
            array(
                'directory1' => array(
                    'file1' => 'text of file1',
                    'file2' => 'text of file2',
                    'subdirectory1' => array(
                        'subfile1' => 'text of subfile1',
                        'subfile2' => 'text of subfile2'
                    )
                ),
                'file3' => 'text of file3',
                'file4' => 'text of file4'
            )
        );
    }

    public function testPropertiesOfDirectoryIsCorrect()
    {
        $o_dir = new Tox\System\IO\Directory(vfsStream::url($this->root));
        $a_files = $o_dir->files;
        $a_vfs_files = array(
            'directory1' => array(
                'file1',
                'file2',
                'subdirectory1' => array('subfile1', 'subfile2')
            ),
            'file3',
            'file4'
        );
        $this->assertEquals('0755', $o_dir->privileges);
        $this->assertEquals($a_vfs_files, $o_dir->files);
    }

    public function testDestroyDirectoryHasItems()
    {
        $o_dir = new Tox\System\IO\Directory(vfsStream::url($this->root));
        $o_dir->destroy(true);
        $this->assertEmpty($o_dir->files);
    }

    /**
     * @expectedException Tox\System\IO\NotEmptyDirectoryException
     */
    public function testNotEmptyDirectoryWouldNotBeDestroyed()
    {
        $o_dir = new Tox\System\IO\Directory(vfsStream::url($this->root));
        $o_dir->destroy();
    }

    /**
     * @expectedException Tox\System\IO\IllegalPathException
     */
    public function testEmptyDirectoryWouldNotBeCreated()
    {
        Tox\System\IO\Directory::create('');
    }

    /**
     * @expectedException Tox\System\IO\ExistPathException
     */
    public function testExistDirectoryWouldNotBeCreated()
    {
        Tox\System\IO\Directory::create(vfsStream::url($this->root . DIRECTORY_SEPARATOR . 'file3'));
    }

    public function tearDown()
    {
        chdir($this->cwd);
    }
}
// vi:ft=php fenc=utf-8 ff=unix ts=4 sts=4 et sw=4 fen fdm=indent fdl=1 tw=120
