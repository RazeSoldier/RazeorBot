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

namespace Razeor\Task;

use Razeor\IChecker;

/**
 * Used to check a task info file
 * @package Razeor\Task
 */
class TaskInfoFileChecker implements IChecker
{
    public const REQUIRE_KEY = [ 'MainClass', 'Time' ];
    /**
     * @var string
     */
    private $filePath;

    public function __construct(string $filePath)
    {
        if ( !is_readable( $filePath ) ) {
            throw new \RuntimeException( "Failed to read $filePath" );
        }
        $this->filePath = $filePath;
    }

    public function check() : bool
    {
        $json = file_get_contents( $this->filePath );
        $arr = json_decode( $json, true );
        if ( $arr === null ) {
            $errMsg = json_last_error_msg();
            throw new \RuntimeException( "Failed to decode the json: $errMsg" );
        }
        // TODO
    }
}