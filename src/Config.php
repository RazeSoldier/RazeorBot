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

namespace Razeor;

/**
 * Used to get config value from config file
 * @package Razeor
 */
final class Config
{
    public const FILE_PATH = ROOT_PATH . '/config.php';

    /**
     * @var Config
     */
    private static $instance;

    /**
     * @var string[] Storage configuration data
     */
    private $values = [];

    private function __construct()
    {
        # Read config file @{
        if ( !is_readable( self::FILE_PATH ) ) {
            throw new \RuntimeException( 'Failed to read config file' );
        }
        require_once self::FILE_PATH;
        # @}
        # Get config options @{
        foreach ( get_defined_vars() as $varName => $varValue ) {
            if ( strpos( $varName, 'g' ) === 0 ) {
                $varName = substr( $varName, 1 );
                $configs[$varName] = $varValue;
            }
        }
        if ( !isset( $configs ) ) {
            throw new \LogicException( 'Without config variables in config file' );
        }
        # @}
        $this->values = $configs;
    }

    public static function getInstance() : Config
    {
        if ( self::$instance === null ) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * Try to get a config value
     * @param string $name A config key
     * @return string
     */
    public function get(string $name)
    {
        if ( $this->has( $name ) ) {
            return $this->values[$name];
        }
        throw new \LogicException( "$name config option does not exist" );
    }

    /**
     * Checks if the config exist
     * @param string $name
     * @return bool
     */
    public function has(string $name) : bool
    {
        return ( isset( $this->values[$name] ) ) ? true : false;
    }
}
