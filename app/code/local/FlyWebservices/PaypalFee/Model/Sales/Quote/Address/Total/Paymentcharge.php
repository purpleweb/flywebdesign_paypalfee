<?php

/**
 * @category   FlyWebservices
 * @package    FlyWebservices_PaypalFee
 */
class FlyWebservices_PaypalFee_Model_Sales_Quote_Address_Total_Paymentcharge extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    public function __construct()
    {
        $this->setCode('payment_charge');
    }

    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        $address->setPaymentCharge(0);
        $address->setBasePaymentCharge(0);
        
    	$items = $address->getAllItems();
        if (!count($items)) {
            return $this;
        }
        
        $paymentMethod = $address->getQuote()->getPayment()->getMethod();
		
		if(Mage::getStoreConfig('tax/calculation/price_includes_tax')!=1)
			$tax=$address->getTaxAmount();
		
        if ($paymentMethod) { 
           	$amount1 = Mage::helper('paymentcharge')->getPaymentCharge($paymentMethod, $address->getQuote());
	   		$amount = Mage::helper('directory')->currencyConvert( $amount1, Mage::app()->getWebsite()->getConfig('currency/options/default'), Mage::app()->getStore()->getCurrentCurrencyCode()); 
			
//			if(Mage::getStoreConfig('payment/paypal_payment_solutions/charge_type')!="percentage"){
					$address->setPaymentCharge($amount);
					$address->setBasePaymentCharge($amount1);
/*			} else {
					$address->setPaymentCharge($amount);
				   
					$subTotal = $address->getBaseSubtotal();	
					$amount12 = ($subTotal+ $tax) * floatval(Mage::getStoreConfig('payment/paypal_payment_solutions/charge_value')) / 100;
					$address->setBasePaymentCharge($amount12);
			}*/
        }
		  
		$address->setGrandTotal($address->getGrandTotal() + $tax  + $address->getPaymentCharge());
        $address->setBaseGrandTotal($address->getBaseGrandTotal()+ $address->getBasePaymentCharge());

        return $this;
    } 

    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {	
        if (($address->getBasePaymentCharge() != 0)) {
            $address->addTotal(array(
                'code' => $this->getCode(),
                'title' => Mage::helper('sales')->__('Payment Charge'),
                'full_info' => array(),
                'value' => $address->getPaymentCharge(),
                'base_value'=> $address->getBasePaymentCharge()
               
          ));
        }		 
		  return $address->getBasePaymentCharge();
    }
}