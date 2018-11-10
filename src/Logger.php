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

use Psr\Log\{
    AbstractLogger,
    InvalidArgumentException
};
use Razeor\DI\RazeorService;

/**
 * Used to logging events
 * @package Razeor
 */
class Logger extends AbstractLogger
{
    /**
     * @var \Monolog\Logger
     */
    private $logger;

    /**
     * @var self
     */
    private static $instance;

    private function __construct()
    {
        $this->logger = RazeorService::getInstance()->get( 'Logger' );
    }

    public static function getInstance() : self
    {
        if ( self::$instance === null ) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function log($level, $message, array $context = [])
    {
        switch ( $level ) {
            case 'emergency';
                $this->logger->emergency( $message, $context );
                break;
            case 'alert':
                $this->logger->alert( $message, $context );
                break;
            case 'critical':
                $this->logger->critical( $message, $context );
                break;
            case 'error':
                $this->logger->error( $message, $context );
                break;
            case 'warning':
                $this->logger->warning( $message, $context );
                break;
            case 'notice':
                $this->logger->notice( $message, $context );
                break;
            case 'info':
                $this->logger->info( $message, $context );
                break;
            case 'debug':
                $this->logger->info( $message, $context );
                break;
            default:
                throw new InvalidArgumentException( "Undefined log level: $level" );
        }
    }

    public function close()
    {
        $this->logger->close();
    }
}