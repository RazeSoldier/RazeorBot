<?php
/**
 * This file to initialize environment
 * @file
 *
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

define( 'ROOT_PATH', dirname( __DIR__ ) );
define( 'SRC_PATH', __DIR__ );
define( 'MINIMUM_VERSION', '7.2.0' );

# Environmental inspection @{
if ( PHP_SAPI !== 'cli' ) {
    throw new \Error( 'Make sure running under cli mode' );
}

if ( version_compare( PHP_VERSION, MINIMUM_VERSION, '<' ) ) {
    throw new \Error( 'Make sure running under PHP ' . MINIMUM_VERSION  . ' or later version' );
}
# @}

require_once SRC_PATH . '/AutoLoader.php';
spl_autoload_register( [ \Razeor\AutoLoader::class, 'loader' ] );

# Load composer autoload file
if ( !is_readable( $vendorDir = ROOT_PATH . '/vendor/autoload.php' ) ) {
    throw new Error( 'You missing depend' );
}
require_once $vendorDir;
