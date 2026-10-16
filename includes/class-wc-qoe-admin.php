<?php
if (!defined('ABSPATH')) {
    exit;
}

class WC_QOE_Admin {
    public function __construct() {
        add_action('admin_menu', array($this, 'register_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
        add_action('admin_init', array($this, 'handle_export_request'));
    }

    public function register_menu() {
        add_submenu_page(
            'woocommerce',
            'Quick Order Exporter',
            'Order Exporter',
            'manage_woocommerce',
            'wc-qoe',
            array($this, 'render_page')
        );
    }

    public function enqueue_assets($hook) {
        if ($hook !== 'woocommerce_page_wc-qoe') {
            return;
        }
        wp_enqueue_style('wc-qoe-admin', WC_QOE_PLUGIN_URL . 'assets/css/admin.css', array(), WC_QOE_VERSION);
    }

    public function render_page() {
        $statuses = wc_get_order_statuses();
        ?>
        <div class="wrap wc-qoe-wrap">
            <h1>WooCommerce Quick Order Exporter</h1>
            <div class="wc-qoe-card">
                <form method="post" action="">
                    <?php wp_nonce_field('wc_qoe_export_nonce', 'wc_qoe_nonce'); ?>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="wc_qoe_start_date">Start Date</label></th>
                            <td><input type="date" name="start_date" id="wc_qoe_start_date" required></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="wc_qoe_end_date">End Date</label></th>
                            <td><input type="date" name="end_date" id="wc_qoe_end_date" required></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="wc_qoe_status">Order Status</label></th>
                            <td>
                                <select name="order_status" id="wc_qoe_status">
                                    <option value="any">Any Status</option>
                                    <?php foreach ($statuses as $key => $label): ?>
                                        <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                    <p class="submit">
                        <input type="submit" name="wc_qoe_export" class="button button-primary" value="Export to CSV">
                    </p>
                </form>
            </div>
        </div>
        <?php
    }

    public function handle_export_request() {
        if (isset($_POST['wc_qoe_export']) && check_admin_referer('wc_qoe_export_nonce', 'wc_qoe_nonce')) {
            if (!current_user_can('manage_woocommerce')) {
                wp_die('Unauthorized');
            }

            $start_date = isset($_POST['start_date']) ? sanitize_text_field($_POST['start_date']) : '';
            $end_date = isset($_POST['end_date']) ? sanitize_text_field($_POST['end_date']) : '';
            $status = isset($_POST['order_status']) ? sanitize_text_field($_POST['order_status']) : 'any';

            $exporter = new WC_QOE_Exporter();
            $exporter->export($start_date, $end_date, $status);
        }
    }
}
