<?php

namespace XLiteWeb\tests;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use XLiteWeb\CheckoutTrait;


/**
 * @author cerber
 */
class testOrderMessagesTest extends \XLiteWeb\AXLiteWeb
{
    /**
     *
     * @var  RemoteWebDriver
     */
    protected $driver;

    use CheckoutTrait;

    public function testAddOrderCustomer()
    {

        $this->placeOrder(37);
        $this->placeOrder(1);
        $this->placeOrder(2);

    }

    public function testAddOrderMessageCustomer()
    {
        $customOrders = $this->CustomerOrders;
        $customOrders->load(true);

        $this->addOrderMessageCustomer(3,1);
        $this->addOrderMessageCustomer(2,2);
        $this->addOrderMessageCustomer(1,2);

    }

    public function testAddOrderMessageAdmin()
    {

        $customOrders = $this->CustomerOrders;
        $customOrders->load(true);

        $this->addOrderMessageAdmin(3);
        $this->addOrderMessageAdmin(2);
        $this->addOrderMessageAdmin(1);

    }

    public function testMarkReadOnAdminOrderMessagePage()
    {

        $adminMessages = $this->AdminMessages;
        $adminMessages->load(true);

        $this->assertTrue($adminMessages->validate(), 'This is not Admin Messagess page.');

        $adminMessages->getMarkAll(1);

        $this->assertFalse($adminMessages->getTextUnReadMessage(), 'Error validation Message.');

    }

    public function testMarkUnreadOnAdminOrderMessagePage()
    {

        $adminMessages = $this->AdminMessages;
        $adminMessages->load(false);

        $this->assertTrue($adminMessages->validate(), 'This is not Admin Messagess page.');

        $adminMessages->getMarkAll(2);

        $message = '4 new messages for order';

        $this->assertEquals($adminMessages->getTextUnReadMessage(), $message, 'Error validation Message.');

    }

    public function testSearchOnAdminMessagePage()
    {
        $adminMessages = $this->AdminMessages;
        $adminMessages->load(true);

        $message = "2 - 7";
        //$message = "2";

        $adminMessages->inputMessageSearch($message);

        $searchMessagesArray = $adminMessages->searchMessage();

        $idLink = 1;

        foreach ($searchMessagesArray as $searchMessage) {

            $adminMessages->linkMessage(count($searchMessagesArray), $idLink);

            $adminOrderMessages = $this->AdminOrderMessages;
            $adminOrderMessages->isSearchOrderMessage($message);

            $this->assertTrue($adminOrderMessages->isSearchOrderMessage($message), 'This is not Search substr.');

            $adminMessages = $this->AdminMessages;
            $adminMessages->load(false);

            $idLink = $idLink + 1;

        }

    }

    public function testCommunicationMarkUnreadOnAdminOrderMessagePage()
    {

        $adminMessages = $this->AdminMessages;
        $adminMessages->load(true);

        $this->assertTrue($adminMessages->validate(), 'This is not Admin Messagess page.');

        $adminMessages->getMarkAll(2);

        $message = '4 new messages for order';

        $this->assertEquals($adminMessages->getTextUnReadMessage(), $message, 'Error validation Message.');

        $adminMessages->markMessage(2);

        $adminMessages->getMarkAll(1);

        $adminMessages->selectMenuCommunication();

        $adminMessages->buttonSubmit();

        $searchMessagesArray = $adminMessages->searchMessage();

        foreach ($searchMessagesArray as $searchMessage) {

            $this->assertEquals($adminMessages->getTextUnReadMessage(), $message, 'Error validation Message.');
            $this->assertEquals($searchMessage->getAttribute("class"), "message unread", 'Error validation Message.');

        }

    }

    public function addOrderMessageCustomer($parOrderId,$recordNumber)
    {

        $customOrders = $this->CustomerOrders;
        $customOrders->load(false);

        $nameOrder = $customOrders->nameOrder($parOrderId);
        $customOrders->linkContactSeller($parOrderId,true);

        $customOrder = $this->CustomerOrder;

        $messageN1 = "New Message 1 - " . $nameOrder;

        $customOrder->inputNewMessageBody($messageN1);
        $customOrder->buttonSubmit();

        $messageN2 = "New Message 2 - " . $nameOrder;

        $customOrder->inputNewMessageBody($messageN2);
        $customOrder->buttonSubmit();

        $this->assertEquals($customOrder->getTextMessageOrder(1), $messageN1, 'Error validation Order Message.');
        $this->assertEquals($customOrder->getTextMessageOrder(2), $messageN2, 'Error validation Order Message.');

        //верность инфы на странице admin.php?target=messages

        $adminOrderMessages = $this->AdminOrderMessages;

        if ($recordNumber === 1) {

            $adminOrderMessages->loadidOrderId(true,$nameOrder);

            $this->assertEquals($customOrder->getTextMessageOrder(1), $adminOrderMessages->getTextMessageOrder(1), 'Error validation Order Message.');
            $this->assertEquals($customOrder->getTextMessageOrder(2), $adminOrderMessages->getTextMessageOrder(2), 'Error validation Order Message.');

        } else {

            $adminOrderMessages->loadidOrderId(false,$nameOrder);

            $this->assertEquals($customOrder->getTextMessageOrder(1), $adminOrderMessages->getTextMessageOrder(2), 'Error validation Order Message.');
            $this->assertEquals($customOrder->getTextMessageOrder(2), $adminOrderMessages->getTextMessageOrder(3), 'Error validation Order Message.');

        }

    }

    public function addOrderMessageAdmin($parOrderId)
    {

        $customOrders = $this->CustomerOrders;
        $customOrders->load(false);

        $nameOrder = $customOrders->nameOrder($parOrderId);

        $adminOrderMessages = $this->AdminOrderMessages;
        $adminOrderMessages->loadidOrderId(true,$nameOrder);

        $messageN1 = "Admin New Message 1 - " . $nameOrder;

        $adminOrderMessages->inputNewMessageBody($messageN1);
        $adminOrderMessages->buttonSubmit();

        $messageN2 = "Admin New Message 2 - " . $nameOrder;

        $adminOrderMessages->inputNewMessageBody($messageN2);
        $adminOrderMessages->buttonSubmit();

        $this->assertEquals($adminOrderMessages->getTextMessageOrder(3), $messageN1, 'Error validation Order Message.');
        $this->assertEquals($adminOrderMessages->getTextMessageOrder(4), $messageN2, 'Error validation Order Message.');

        //верность инфы на странице Кастомера

        $customOrders = $this->CustomerOrders;
        $customOrders->load(false);

        $customOrders->linkContactSeller($parOrderId,false);

        $customOrder = $this->CustomerOrder;

        $this->assertEquals($customOrder->getTextMessageOrder(4), $adminOrderMessages->getTextMessageOrder(3), 'Error validation Order Message.');
        $this->assertEquals($customOrder->getTextMessageOrder(5), $adminOrderMessages->getTextMessageOrder(4), 'Error validation Order Message.');

    }

}
