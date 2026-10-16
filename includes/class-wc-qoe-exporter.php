<?php
if (!defined('ABSPATH')) {
    exit;
}

class WC_QOE_Exporter {
    public function export($start_date, $end_date, $status) {
        $args = array(
            'limit' => -1,
            'date_created' => $start_date . '...' . $end_date,
        );

        if ($status !== 'any') {
            $args['status'] = $status;
        }

        $orders = wc_get_orders($args);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=woocommerce-orders-' . date('Y-m-d') . '.csv');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        fputcsv($output, array(
            'Order ID',
            'Order Date',
            'Status',
            'Billing Name',
            'Billing Email',
            'Billing Phone',
            'Order Total',
            'Payment Method',
            'Items'
        ));

        if (!empty($orders)) {
            foreach ($orders as $order) {
                $items = $order->get_items();
                $item_names = array();
                
                foreach ($items as $item) {
                    $item_names[] = $item->get_name() . ' (x' . $item->get_quantity() . ')';
                }

                fputcsv($output, array(
                    $order->get_id(),
                    $order->get_date_created() ? $order->get_date_created()->date('Y-m-d H:i:s') : '',
                    wc_get_order_status_name($order->get_status()),
                    $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
                    $order->get_billing_email(),
                    $order->get_billing_phone(),
                    $order->get_total(),
                    $order->get_payment_method_title(),
                    implode(' | ', $item_names)
                ));
            }
        }

        fclose($output);
        exit;
    }
}
