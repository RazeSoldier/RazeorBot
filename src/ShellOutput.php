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
 * Used to output a string to shell
 * @package Razeor
 */
class ShellOutput
{
    public const RED = 1;
    public const GREEN = 2;
    public const YELLOW = 3;
    public const COLOR = [
        1 => '31m',
        2 => '32m',
        3 => '33m'
    ];

    private static function preHandle(string &$str, int $color = null) : void
    {
        if ( PHP_OS === 'WINNT' || $color === null ) {
            return;
        }
        if ( !isset( self::COLOR[$color] ) ) {
            throw new \LogicException( "Undefined color index: $color" );
        }
        $str = "\033[" . self::COLOR[$color] . $str . " \033[0m";
    }

    /**
     * Print $str to the screen
     * @param string $str
     * @param int|null $color
     */
    public static function println(string $str, int $color = null) : void
    {
        self::preHandle( $str, $color );
        echo "$str\n";
    }
}