<?php

namespace BrjupoKo\BasicConfigUsingAlias\Controller\BasicConfigUsingAlias;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * Controller for the 'brjupoko/basicconfigusingalias/index' URL route.
 */
class Index implements HttpGetActionInterface
{
    protected $resultPageFactory;

    /**
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        PageFactory $resultPageFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        return $this->resultPageFactory->create();
    }
}
