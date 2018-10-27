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
 * Used to check a task info
 * @package Razeor\Checker
 */
class TaskInfoChecker implements IChecker
{
    public const REQUIRE_KEY = [ 'MainClass' => 'string', 'Time' => 'Date' ];

    private $info;

    public function __construct(array $info)
    {
        $this->info = $info;
    }

    public function check() : bool
    {
        if ( !$this->checkInfoStructure() ) {
            return false;
        }
        if ( !$this->checkInfoValid() ) {
            return false;
        }
        return true;
    }

    private function checkInfoStructure() : bool
    {
        $checker = new ArrayMatchChecker( $this->info, array_keys( self::REQUIRE_KEY ) );
        return $checker->check();
    }

    private function checkInfoValid() : bool
    {
        $checker = new TypeMatchChecker( $this->info, self::REQUIRE_KEY );
        return $checker->check();
    }
}