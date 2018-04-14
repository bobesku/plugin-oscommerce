<?php
/**
 * Aldrapay Payment Module version 1.1.0 for osCommerce 2.3.x. Support contact : support@aldrapay.com
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 *
 * @author    Aldrapay (https://www.aldrapay.com/)
 * @copyright 2014-2018 Aldrapay
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html  GNU General Public License (GPL v2)
 * @category  payment
 * @package   aldrapay
 */

/**
 * This file is an access point for the Aldrapay IPN (gateway notifications)
 */
require_once('includes/configure.php');

require_once (DIR_FS_CATALOG . 'includes/classes/aldrapay_webhook_response.php');
$aldrapay_response = new AldrapayWebhookResponse();

// check authenticity and valid data
if (!$aldrapay_response->isValid() || empty($aldrapay_response->getUid())
		|| empty($aldrapay_response->getTrackingId())) {
	echo 'NOK';
	exit();
}


$_COOKIE['__call_from_server__'] = 'yes';
$cartRef = $aldrapay_response->getTrackingId();

$osCsid = substr($cartRef, strrpos($cartRef,'-')+1);

$_POST['osCsid'] = $osCsid;
$_GET['osCsid'] = $osCsid;

// for cookie based sessions ...
$_COOKIE['osCsid'] = $osCsid;
$_COOKIE['cookie_test'] = 'please_accept_for_session';

require_once 'checkout_process.php';
