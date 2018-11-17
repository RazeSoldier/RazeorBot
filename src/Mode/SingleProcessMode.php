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
 * A running mode that only using single-process to handle tasks
 * @package Razeor\Mode
 */
class SingleProcessMode extends MultiTaskMode
{
    public function run()
    {
        $runQueue = [];
        while ( true ) {
            $this->syncTaskList();
            $currentTime = microtime( true );
            $upcomingTask = $this->taskManager->getUpcomingTasks( $currentTime, $this->waitTime );

            if ( $upcomingTask === null && $runQueue === [] ) {
                sleep( $this->waitTime );
                continue;
            }

            // First, determine if there is a conflicting task
            if ( count( $conflictTask = array_keys( $upcomingTask, current( $upcomingTask ) ) ) >= 2 ) {
                unset( $conflictTask[array_search(key($upcomingTask), $conflictTask)] );
                // All conflicting tasks will be placed at the beginning of the queue
                foreach ( $conflictTask as $task ) {
                    array_unshift( $runQueue, $task );
                }
            }

            time_sleep_until( current( $upcomingTask ) );

            // Run upcoming task
            $processBeforeTime = microtime( true );
            $this->taskManager->runTask( key( $upcomingTask ) );
            $processAfterTime = microtime( true );
            $missTask = $this->taskManager->getUpcomingTasks( $processBeforeTime, $processAfterTime - $processBeforeTime );
            if ( $missTask !== null ) {
                unset( $missTask[key($upcomingTask)] );
                foreach ( $conflictTask as $task ) {
                    unset( $missTask[$task] );
                }
                foreach ( array_keys( $missTask ) as $task ) {
                    array_unshift( $runQueue, $task );
                }
            }

            # Process the tasks in the queue @{
            if ( $runQueue !== [] ) {
                $processBeforeTime = microtime( true );
                foreach ( $runQueue as $taskName ) {
                    $this->taskManager->runTask( $taskName );
                }
                $processAfterTime = microtime( true );
                $missTask = $this->taskManager->getUpcomingTasks( $processBeforeTime, $processAfterTime - $processBeforeTime );
                $runQueue = $missTask === null ? [] : $missTask;
            }
            # @}
        }
    }
}
