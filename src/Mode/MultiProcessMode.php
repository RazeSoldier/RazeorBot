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

use Razeor\{
    Logger,
};
use Jenner\SimpleFork\Process;

class MultiProcessMode extends MultiTaskMode
{
    private $runningTask = [];

    public function __construct()
    {
        parent::__construct();
        pcntl_signal( SIGCHLD, [ $this, 'childHandler' ] );
    }

    public function run()
    {
        $runQueue = [];
        while ( true ) {
            pcntl_signal_dispatch();
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
            $this->runTask( key( $upcomingTask ) );

            # Process the tasks in the queue @{
            if ( $runQueue !== [] ) {
                foreach ( $runQueue as $taskName ) {
                    $this->runTask( $taskName );
                }
            }
            # @}
        }
    }

    private function runTask(string $taskName)
    {
        $pro = new Process( function () use ( $taskName ) {
            define( 'IS_CHILD', true );
            $this->taskManager->runTask( $taskName );
        } );
        $pro->start();
        $this->runningTask[$taskName] = $pro->getPid();
    }

    private function childHandler(int $signo)
    {
        switch ( $signo ) {
            case SIGCHLD:
                $pid = pcntl_wait( $status );
                $taskName = array_search( $pid, $this->runningTask );
                if ( $status === 0 ) {
                    Logger::getInstance()->notice( "$taskName process successful exit" );
                } else {
                    Logger::getInstance()->warning( "$taskName process fail exit, exit code: $status" );
                }
        }
    }
}