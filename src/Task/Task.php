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

/**
 * Class that timing task mapping
 * @package Razeor\Task
 */
class Task
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $mainClass;

    /**
     * @var float
     */
    private $time;

    public function __construct(string $name, string $mainClass, float $time)
    {
        $this->name = $name;
        $this->mainClass = $mainClass;
        $this->time = $time;
    }

    /**
     * @return float
     */
    public function getTime() : float
    {
        return $this->time;
    }

    /**
     * @return string
     */
    public function getMainClass() : string
    {
        return $this->mainClass;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }
}
