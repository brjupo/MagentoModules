<?php

namespace BrjupoEavAttributes\CustomerAddress\Plugin\Webapi\Magento\Checkout\Model;

use Psr\Log\LoggerInterface as Logger;

use Magento\Framework\App\RequestInterface;

use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Api\CartRepositoryInterface;

class ShippingInformationManagement
{

    protected Logger $logger;
    protected RequestInterface $request;
    protected CartRepositoryInterface $quoteRepository;

    public function __construct(
        Logger                  $logger,
        RequestInterface $request,
        CartRepositoryInterface $quoteRepository,
    )
    {
        $this->logger = $logger;
        $this->request = $request;
        $this->quoteRepository = $quoteRepository;
    }

    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
                                                              $cartId,
                                                              $addressInformation
    )
    {
        try {
            /** @var Quote $quote */
            $quote = $this->quoteRepository->getActive($cartId);
            $this->logger->info(__METHOD__ . ' Colocando Id DireccionesTiendas en el quote: ' . $quote->getId());

            $extAttributes = $addressInformation->getExtensionAttributes();
            $idDireccionestiendas = $extAttributes->getDireccionestiendasId();
            $this->logger->info(__METHOD__ . $idDireccionestiendas);
            $quote->setDireccionestiendasId($idDireccionestiendas);
            // Quote save commented as the Plugin function will do it, just after this
            //$this->quoteRepository->save($quote);
        } catch (\Exception $e) {
            $this->logger->error('Error in plugin: ' . __METHOD__ . ' ' . $e->getMessage());
        }

        return [$cartId, $addressInformation];
    }


}

