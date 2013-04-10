<?php
/**
 * Defines the test case for Tox\Application\Dao\Cache\Dao.
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

namespace Tox\Application\Dao\Cache;

use PHPUnit_Framework_TestCase;

require_once __DIR__ . '/../../../../../src/core/assembly.php';
require_once __DIR__ . '/../../../../../src/core/isingleton.php';
require_once __DIR__ . '/../../../../../src/data/isource.php';
require_once __DIR__ . '/../../../../../src/application/idao.php';
require_once __DIR__ . '/../../../../../src/application/dao/dao.php';
require_once __DIR__ . '/../../../../../src/application/dao/cache/dao.php';
require_once __DIR__ . '/../../../../../src/application/iconfiguration.php';
require_once __DIR__ . '/../../../../../src/application/configuration/configuration.php';
require_once __DIR__ . '/../../../../../src/data/ikv.php';
require_once __DIR__ . '/../../../../../src/data/kv/kv.php';
require_once __DIR__ . '/../../../../../src/data/kv/memcache.php';

require_once __DIR__ . '/../../../../../src/core/exception.php';
require_once __DIR__ . '/../../../../../src/application/dao/cache/@exception/datasourceexpected.php';
require_once __DIR__ . '/../../../../../src/application/dao/cache/@exception/invalidcachingdatadomain.php';

use Tox;
use stdClass;

/**
 * Tests Tox\Application\Dao\Cache\Dao.
 *
 * @internal
 *
 * @package tox.application.dao.cache
 * @author  Trainxy Ho <trainxy@gmail.com>
 */
class DaoTest extends PHPUnit_Framework_TestCase
{

    public function testCacheFlowWouldWorkFine()
    {
        $o_mock_dao = $this->getMockBuilder('Tox\\Application\\Dao\\Dao')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $o_mock_dao->expects($this->once())
            ->method('read')
            ->with($this->equalTo('111'))
            ->will($this->returnValue('hello'));

        $s_key = md5(get_class($o_mock_dao) . '-' . '111');

        $o_mock_cache = $this->getMock('Tox\\Data\\KV\\Memcache', array('get', 'set'));
        $o_mock_cache->expects($this->at(0))
            ->method('get')
            ->with($this->equalTo($s_key))
            ->will($this->returnValue(false));
        $o_mock_cache->expects($this->at(2))
            ->method('get')
            ->with($this->equalTo($s_key))
            ->will($this->returnValue('world'));

        $o_cache_dao = $this->getMockBuilder('Tox\\Application\\Dao\\Cache\\Dao')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $o_cache_dao::bindDomain($o_mock_cache);
        $o_cache_dao::getInstance();
        $o_cache_dao->bind($o_mock_dao);

        $this->assertEquals('hello', $o_cache_dao->read('111'));
        $this->assertEquals('world', $o_cache_dao->read('111'));
    }

    public function testTransmitToNormalDaoWhenCreateUpdateDeleteOperationCalled()
    {
        $o_mock_dao = $this->getMockBuilder('Tox\\Application\\Dao\\Dao')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $o_mock_dao->expects($this->once())
            ->method('create')
            ->with($this->equalTo(array('title' => 'hello', 'description' => 'world')))
            ->will($this->returnValue('111'));
        $o_mock_dao->expects($this->once())
            ->method('update')
            ->with(
                $this->equalTo('111'),
                $this->equalTo(array('title' => 'new title'))
            );
        $o_mock_dao->expects($this->once())
            ->method('delete')
            ->with($this->equalTo('111'));

        $s_key = md5(get_class($o_mock_dao) . '-' . '111');
        $o_mock_cache = $this->getMock('Tox\\Data\\KV\\Memcache', array('get', 'set', 'delete'));
        $o_mock_cache->expects($this->once())
            ->method('get')
            ->with($this->equalTo($s_key))
            ->will($this->returnValue(array('id' => '111', 'title' => 'original', 'description' => 'bbb')));
        $o_mock_cache->expects($this->at(0))
            ->method('set')
            ->with(
                $this->equalTo($s_key),
                $this->equalTo(array('id' => '111', 'title' => 'hello', 'description' => 'world')),
                60
            );
        $o_mock_cache->expects($this->at(2))
            ->method('set')
            ->with(
                $this->equalTo($s_key),
                $this->equalTo(array('id' => '111', 'title' => 'new title', 'description' => 'bbb')),
                60
            );

        $o_cache_dao = $this->getMockBuilder('Tox\\Application\\Dao\\Cache\\Dao')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $o_cache_dao::bindDomain($o_mock_cache);
        $o_cache_dao::getInstance();
        $o_cache_dao->bind($o_mock_dao);
        $o_cache_dao->setExpire(60);

        $this->assertEquals('111', $o_cache_dao->create(array('title' => 'hello', 'description' => 'world')));
        $o_cache_dao->update('111', array('title' => 'new title'));
        $o_cache_dao->delete('111');
    }

    public function testTransmitToNormalDaoWhenCountByAndListAndSortByOperationCalled()
    {
        $o_mock_dao = $this->getMockBuilder('Tox\\Application\\Dao\\Dao')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $o_mock_dao->expects($this->once())
            ->method('countBy');
        $o_mock_dao->expects($this->once())
            ->method('listBy');

        $o_mock_cache = $this->getMock('Tox\\Data\\KV\\Memcache', array('get', 'set', 'delete'));

        $o_cache_dao = $this->getMockBuilder('Tox\\Application\\Dao\\Cache\\Dao')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $o_cache_dao::bindDomain($o_mock_cache);
        $o_cache_dao::getInstance();
        $o_cache_dao->bind($o_mock_dao);

        $o_cache_dao->countBy();
        $o_cache_dao->listBy();
    }

    public function testMethodNotImplementedWouldTransmitedBy()
    {
        $o_mock_dao = $this->getMock(
            'Tox\\Application\\Dao\\Dao',
            array('countBy', 'create', 'delete', 'update', 'listBy', 'read', 'listAndSortByCategoryAndCountry')
        );

        $o_mock_dao->expects($this->once())
            ->method('listAndSortByCategoryAndCountry')
            ->with($this->equalTo(array('category', 'country')))
            ->will($this->returnValue('list'));

        $o_cache_dao = $this->getMockBuilder('Tox\\Application\\Dao\\Cache\\Dao')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $o_cache_dao::getInstance();
        $o_cache_dao->bind($o_mock_dao);
        $this->assertEquals('list', $o_cache_dao->listAndSortByCategoryAndCountry(array('category', 'country')));
    }

    public function testGetsExpireTimeByRightOrder()
    {
        $o_mock_dao = $this->getMock(
            'Tox\\Application\\Dao\\Dao',
            array('countBy', 'create', 'delete', 'update', 'listBy', 'read', 'listAndSortByCategoryAndCountry')
        );
        $o_cache_dao = $this->getMockBuilder('Tox\\Application\\Dao\\Cache\\Dao')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $o_cache_dao::getInstance();
        $o_cache_dao->bind($o_mock_dao);
        $this->assertEquals('3600', $o_cache_dao->getExpire());

        $a_configs = array(
            'tox.application.dao.cache.dao' => array(
                get_class($o_mock_dao) => '7200'
            )
        );

        $o_mock_config = $this->getMockBuilder('Tox\\Application\\Configuration\\Configuration')
            ->disableOriginalConstructor()
            ->getMock();
        $o_mock_config->expects($this->any())
            ->method('offsetGet')
            ->will(
                $this->returnCallBack(
                    function ($key) use ($a_configs) {
                        return $a_configs[$key];
                    }
                )
            );

        $o_mock_config->expects($this->any())
            ->method('offsetExists')
            ->with($this->equalTo('tox.application.dao.cache.dao'))
            ->will($this->returnValue(true));

        $o_cache_dao::config($o_mock_config);
        $this->assertEquals('7200', $o_cache_dao->getExpire());

        $o_cache_dao->setExpire('9600');
        $this->assertEquals('9600', $o_cache_dao->getExpire());
    }
}
// vi:ft=php fenc=utf-8 ff=unix ts=4 sts=4 et sw=4 fen fdm=indent fdl=1 tw=120
