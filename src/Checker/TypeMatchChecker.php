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

namespace Razeor\Checker;

/**
 * Used to check a variable data type match expect
 * @package Razeor\Checker
 */
class TypeMatchChecker implements IChecker
{
    /**
     * @var mixed The data to be detected
     */
    private $data;

    /**
     * @var string|string[] expect value
     */
    private $expect;

    /**.
     * @param $data mixed Data to be detected
     * @param $expect string|string[] If $data is an array, please pass string[]
     */
    public function __construct($data, $expect)
    {
        if ( is_array( $data ) && !is_array( $expect ) ) {
            throw new \RuntimeException( '$expect must be an array' );
        }
        if ( !is_array( $data ) && is_array( $expect ) ) {
            throw new \RuntimeException( '$expect must be a string' );
        }
        $this->data = $data;
        $this->expect = $expect;
    }

    public function check() : bool
    {
        if ( is_array( $this->data ) ) {
            foreach ( $this->expect as $key => $requireType ) {
                if ( is_array( $requireType ) ) {
                    // TODO
                } else {
                    if ( array_key_exists( $key, $this->data ) ) {
                        if ( !$this->checkType( $requireType, $this->data[$key] ) ) {
                            return false;
                        }
                    } else {
                        return false;
                    }
                }
            }
        } else {
            return $this->checkType( $this->expect, $this->data );
        }
    }

    private function checkType(string $type, $value) : bool
    {
        switch ( $type ) {
            case 'string':
                return is_string( $value );
            default:
                throw new \LogicException( "Unknown type: $type" );
        }
    }
}