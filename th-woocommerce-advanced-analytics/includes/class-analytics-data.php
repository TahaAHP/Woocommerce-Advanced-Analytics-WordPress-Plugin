<?php

class Analytics_Data {
    public static function get_total_sales() {
        global $wpdb;
        $orders = $wpdb->get_results("
            SELECT SUM(meta_value) AS total
            FROM {$wpdb->postmeta}
            WHERE meta_key = '_order_total'
            AND post_id IN (
                SELECT ID FROM {$wpdb->posts} WHERE post_type = 'shop_order' AND post_status IN ('wc-completed')
            )
        ");
        return isset($orders[0]->total) ? $orders[0]->total : 0;
    }

    public static function get_total_orders() {
        return wp_count_posts('shop_order')->publish;
    }

    public static function get_total_customers() {
        return count(get_users(array('role' => 'customer')));
    }

    public static function get_total_products() {
        return wp_count_posts('product')->publish;
    }

    public static function get_average_order_value() {
        global $wpdb;
        $total = $wpdb->get_var("
            SELECT SUM(meta_value) FROM {$wpdb->postmeta}
            WHERE meta_key = '_order_total'
            AND post_id IN (
                SELECT ID FROM {$wpdb->posts}
                WHERE post_type = 'shop_order' AND post_status = 'wc-completed'
            )
        ");
        $count = self::get_total_orders();
        return $count > 0 ? $total / $count : 0;
    }

    public static function get_monthly_sales() {
        global $wpdb;
        $sales_data = [];
        for ($i = 12; $i >= 1; $i--) {
            $sales_data[] = $wpdb->get_var($wpdb->prepare("
                SELECT SUM(meta_value)
                FROM {$wpdb->postmeta}
                WHERE meta_key = '_order_total'
                AND post_id IN (
                    SELECT ID FROM {$wpdb->posts}
                    WHERE post_type = 'shop_order'
                    AND post_status = 'wc-completed'
                    AND DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL %d MONTH)
                    AND DATE(created_at) < DATE_SUB(CURDATE(), INTERVAL %d - 1 MONTH)
                )
            ", $i, $i));
        }
        return $sales_data;
    }

    public static function get_monthly_labels() {
        return array_map(function($month) {
            return date('F', strtotime("-$month month"));
        }, range(12, 1));
    }

    public static function get_top_products($limit = 5) {
        global $wpdb;
        $results = $wpdb->get_results("
            SELECT p.ID, p.post_title, SUM(oi.meta_value) as total_sales
            FROM {$wpdb->prefix}woocommerce_order_items oi
            JOIN {$wpdb->prefix}posts p ON oi.product_id = p.ID
            JOIN {$wpdb->prefix}posts o ON oi.order_id = o.ID
            WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed'
            AND oi.order_item_type = 'line_item'
            GROUP BY p.ID
            ORDER BY total_sales DESC
            LIMIT $limit
        ");
        return $results;
    }

    public static function get_top_customers($limit = 5) {
        global $wpdb;
        $results = $wpdb->get_results("
            SELECT u.ID, u.display_name, SUM(oi.meta_value) as total_spent
            FROM {$wpdb->prefix}woocommerce_order_items oi
            JOIN {$wpdb->prefix}posts o ON oi.order_id = o.ID
            JOIN {$wpdb->prefix}users u ON o.post_author = u.ID
            WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed'
            AND oi.order_item_type = 'line_item'
            GROUP BY u.ID
            ORDER BY total_spent DESC
            LIMIT $limit
        ");
        return $results;
    }
}
