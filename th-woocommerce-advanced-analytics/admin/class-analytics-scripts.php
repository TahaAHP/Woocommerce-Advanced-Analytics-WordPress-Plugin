<?php

class Analytics_Scripts {
    public function __construct() {
        add_action('admin_enqueue_scripts', array($this, 'enqueue_chartjs'));
    }

    public function enqueue_chartjs() {
        wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js', array(), null, true);
    }
}

new Analytics_Scripts();
