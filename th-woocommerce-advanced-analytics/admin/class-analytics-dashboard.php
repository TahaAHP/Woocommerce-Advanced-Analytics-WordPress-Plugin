<?php

class WooCommerce_Advanced_Analytics {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_analytics_dashboard_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    public function add_analytics_dashboard_menu() {
        add_submenu_page(
            'woocommerce',
            'Advanced Analytics',
            'Advanced Analytics',
            'manage_woocommerce',
            'advanced-analytics',
            array($this, 'render_analytics_dashboard')
        );
    }

    public function render_analytics_dashboard() {
        ?>
        <div class="wrap">
            <h1>WooCommerce Advanced Analytics Dashboard</h1>
            <form method="post" action="">
                <label for="start_date">Start Date:</label>
                <input type="date" name="start_date" id="start_date" value="<?php echo date('Y-m-d', strtotime('-1 month')); ?>">
                <label for="end_date">End Date:</label>
                <input type="date" name="end_date" id="end_date" value="<?php echo date('Y-m-d'); ?>">
                <input type="submit" class="button" value="Filter">
            </form>
            <div id="analytics-dashboard">
                <?php $this->display_metrics(); ?>
            </div>

            <h2>Monthly Sales Breakdown</h2>
            <canvas id="salesChart" width="400" height="200"></canvas>
            <script>
                var ctx = document.getElementById('salesChart').getContext('2d');
                var salesChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: <?php echo json_encode(Analytics_Data::get_monthly_labels()); ?>,
                        datasets: [{
                            label: 'Sales ($)',
                            data: <?php echo json_encode(Analytics_Data::get_monthly_sales()); ?>,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 2,
                            fill: false
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            </script>

            <h2>Top Products</h2>
            <ul>
                <?php foreach (Analytics_Data::get_top_products() as $product): ?>
                    <li><?php echo $product->get_name() . ' - ' . wc_price($product->get_total_sales()); ?></li>
                <?php endforeach; ?>
            </ul>

            <h2>Top Customers</h2>
            <ul>
                <?php foreach (Analytics_Data::get_top_customers() as $customer): ?>
                    <li><?php echo $customer->display_name . ' - ' . wc_price($customer->total_spent); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <style>
            #analytics-dashboard {
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;
                margin-bottom: 20px;
            }
            .analytics-box {
                border: 1px solid #ddd;
                border-radius: 5px;
                padding: 20px;
                margin: 10px;
                flex: 1;
                min-width: 200px;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                background: #fff;
            }
            .analytics-box h2 {
                margin-bottom: 10px;
            }
            canvas {
                max-width: 100%;
                margin: 20px 0;
            }
        </style>
        <?php
    }

    private function display_metrics() {
        $total_sales = Analytics_Data::get_total_sales();
        $total_orders = Analytics_Data::get_total_orders();
        $total_customers = Analytics_Data::get_total_customers();
        $total_products = Analytics_Data::get_total_products();
        $average_order_value = Analytics_Data::get_average_order_value();
        
        echo '<div class="analytics-box"><h2>Total Sales</h2><p>' . wc_price($total_sales) . '</p></div>';
        echo '<div class="analytics-box"><h2>Total Orders</h2><p>' . $total_orders . '</p></div>';
        echo '<div class="analytics-box"><h2>Total Customers</h2><p>' . $total_customers . '</p></div>';
        echo '<div class="analytics-box"><h2>Total Products</h2><p>' . $total_products . '</p></div>';
        echo '<div class="analytics-box"><h2>Average Order Value</h2><p>' . wc_price($average_order_value) . '</p></div>';
    }
}
