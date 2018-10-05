<?php
/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @copyright
 */

namespace Razeor\DI;

class RazeorService
{
    /**
     * @var RazeorService
     */
    private static $instance;

    /**
     * @var \DI\Container
     */
    private $container;

    private function __construct()
    {
        $diMap = require_once SRC_PATH . '/DI/DefinitionsConfig.php';
        $builder = new \DI\ContainerBuilder();
        $builder->addDefinitions( $diMap );
        $this->container = $builder->build();
    }

    public static function getInstance() : RazeorService
    {
        if ( self::$instance === null ) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * Get a object from the container
     * @param string $key
     * @return mixed
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function get(string $key)
    {
        return $this->container->get( $key );
    }
}
