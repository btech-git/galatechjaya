<?php

namespace AppBundle\Listener;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class SecurityListener
{
    private $securityContext;

    public function __construct(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $securityContext = $this->securityContext;
        
        $transactionRowMenu = array(
            'sale_invoice_header',
            'sale_return_header',
            'purchase_order_header',
            'receive_header',
            'purchase_return_header',
            'adjustment_stock_header',
        );
        $transactionRowMenu = array_combine($transactionRowMenu, $transactionRowMenu);
        $callback = function($value) use ($securityContext) {
            return $securityContext->isGranted($this->getTaskRoleNames($value));
        };
        $transactionRowMenu = array_map($callback, $transactionRowMenu);
        $transactionTableMenu = array(
            'sale_transaction' => array(0, 2),
            'purchase_transaction' => array(2, 3),
            'inventory_transaction' => array(5, 1),
        );
        $callback = function($value) use ($transactionRowMenu) {
            return array_reduce(array_slice($transactionRowMenu, $value[0], $value[1]), function($carry, $item) { return $carry || $item; }, false);
        };
        $transactionTableMenu = array_map($callback, $transactionTableMenu);
        
        $financeRowMenu = array(
            'sale_receipt_header',
            'sale_payment_header',
            'sale_cheque',
            'purchase_invoice_header',
            'purchase_receipt_header',
            'purchase_payment_header',
            'deposit_header',
            'expense_header',
            'journal_voucher_header',
        );
        $financeRowMenu = array_combine($financeRowMenu, $financeRowMenu);
        $callback = function($value) use ($securityContext) {
            return $securityContext->isGranted($this->getTaskRoleNames($value));
        };
        $financeRowMenu = array_map($callback, $financeRowMenu);
        $financeTableMenu = array(
            'sale_finance' => array(0, 3),
            'purchase_finance' => array(3, 3),
            'accounting' => array(6, 3),
        );
        $callback = function($value) use ($financeRowMenu) {
            return array_reduce(array_slice($financeRowMenu, $value[0], $value[1]), function($carry, $item) { return $carry || $item; }, false);
        };
        $financeTableMenu = array_map($callback, $financeTableMenu);
        
        $warehouseRowMenu = array(
            'delivery_header',
            'transfer_header',
        );
        $warehouseRowMenu = array_combine($warehouseRowMenu, $warehouseRowMenu);
        $callback = function($value) use ($securityContext) {
            return $securityContext->isGranted($this->getTaskRoleNames($value));
        };
        $warehouseRowMenu = array_map($callback, $warehouseRowMenu);
        $warehouseTableMenu = array(
            'inventory_warehouse' => array(0, 2),
        );
        $callback = function($value) use ($warehouseRowMenu) {
            return array_reduce(array_slice($warehouseRowMenu, $value[0], $value[1]), function($carry, $item) { return $carry || $item; }, false);
        };
        $warehouseTableMenu = array_map($callback, $warehouseTableMenu);
        
        $headerMenu = array(
            'master' => $securityContext->isGranted('ROLE_MASTER'),
            'report' => $securityContext->isGranted('ROLE_REPORT'),
            'staff' => $securityContext->isGranted('ROLE_ADMIN'),
            'transaction' => $transactionTableMenu['sale_transaction'] || $transactionTableMenu['purchase_transaction'] || $transactionTableMenu['inventory_transaction'],
            'finance' => $financeTableMenu['sale_finance'] || $financeTableMenu['purchase_finance'] || $financeTableMenu['accounting'],
            'warehouse' => $warehouseTableMenu['inventory_warehouse'],
        );
        $menu = array_merge($transactionRowMenu, $transactionTableMenu, $financeRowMenu, $financeTableMenu, $warehouseRowMenu, $warehouseTableMenu, $headerMenu);
        
        $session = $event->getRequest()->getSession();
        $session->set('menu', $menu);
        $event->getRequest()->setSession($session);
    }

    private function getTaskRoleNames($module)
    {
        $taskRoleNames = array();
        foreach (array('NEW', 'EDIT', 'DELETE') as $task) {
            $taskRoleNames[] = 'ROLE_' . strtoupper($module) . '_' . $task;
        }
        return $taskRoleNames;
    }
}
