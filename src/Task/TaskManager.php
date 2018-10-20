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
 * Manage all valid tasks
 * @package Razeor\Task
 */
final class TaskManager
{
    /**
     * @var TaskManager
     */
    private static $instance;

    /**
     * @var TaskList
     */
    private $taskList;

    private function __construct()
    {
        $this->taskList = TaskList::getInstance();
    }

    public static function getInstance() : TaskManager
    {
        if ( self::$instance === null ) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function syncTaskList() : void
    {
        $this->taskList->sync();
    }

    /**
     * Get upcoming task names
     * @return string[]|null
     */
    public function getUpcomingTasks(int $currentTime, $timeLimit = 100) :? array
    {
        $upcomingTask = [];
        foreach ( $this->taskList as $taskName => $taskInfo ) {
            $time = $taskInfo['time'];
            if ( $time < $currentTime ) {
                continue;
            }
            if ( $time < $currentTime + $timeLimit ) {
                $upcomingTask[$taskName] = $time;
            }
        }
        return $upcomingTask;
    }
}