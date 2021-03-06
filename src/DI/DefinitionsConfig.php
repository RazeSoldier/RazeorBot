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

use function DI\{
    create,
    factory,
    get,
};

return [
    'LineFormatter' => create( Monolog\Formatter\LineFormatter::class )
        ->constructor( "[%datetime%] (%level_name%) > %message%\n",
            'Y-m-d H:i:s:u' ),
    'Logger' => create( Monolog\Logger::class )->constructor( 'Main' )
        ->method( 'pushHandler', get( 'StreamHandler' ) ),
    'Mode' => factory( [ \Razeor\Mode\ModeFactory::class, 'make' ] ),
    'StreamHandler' => create( Monolog\Handler\StreamHandler::class )
        ->constructor( ROOT_PATH . '/razeor.log' )
        ->method( 'setFormatter', get( 'LineFormatter' ) ),
];
