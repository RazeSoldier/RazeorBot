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

namespace Razeor\Mode;

use Razeor\Config;

/**
 * This is an abstract class as the parent class of run multi task classes
 * @package Razeor\Mode
 */
abstract class MultiTaskMode extends AbstractMode
{
    /**
     * @var int
     */
    protected $waitTime;

    public function __construct()
    {
        parent::__construct();
        if ( Config::getInstance()->has( 'CheckIntervalTime' ) ) {
            $this->waitTime = Config::getInstance()->get( 'CheckIntervalTime' );
        } else {
            $this->waitTime = 30;
        }
    }

    protected function syncTaskList()
    {
        $this->taskManager->syncTaskList();
    }
}