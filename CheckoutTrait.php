<?php
// vim: set ts=4 sw=4 sts=4 et:

namespace XLiteWeb;
use XLiteTest\Framework\Config;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverDimension;
use Facebook\WebDriver\Remote\DesiredCapabilities;

trait CheckoutTrait
{

    public function placeOrder($productId)
    {

        $address = array(
            'shippingaddress-firstname' => 'User',
            'shippingaddress-lastname'  => 'Userovich',
            'shippingaddress-street'   => '1000 Address',
        );

        $this->clearSession($this->getStorefrontDriver());

        $product = $this->CustomerProduct;

        $product->loadProductId(true,$productId);

        $this->assertTrue($product->validate(), 'Opened page not the product page.');

        $product->addToCart();

        $product->componentMiniCart->open();
        $product->componentMiniCart->openCheckout();

        $checkout = $this->CustomerCheckout;
        $this->assertTrue($checkout->validate(), 'This is not checkout page.');

        $checkout->fillForm($address);

        $checkout->waitForAjax(10);

        $checkout->waitForPlaceOrderButton()->click();

        $invoice = $this->CustomerInvoice;
        $this->assertTrue($invoice->validate(), 'This is not invoice page.');

        $orderId = $invoice->getInvoiceNumber();

        return $orderId;

    }

}
