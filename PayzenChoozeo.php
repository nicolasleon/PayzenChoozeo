<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace PayzenChoozeo;

use Payzen\Model\PayzenConfigQuery;
use Payzen\Payzen;
use Payzen\Payzen\PayzenApi;
use Propel\Runtime\Connection\ConnectionInterface;
use Thelia\Core\HttpFoundation\Response;
use Thelia\Core\Translation\Translator;
use Thelia\Model\Order;

class PayzenChoozeo extends Payzen
{
    const MODULE_DOMAIN = "payzenchoozeo";

    public function postActivation(ConnectionInterface $con = null)
    {
        //Declare postActivation for inherit from Payzen and don't clear payzen data on activation
    }

    /**
     * @return boolean true to allow usage of this payment module, false otherwise.
     */
    public function isValidPayment()
    {
        $valid = false;

        $mode = PayzenConfigQuery::read('mode', false);

        // If we're in test mode, do not display Payzen on the front office, except for allowed IP addresses.
        if ('TEST' == $mode) {

            $raw_ips = explode("\n", PayzenConfigQuery::read('allowed_ip_list', ''));

            $allowed_client_ips = array();

            foreach ($raw_ips as $ip) {
                $allowed_client_ips[] = trim($ip);
            }

            $client_ip = $this->getRequest()->getClientIp();

            $valid = in_array($client_ip, $allowed_client_ips);

        } elseif ('PRODUCTION' == $mode) {
            $valid = true;
        }

        if ($valid) {
            // Check if total order amount is in the module's limits
            $valid = $this->checkMinMaxAmount();
        }

        return $valid;
    }

    public function getLabel()
    {
        $count = PayzenConfigQuery::read('choozeo_number_of_payments', 4);
        return Translator::getInstance()->trans("Pay with Choozeo in '%s' times", ['%s' => $count], PayzenChoozeo::MODULE_DOMAIN);
    }

    /**
     *
     *  Method used by payment gateway.
     *
     *  If this method return a \Thelia\Core\HttpFoundation\Response instance, this response is sent to the
     *  browser.
     *
     *  In many cases, it's necessary to send a form to the payment gateway.
     *  On your response you can return this form already
     *  completed, ready to be sent
     *
     * @param  Order $order processed order
     * @return Response the HTTP response
     */
    public function pay(Order $order)
    {
        return $this->doPay($order, 'SINGLE');
    }

    /**
     * Payment gateway invocation
     *
     * @param Order $order processed order
     * @param string $payment_mode the payment mode, either 'SINGLE' ou 'MULTI'
     * @param string $payment_mean, either SDD (SEPA) or bank cards list
     * @return Response the HTTP response
     * @throws \Exception
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function doPay(Order $order, $payment_mode, $payment_mean = '')
    {
        // Backup current allowed cards values
        $allowed_cards = PayzenConfigQuery::read('allowed_cards');

        if(PayzenConfigQuery::read('choozeo_number_of_payments', 4) == 3) {
            PayzenConfigQuery::set('allowed_cards','EPNF_3X');
        } else {
            PayzenConfigQuery::set('allowed_cards', 'EPNF_4X');
        }

        $payzen_params = $this->getPayzenParameters($order, $payment_mode, $payment_mean);

        // Restore allowed cards values
        PayzenConfigQuery::set('allowed_cards', $allowed_cards);

        // Convert files into standard var => value array
        $html_params = array();

        /** @var PayzenField $field */
        foreach ($payzen_params as $name => $field) {
            $html_params[$name] = $field->getValue();
        }


        // Be sure to have a valid platform URL, otherwise give up
        if (false === $platformUrl = PayzenConfigQuery::read('platform_url', false)) {
            throw new \InvalidArgumentException(
                Translator::getInstance()->trans(
                    "The platform URL is not defined, please check Payzen module configuration.",
                    [],
                    Payzen::MODULE_DOMAIN
                )
            );
        }
        // var_dump($this->generateGatewayFormResponse($order, $platformUrl, $html_params));
        // die();
        return $this->generateGatewayFormResponse($order, $platformUrl, $html_params);
    }

    /**
     * Check if total order amount is in the module's limits
     *
     * @return bool true if the current order total is within the min and max limits
     */
    protected function checkMinMaxAmount()
    {
        // Check if total order amount is in the module's limits
        $order_total = $this->getCurrentOrderTotalAmount();

        $min_amount = PayzenConfigQuery::read('choozeo_minimum_amount', 0);
        $max_amount = PayzenConfigQuery::read('choozeo_maximum_amount', 0);

        return $order_total > 0 &&
        ($min_amount <= 0 || $order_total >= $min_amount) &&
        ($max_amount <= 0 || $order_total <= $max_amount);
    }
}
