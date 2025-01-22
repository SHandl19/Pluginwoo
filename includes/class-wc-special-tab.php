<?php
if (!defined('ABSPATH')) exit; // Sicherheitscheck

class WC_Special_Tab {
    public function __construct() {
        add_filter('woocommerce_product_data_tabs', [$this, 'add_special_product_tab']);
        add_action('woocommerce_product_data_panels', [$this, 'special_product_tab_content']);
        add_action('woocommerce_process_product_meta', [$this, 'save_special_product_data']);
    }

    /**
     * Fügt den Spezial-Varianten-Reiter in die Produkt-Bearbeitung ein
     */
    public function add_special_product_tab($tabs) {
        $tabs['special_product_tab'] = [
            'label' => __('Spezial-Varianten', 'woocommerce'),
            'target' => 'special_product_data',
            'class'  => ['show_if_special_product']
        ];
        return $tabs;
    }

    /**
     * Zeigt die Spezial-Varianten-Tabelle im Admin-Panel
     */
    public function special_product_tab_content() {
        global $post;
        ?>
        <div id="special_product_data" class="panel woocommerce_options_panel">
            <p><?php _e('Definiere Preis, Lagerstatus und Menge für Varianten.', 'woocommerce'); ?></p>
            <table class="widefat fixed" id="special-variants-table">
                <thead>
                    <tr>
                        <th><?php _e('Wert', 'woocommerce'); ?></th>
                        <th><?php _e('Preis', 'woocommerce'); ?></th>
                        <th><?php _e('Auf Lager', 'woocommerce'); ?></th>
                        <th><?php _e('Menge', 'woocommerce'); ?></th>
                    </tr>
                </thead>
                <tbody id="special-variants-body">
                    <?php $this->render_variants_table($post->ID); ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    /**
     * Generiert die Varianten-Tabelle
     */
    public function render_variants_table($post_id) {
        $product = wc_get_product($post_id);
        $attributes = $product->get_attributes();
        $variants = get_post_meta($post_id, '_special_variants', true) ?: [];

        foreach ($attributes as $attribute) {
            if (!$attribute->is_taxonomy()) continue;
            $terms = wc_get_product_terms($post_id, $attribute->get_name(), ['fields' => 'names']);

            foreach ($terms as $value) {
                $price = $variants[$value]['price'] ?? '';
                $stock = $variants[$value]['stock'] ?? 'outofstock';
                $quantity = $variants[$value]['quantity'] ?? 0;

                echo '<tr>';
                echo '<td>' . esc_html($value) . '</td>';
                echo '<td><input type="text" name="special_variants[' . esc_attr($value) . '][price]" value="' . esc_attr($price) . '" /></td>';
                echo '<td><input type="checkbox" name="special_variants[' . esc_attr($value) . '][stock]" value="instock" ' . checked($stock, 'instock', false) . ' /></td>';
                echo '<td><input type="number" name="special_variants[' . esc_attr($value) . '][quantity]" value="' . esc_attr($quantity) . '" min="0" /></td>';
                echo '</tr>';
            }
        }
    }

    /**
     * Speichert die Spezial-Varianten-Daten
     */
    public function save_special_product_data($post_id) {
        if (isset($_POST['special_variants'])) {
            $variants = $_POST['special_variants'];

            foreach ($variants as $key => $data) {
                $stock_status = isset($data['stock']) ? 'instock' : 'outofstock';
                $quantity = isset($data['quantity']) ? intval($data['quantity']) : 0;
                
                $variants[$key]['stock'] = $stock_status;
                $variants[$key]['quantity'] = $quantity;
            }

            update_post_meta($post_id, '_special_variants', $variants);
            // 🛠 Spezialvarianten aus der Datenbank holen
$variants = get_post_meta($post_id, '_special_variants', true);

// 🛠 Debugging: Logge die gesamten Varianten-Daten
error_log("🔍 Spezialvarianten für Produkt-ID $post_id: " . print_r($variants, true));

if (!empty($variants) && is_array($variants)) {
    $prices = [];

    // Preise aus den Varianten extrahieren
    foreach ($variants as $key => $data) {
        if (!empty($data['price'])) {
            $prices[] = floatval($data['price']);
        }
    }

    // 🛠 Debugging: Zeige alle gefundenen Preise an
    error_log("📊 Extrahierte Preise für Produkt-ID $post_id: " . print_r($prices, true));

    if (!empty($prices)) {
        // Berechne den niedrigsten und höchsten Preis
        $min_price = min($prices);
        $max_price = max($prices);

        // 🛠 Debugging: Zeige berechnete Preiswerte an
        error_log("💰 Preisbereich für Produkt-ID $post_id: min=$min_price, max=$max_price");

        // Falls nur ein Preis existiert, setze diesen als Hauptpreis
        if ($min_price == $max_price) {
            update_post_meta($post_id, '_price', $min_price);
            update_post_meta($post_id, '_regular_price', $min_price);
            error_log("✅ Einzelpreis für Produkt-ID $post_id gesetzt: $min_price");
        } else {
            // Setze Preisspanne (wie WooCommerce-Varianten)
            update_post_meta($post_id, '_price', "$min_price – $max_price");
            update_post_meta($post_id, '_regular_price', "$min_price – $max_price");
            error_log("✅ Preisspanne für Produkt-ID $post_id gesetzt: $min_price – $max_price");
        }
    } else {
        // Falls keine Preise gesetzt sind, setze auf 0
        update_post_meta($post_id, '_price', 0);
        update_post_meta($post_id, '_regular_price', 0);
        error_log("❌ Kein Preis gefunden – Preis für Produkt-ID $post_id auf 0 gesetzt");
    }
}

        }
    }
}

new WC_Special_Tab();
