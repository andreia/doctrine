<?php

namespace Doctrine\Tests\ORM\Functional;

use Doctrine\Tests\Models\ECommerce\ECommerceCart;
use Doctrine\Tests\Models\ECommerce\ECommerceCustomer;
use Doctrine\Tests\Models\ECommerce\ECommerceProduct;
use Doctrine\ORM\Mapping\AssociationMapping;

require_once __DIR__ . '/../../TestInit.php';

/**
 * Tests capabilities of the persister.
 * @author Giorgio Sironi <piccoloprincipeazzurro@gmail.com>
 */
class StandardEntityPersisterTest extends \Doctrine\Tests\OrmFunctionalTestCase
{
    protected function setUp()
    {
        $this->useModelSet('ecommerce');
        parent::setUp();
    }

    public function testAcceptsForeignKeysAsCriteria()
    {
        $this->_em->getConfiguration()->setAllowPartialObjects(false);
        
        $customer = new ECommerceCustomer();
        $customer->setName('John Doe');
        $cart = new ECommerceCart();
        $cart->setPayment('Credit card');
        $customer->setCart($cart);
        $this->_em->persist($customer);
        $this->_em->flush();
        $this->_em->clear();
        unset($cart);
        
        $persister = $this->_em->getUnitOfWork()->getEntityPersister('Doctrine\Tests\Models\ECommerce\ECommerceCart');
        $newCart = new ECommerceCart();
        $persister->load(array('customer_id' => $customer->getId()), $newCart);
        $this->assertEquals('Credit card', $newCart->getPayment());
    }

    public function testAcceptsJoinTableAsCriteria()
    {
        $this->_em->getConfiguration()->setAllowPartialObjects(false);

        $cart = new ECommerceCart();
        $product = new ECommerceProduct();
        $product->setName('Star Wars: A New Hope');
        $cart->addProduct($product);
        $this->_em->persist($cart);
        $this->_em->flush();
        $this->_em->clear();
        unset($product);

        $persister = $this->_em->getUnitOfWork()->getEntityPersister('Doctrine\Tests\Models\ECommerce\ECommerceProduct');
        $newProduct = new ECommerceProduct();
        $criteria = array(
            array(
                'table' => 'ecommerce_carts_products',
                'join' => array('id' => 'product_id'),
                'criteria' => array('cart_id' => $cart->getId())
            )
        );
        $persister->load($criteria, $newProduct);
        $this->assertEquals('Star Wars: A New Hope', $newProduct->getName());
    }
}
