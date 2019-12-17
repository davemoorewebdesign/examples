<?php

namespace DaveM\Test\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Class CustomerLoggedIn
 */
class CustomerLoggedIn implements ObserverInterface
{
	const LAYOUT_HANDLE_NAME = 'customer_logged_in';

    protected $customerSession;
	
    public function __construct(CustomerSession $customerSession)
    {
        $this->customerSession = $customerSession;
    }

    public function execute(Observer $observer)
    {
        $layout = $observer->getEvent()->getLayout();


        if ($this->customerSession->isLoggedIn())
        {
			$layout->getUpdate()->addHandle(static::LAYOUT_HANDLE_NAME);
        }
    }
}