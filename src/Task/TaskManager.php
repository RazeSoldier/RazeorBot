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

use Razeor\Logger;

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
     * Get the name and the time of the recent task
     * @return string[]|null Returns NULL, if Task list is empty. Or returns an array, includes following keys:
     *  - name task name
     *  - time task start time
     */
    public function getRecentTask() :?array
    {
        if ( $this->taskList->listIsEmpty() ) {
            return null;
        }
        $this->taskList->rewind();
        return [
            'name' => $this->taskList->key(),
            'time' => $this->taskList->current()['time']
        ];
    }

    /**
     * Get upcoming task names
     * @param float $currentTime
     * @param float $timeLimit
     * @return string[]|null
     */
    public function getUpcomingTasks(float $currentTime, float $timeLimit = 100) :? array
    {
        $upcomingTask = null;
        if ( $this->taskList->listIsEmpty() ) {
            return $upcomingTask;
        }
        foreach ( $this->taskList as $taskName => $taskInfo ) {
            $time = $taskInfo['time'];
            if ( $time < $currentTime ) {
                continue;
            }
            if ( $time < $currentTime + $timeLimit ) {
                $upcomingTask[$taskName] = $time;
            }
        }
        if ( is_array( $upcomingTask ) ) {
            asort( $upcomingTask );
        }
        return $upcomingTask;
    }

    public function runTask(string $taskName)
    {
        $startTime = microtime( true );
        Logger::getInstance()->notice( "'$taskName' task start" );
        require_once $this->taskList->getMainClassFile( $taskName );
        /** @var object $main */
        $main = $this->taskList->getMainClass( $taskName );
        $main::run();
        $endTime = microtime( true );
        $duration = $endTime - $startTime;
        Logger::getInstance()->notice( "'$taskName' task finished, duration: $duration s" );
    }
}
