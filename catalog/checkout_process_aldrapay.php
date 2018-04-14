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
 * This file is an access point for the Aldrapay payment gateway to validate an order.
 */

    
    require_once('includes/application_top.php');

    global $aldrapay_response, $language, $messageStack;

    require_once (DIR_FS_CATALOG . 'includes/modules/payment/aldrapay.php');
    $paymentObject = new aldrapay();

    require_once (DIR_FS_CATALOG . 'includes/classes/aldrapay_webhook_response.php');
    $aldrapay_response = new AldrapayWebhookResponse(
            constant($paymentObject->prefix . 'PASS_CODE'),
            constant($paymentObject->prefix . 'PSIGN_ALGO')
    );

    // check authenticity and valid data
	if (!$aldrapay_response->isAuthorized() || !$aldrapay_response->isValid() || empty($aldrapay_response->getUid())) {
        $messageStack->add_session('header', MODULE_PAYMENT_ALDRAPAY_TECHNICAL_ERROR, 'error');

        tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true));
    }
    
    // keep track of originating source (app needs to know further if this is a gateway notification or not) 
    $_COOKIE['__call_from_server__'] = 'no';

    if ($paymentObject->_is_order_paid()) {
        // messages to display on payment result page
        if (constant($paymentObject->prefix . 'CTX_MODE') == 'TEST') {
        	$messageStack->add_session('header', MODULE_PAYMENT_ALDRAPAY_GOING_INTO_PROD_INFO . ' <a href="https://secure.aldrapay.com/backoffice/docs/api/testing.html" target="_blank">https://secure.aldrapay.com/backoffice/docs/api/testing.html</a>', 'success');
        }
        
        tep_redirect(tep_href_link(FILENAME_CHECKOUT_SUCCESS, '', 'SSL', true));
    } else {

    	tep_redirect(tep_href_link(FILENAME_CHECKOUT_PROCESS, http_build_query($aldrapay_response->getResponseArray()), 'SSL', true));
    }

