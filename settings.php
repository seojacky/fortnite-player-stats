<?php
/**
 * Fortnite Player Stats - Settings
 * 
 * Файл содержит код для работы со страницами настроек и отладки плагина
 */

// Запрет прямого доступа к файлу
if (!defined('ABSPATH')) {
    exit;
}

// Регистрация настроек плагина
function fortnite_stats_register_settings() {
    add_option('fortnite_stats_api_key', '');
    add_option('fortnite_stats_cache_time', '900'); // 15 минут кэширования по умолчанию
    add_option('fortnite_stats_debug_mode', '0'); // Режим отладки выключен по умолчанию
    
    register_setting('fortnite_stats_options_group', 'fortnite_stats_api_key');
    register_setting('fortnite_stats_options_group', 'fortnite_stats_cache_time');
    register_setting('fortnite_stats_options_group', 'fortnite_stats_debug_mode');
}
add_action('admin_init', 'fortnite_stats_register_settings');

// Добавление страницы настроек в меню
function fortnite_stats_add_menu() {
    add_options_page(
        __('Fortnite Stats Settings', 'fortnite-stats-wp'),
        __('Fortnite Stats', 'fortnite-stats-wp'),
        'manage_options',
        'fortnite-stats',
        'fortnite_stats_settings_page'
    );
}
add_action('admin_menu', 'fortnite_stats_add_menu');

// Страница настроек плагина
function fortnite_stats_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php _e('Fortnite Stats Settings', 'fortnite-stats-wp'); ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields('fortnite_stats_options_group'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="fortnite_stats_api_key"><?php _e('API Key', 'fortnite-stats-wp'); ?></label></th>
                    <td><input type="text" id="fortnite_stats_api_key" name="fortnite_stats_api_key" value="<?php echo esc_attr(get_option('fortnite_stats_api_key')); ?>" class="regular-text" required /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="fortnite_stats_cache_time"><?php _e('Cache Time (seconds)', 'fortnite-stats-wp'); ?></label></th>
                    <td><input type="number" id="fortnite_stats_cache_time" name="fortnite_stats_cache_time" value="<?php echo esc_attr(get_option('fortnite_stats_cache_time')); ?>" class="regular-text" required /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="fortnite_stats_debug_mode"><?php _e('Debug Mode', 'fortnite-stats-wp'); ?></label></th>
                    <td>
                        <label for="fortnite_stats_debug_mode">
                            <input type="checkbox" id="fortnite_stats_debug_mode" name="fortnite_stats_debug_mode" value="1" <?php checked(get_option('fortnite_stats_debug_mode'), '1'); ?> />
                            <?php _e('Enable debug logs', 'fortnite-stats-wp'); ?>
                        </label>
                    </td>
                </tr>
            </table>
            <p class="description">
                <?php _e('Enter API Key for access to Fortnite-API.com. You can get the key at', 'fortnite-stats-wp'); ?> <a href="https://fortnite-api.com/" target="_blank">Fortnite-API.com</a>.
            </p>
            <p><strong><?php _e('Note:', 'fortnite-stats-wp'); ?></strong> <?php _e('Cache time is specified in seconds. A value between 300 (5 minutes) and 3600 (1 hour) is recommended to reduce load on the API.', 'fortnite-stats-wp'); ?></p>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Добавление страницы отладки (только для администраторов)
function fortnite_stats_debug_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Очистка логов, если запрошено
    if (isset($_POST['clear_logs']) && check_admin_referer('fortnite_stats_clear_logs')) {
        delete_option('fortnite_stats_debug_logs');
        echo '<div class="notice notice-success"><p>' . __('Debug logs cleared successfully.', 'fortnite-stats-wp') . '</p></div>';
    }
    
    // Получение логов
    $logs = get_option('fortnite_stats_debug_logs', array());
    ?>
    <div class="wrap">
        <h1><?php _e('Fortnite Stats Debug Logs', 'fortnite-stats-wp'); ?></h1>
        
        <form method="post">
            <?php wp_nonce_field('fortnite_stats_clear_logs'); ?>
            <input type="submit" name="clear_logs" class="button button-secondary" value="<?php _e('Clear Logs', 'fortnite-stats-wp'); ?>">
        </form>
        
        <div class="log-container" style="margin-top: 20px; background: #f8f9fa; padding: 15px; border-radius: 5px;">
            <?php if (empty($logs)): ?>
                <p><?php _e('No logs available.', 'fortnite-stats-wp'); ?></p>
            <?php else: ?>
                <?php foreach (array_reverse($logs) as $entry): ?>
                    <div class="log-entry <?php echo esc_attr($entry['type']); ?>" style="margin-bottom: 10px; padding: 10px; border-radius: 4px; background: <?php echo ($entry['type'] === 'error') ? '#ffebee' : '#f5f5f5'; ?>;">
                        <div>[<?php echo esc_html($entry['time']); ?>] <strong><?php echo strtoupper(esc_html($entry['type'])); ?></strong>: <?php echo esc_html($entry['message']); ?></div>
                        <?php if (!empty($entry['data'])): ?>
                            <pre style="margin: 5px 0 0; padding: 8px; background: rgba(255,255,255,0.7); font-size: 12px; overflow-x: auto;"><?php echo esc_html(is_string($entry['data']) ? $entry['data'] : json_encode($entry['data'], JSON_PRETTY_PRINT)); ?></pre>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

// Добавление страницы отладки в меню
function fortnite_stats_add_debug_menu() {
    if (get_option('fortnite_stats_debug_mode') == '1' && current_user_can('manage_options')) {
        add_submenu_page(
            'options-general.php',
            __('Fortnite Stats Debug', 'fortnite-stats-wp'),
            __('Fortnite Stats Debug', 'fortnite-stats-wp'),
            'manage_options',
            'fortnite-stats-debug',
            'fortnite_stats_debug_page'
        );
    }
}
add_action('admin_menu', 'fortnite_stats_add_debug_menu');