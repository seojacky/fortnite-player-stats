<?php
/**
 * Plugin Name: Fortnite Player Stats 
 * Description: Displays Fortnite player statistics using Fortnite-API.com
 * Version: 2.0
 * Author: seo_jacky
 * Author URI: https://t.me/big_jacky
 * Plugin URI: https://github.com/seojacky/fortnite-stats-wp
 * GitHub Plugin URI: https://github.com/seojacky/fortnite-stats-wp
 * Text Domain: fortnite-stats-wp
 * Domain Path: /languages
 * License: GPL2
 */

// Запрет прямого доступа к файлу
if (!defined('ABSPATH')) {
    exit;
}

// Регистрация стилей и скриптов
function fortnite_stats_enqueue_scripts() {
    wp_enqueue_style('fortnite-stats-style', plugin_dir_url(__FILE__) . 'assets/css/style.css', array(), '2.0.0');
    wp_enqueue_script('fortnite-stats-script', plugin_dir_url(__FILE__) . 'assets/js/script.js', array('jquery'), '2.0.0', true);
    wp_localize_script('fortnite-stats-script', 'fortniteStats', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('fortnite-stats-nonce')
    ));
}
add_action('wp_enqueue_scripts', 'fortnite_stats_enqueue_scripts');

// Регистрация шорткода [fortnite_stats_form]
function fortnite_stats_shortcode() {
    ob_start();
    ?>
    <div class="fortnite-stats-container">
        <h2>Fortnite Player Stats</h2>
        <form id="fortnite-stats-form" class="fortnite-stats-form">
            <div class="form-group">
                <label for="fortnite-username">Username:</label>
                <input type="text" id="fortnite-username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="fortnite-platform">Account Type:</label>
                <select id="fortnite-platform" name="accountType" required>
                    <option value="epic">Epic</option>
                    <option value="psn">PlayStation</option>
                    <option value="xbl">Xbox</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="fortnite-timewindow">Time Window:</label>
                <select id="fortnite-timewindow" name="timeWindow">
                    <option value="lifetime">Lifetime</option>
                    <option value="season">Current Season</option>
                </select>
            </div>
            
            <button type="submit" class="fortnite-stats-submit">Get Stats</button>
        </form>
        
        <div id="fortnite-stats-results" class="fortnite-stats-results"></div>
        <div id="fortnite-stats-error" class="fortnite-stats-error"></div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('fortnite_stats_form', 'fortnite_stats_shortcode');

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
        'Fortnite Stats Settings',
        'Fortnite Stats',
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
        <h1>Fortnite Stats Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('fortnite_stats_options_group'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="fortnite_stats_api_key">API Key</label></th>
                    <td><input type="text" id="fortnite_stats_api_key" name="fortnite_stats_api_key" value="<?php echo esc_attr(get_option('fortnite_stats_api_key')); ?>" class="regular-text" required /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="fortnite_stats_cache_time">Cache Time (seconds)</label></th>
                    <td><input type="number" id="fortnite_stats_cache_time" name="fortnite_stats_cache_time" value="<?php echo esc_attr(get_option('fortnite_stats_cache_time')); ?>" class="regular-text" required /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="fortnite_stats_debug_mode">Debug Mode</label></th>
                    <td>
                        <label for="fortnite_stats_debug_mode">
                            <input type="checkbox" id="fortnite_stats_debug_mode" name="fortnite_stats_debug_mode" value="1" <?php checked(get_option('fortnite_stats_debug_mode'), '1'); ?> />
                            Enable debug logs
                        </label>
                    </td>
                </tr>
            </table>
            <p class="description">
                Введите API Key для доступа к Fortnite-API.com. Вы можете получить ключ на сайте <a href="https://fortnite-api.com/" target="_blank">Fortnite-API.com</a>.
            </p>
            <p><strong>Примечание:</strong> Время кэширования указывается в секундах. Рекомендуется значение от 300 (5 минут) до 3600 (1 час), чтобы уменьшить нагрузку на API.</p>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

/**
 * Логирование для отладки
 */
function fortnite_stats_log($type, $message, $data = null) {
    if (get_option('fortnite_stats_debug_mode') != '1') {
        return;
    }
    
    $log_entry = array(
        'time' => date('Y-m-d H:i:s'),
        'type' => $type,
        'message' => $message,
        'data' => $data
    );
    
    $logs = get_option('fortnite_stats_debug_logs', array());
    $logs[] = $log_entry;
    
    // Ограничение количества записей в логе
    if (count($logs) > 100) {
        $logs = array_slice($logs, -100);
    }
    
    update_option('fortnite_stats_debug_logs', $logs);
}

/**
 * Функция для получения статистики игрока
 */
function fortnite_stats_get_player_stats($username, $accountType, $timeWindow) {
    $api_key = get_option('fortnite_stats_api_key');
    
    if (empty($api_key)) {
        fortnite_stats_log('error', 'API key is not set');
        return array('error' => 'API key is not set. Please configure the plugin in admin settings.');
    }
    
    fortnite_stats_log('info', "Getting stats for player: {$username}, account type: {$accountType}, time window: {$timeWindow}");
    
    // URL для Fortnite API с правильными параметрами
    $url = "https://fortnite-api.com/v2/stats/br/v2?name=" . urlencode($username) . 
           "&accountType=" . urlencode($accountType) . 
           "&timeWindow=" . urlencode($timeWindow);
    
    fortnite_stats_log('debug', 'Stats request URL', $url);
    
    $response = wp_remote_get($url, array(
        'headers' => array(
            'Authorization' => $api_key
        ),
        'timeout' => 15
    ));
    
    if (is_wp_error($response)) {
        fortnite_stats_log('error', 'WP error during stats retrieval', $response->get_error_message());
        return array('error' => "Error: " . $response->get_error_message());
    }
    
    $status = wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);
    
    fortnite_stats_log('debug', 'API response status', $status);
    
    $stats_data = json_decode($body, true);
    
    if ($status != 200 || !isset($stats_data['data'])) {
        $error_message = 'Failed to retrieve stats';
        
        if (isset($stats_data['error'])) {
            if (is_string($stats_data['error'])) {
                $error_message = $stats_data['error'];
            } elseif (isset($stats_data['error']['message'])) {
                $error_message = $stats_data['error']['message'];
            }
        }
        
        fortnite_stats_log('error', 'Stats retrieval failed', array(
            'status' => $status,
            'error' => $error_message,
            'response' => $stats_data
        ));
        
        return array(
            'error' => $error_message,
            'status' => $status
        );
    }
    
    // Форматируем данные статистики
    $formatted_stats = fortnite_stats_format_api_data($stats_data['data']);
    
    fortnite_stats_log('success', 'Stats retrieved successfully', $formatted_stats);
    
    return $formatted_stats;
}

/**
 * Форматирование данных API в структуру для отображения
 */
function fortnite_stats_format_api_data($api_data) {
    $result = array(
        'account' => array(
            'id' => $api_data['account']['id'] ?? '',
            'name' => $api_data['account']['name'] ?? '',
            'level' => $api_data['account']['level'] ?? 0
        ),
        'battlePass' => array(
            'level' => $api_data['battlePass']['level'] ?? 0,
            'progress' => $api_data['battlePass']['progress'] ?? 0
        ),
        'wins' => array(
            'solo' => 0,
            'duo' => 0,
            'squad' => 0,
            'ltm' => 0,
            'total' => 0
        ),
        'matches' => array(
            'solo' => 0,
            'duo' => 0,
            'squad' => 0,
            'ltm' => 0,
            'total' => 0
        ),
        'kd' => array(
            'solo' => 0,
            'duo' => 0,
            'squad' => 0,
            'ltm' => 0,
            'total' => 0
        ),
        'winRate' => array(
            'solo' => 0,
            'duo' => 0,
            'squad' => 0,
            'ltm' => 0,
            'total' => 0
        ),
        'kills' => array(
            'solo' => 0,
            'duo' => 0,
            'squad' => 0,
            'ltm' => 0,
            'total' => 0
        ),
        'deaths' => array(
            'solo' => 0,
            'duo' => 0,
            'squad' => 0,
            'ltm' => 0,
            'total' => 0
        ),
        'minutesPlayed' => array(
            'solo' => 0,
            'duo' => 0,
            'squad' => 0,
            'ltm' => 0,
            'total' => 0
        ),
        'scorePerMatch' => array(
            'solo' => 0,
            'duo' => 0,
            'squad' => 0,
            'ltm' => 0,
            'total' => 0
        ),
        'scorePerMin' => array(
            'solo' => 0,
            'duo' => 0,
            'squad' => 0,
            'ltm' => 0,
            'total' => 0
        ),
        'lastModified' => $api_data['stats']['lastModified'] ?? null
    );
    
    // Обработка статистики для каждого типа ввода и комбинирование
    if (isset($api_data['stats'])) {
        $stats = $api_data['stats'];
        foreach (['keyboardMouse', 'gamepad', 'touch', 'all'] as $inputType) {
            if (isset($stats[$inputType])) {
                $inputStats = $stats[$inputType];
                
                // Обработка общей статистики
                if (isset($inputStats['overall'])) {
                    $result['wins']['total'] += $inputStats['overall']['wins'] ?? 0;
                    $result['matches']['total'] += $inputStats['overall']['matches'] ?? 0;
                    $result['kills']['total'] += $inputStats['overall']['kills'] ?? 0;
                    $result['deaths']['total'] += $inputStats['overall']['deaths'] ?? 0;
                    $result['minutesPlayed']['total'] += $inputStats['overall']['minutesPlayed'] ?? 0;
                    
                    // Берем максимальное значение для рейтинговых показателей
                    $result['kd']['total'] = max($result['kd']['total'], $inputStats['overall']['kd'] ?? 0);
                    $result['winRate']['total'] = max($result['winRate']['total'], $inputStats['overall']['winRate'] ?? 0);
                    $result['scorePerMatch']['total'] = max($result['scorePerMatch']['total'], $inputStats['overall']['scorePerMatch'] ?? 0);
                    $result['scorePerMin']['total'] = max($result['scorePerMin']['total'], $inputStats['overall']['scorePerMin'] ?? 0);
                }
                
                // Обработка соло статистики
                if (isset($inputStats['solo'])) {
                    $result['wins']['solo'] += $inputStats['solo']['wins'] ?? 0;
                    $result['matches']['solo'] += $inputStats['solo']['matches'] ?? 0;
                    $result['kills']['solo'] += $inputStats['solo']['kills'] ?? 0;
                    $result['deaths']['solo'] += $inputStats['solo']['deaths'] ?? 0;
                    $result['minutesPlayed']['solo'] += $inputStats['solo']['minutesPlayed'] ?? 0;
                    
                    $result['kd']['solo'] = max($result['kd']['solo'], $inputStats['solo']['kd'] ?? 0);
                    $result['winRate']['solo'] = max($result['winRate']['solo'], $inputStats['solo']['winRate'] ?? 0);
                    $result['scorePerMatch']['solo'] = max($result['scorePerMatch']['solo'], $inputStats['solo']['scorePerMatch'] ?? 0);
                    $result['scorePerMin']['solo'] = max($result['scorePerMin']['solo'], $inputStats['solo']['scorePerMin'] ?? 0);
                }
                
                // Обработка дуо статистики
                if (isset($inputStats['duo'])) {
                    $result['wins']['duo'] += $inputStats['duo']['wins'] ?? 0;
                    $result['matches']['duo'] += $inputStats['duo']['matches'] ?? 0;
                    $result['kills']['duo'] += $inputStats['duo']['kills'] ?? 0;
                    $result['deaths']['duo'] += $inputStats['duo']['deaths'] ?? 0;
                    $result['minutesPlayed']['duo'] += $inputStats['duo']['minutesPlayed'] ?? 0;
                    
                    $result['kd']['duo'] = max($result['kd']['duo'], $inputStats['duo']['kd'] ?? 0);
                    $result['winRate']['duo'] = max($result['winRate']['duo'], $inputStats['duo']['winRate'] ?? 0);
                    $result['scorePerMatch']['duo'] = max($result['scorePerMatch']['duo'], $inputStats['duo']['scorePerMatch'] ?? 0);
                    $result['scorePerMin']['duo'] = max($result['scorePerMin']['duo'], $inputStats['duo']['scorePerMin'] ?? 0);
                }
                
                // Обработка статистики отрядов
                if (isset($inputStats['squad'])) {
                    $result['wins']['squad'] += $inputStats['squad']['wins'] ?? 0;
                    $result['matches']['squad'] += $inputStats['squad']['matches'] ?? 0;
                    $result['kills']['squad'] += $inputStats['squad']['kills'] ?? 0;
                    $result['deaths']['squad'] += $inputStats['squad']['deaths'] ?? 0;
                    $result['minutesPlayed']['squad'] += $inputStats['squad']['minutesPlayed'] ?? 0;
                    
                    $result['kd']['squad'] = max($result['kd']['squad'], $inputStats['squad']['kd'] ?? 0);
                    $result['winRate']['squad'] = max($result['winRate']['squad'], $inputStats['squad']['winRate'] ?? 0);
                    $result['scorePerMatch']['squad'] = max($result['scorePerMatch']['squad'], $inputStats['squad']['scorePerMatch'] ?? 0);
                    $result['scorePerMin']['squad'] = max($result['scorePerMin']['squad'], $inputStats['squad']['scorePerMin'] ?? 0);
                }
                
                // Обработка LTM статистики
                if (isset($inputStats['ltm'])) {
                    $result['wins']['ltm'] += $inputStats['ltm']['wins'] ?? 0;
                    $result['matches']['ltm'] += $inputStats['ltm']['matches'] ?? 0;
                    $result['kills']['ltm'] += $inputStats['ltm']['kills'] ?? 0;
                    $result['deaths']['ltm'] += $inputStats['ltm']['deaths'] ?? 0;
                    $result['minutesPlayed']['ltm'] += $inputStats['ltm']['minutesPlayed'] ?? 0;
                    
                    $result['kd']['ltm'] = max($result['kd']['ltm'], $inputStats['ltm']['kd'] ?? 0);
                    $result['winRate']['ltm'] = max($result['winRate']['ltm'], $inputStats['ltm']['winRate'] ?? 0);
                    $result['scorePerMatch']['ltm'] = max($result['scorePerMatch']['ltm'], $inputStats['ltm']['scorePerMatch'] ?? 0);
                    $result['scorePerMin']['ltm'] = max($result['scorePerMin']['ltm'], $inputStats['ltm']['scorePerMin'] ?? 0);
                }
            }
        }
    }
    
    return $result;
}

/**
 * Функция для форматирования времени игры
 */
function fortnite_stats_format_time($minutes) {
    if (!$minutes) return '0m';
    
    $hours = floor($minutes / 60);
    $mins = $minutes % 60;
    $days = floor($hours / 24);
    $hours = $hours % 24;
    
    $result = '';
    if ($days > 0) $result .= $days . 'd ';
    if ($hours > 0) $result .= $hours . 'h ';
    if ($mins > 0) $result .= $mins . 'm';
    
    return trim($result);
}

// Обработка AJAX запроса
function fortnite_stats_ajax_handler() {
    // Проверка nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'fortnite-stats-nonce')) {
        wp_send_json_error(array('message' => 'Security check failed'));
        wp_die();
    }
    
    // Получение параметров
    $username = isset($_POST['username']) ? sanitize_text_field($_POST['username']) : '';
    $accountType = isset($_POST['accountType']) ? sanitize_text_field($_POST['accountType']) : 'epic';
    $timeWindow = isset($_POST['timeWindow']) ? sanitize_text_field($_POST['timeWindow']) : 'lifetime';
    
    if (empty($username)) {
        wp_send_json_error(array('message' => 'Username is required'));
        wp_die();
    }
    
    // Проверка кэша
    $cache_key = 'fortnite_stats_' . md5($username . '_' . $accountType . '_' . $timeWindow);
    $cache_time = intval(get_option('fortnite_stats_cache_time', 900));
    $cached_data = get_transient($cache_key);
    
    if ($cached_data !== false) {
        $cached_data['cached'] = true;
        $cached_data['cacheTime'] = time();
        fortnite_stats_log('info', 'Returning cached data for ' . $username);
        wp_send_json_success($cached_data);
        wp_die();
    }
    
    // Получение данных игрока
    $player_stats = fortnite_stats_get_player_stats($username, $accountType, $timeWindow);
    
    if (isset($player_stats['error'])) {
        wp_send_json_error(array('message' => $player_stats['error']));
        wp_die();
    }
    
    // Сохраняем в кэш
    set_transient($cache_key, $player_stats, $cache_time);
    
    wp_send_json_success($player_stats);
    wp_die();
}
add_action('wp_ajax_fortnite_stats', 'fortnite_stats_ajax_handler');
add_action('wp_ajax_nopriv_fortnite_stats', 'fortnite_stats_ajax_handler');

// Добавление страницы отладки (только для админов)
function fortnite_stats_debug_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Очистка логов, если запрошено
    if (isset($_POST['clear_logs']) && check_admin_referer('fortnite_stats_clear_logs')) {
        delete_option('fortnite_stats_debug_logs');
        echo '<div class="notice notice-success"><p>Debug logs cleared successfully.</p></div>';
    }
    
    // Получение логов
    $logs = get_option('fortnite_stats_debug_logs', array());
    ?>
    <div class="wrap">
        <h1>Fortnite Stats Debug Logs</h1>
        
        <form method="post">
            <?php wp_nonce_field('fortnite_stats_clear_logs'); ?>
            <input type="submit" name="clear_logs" class="button button-secondary" value="Clear Logs">
        </form>
        
        <div class="log-container" style="margin-top: 20px; background: #f8f9fa; padding: 15px; border-radius: 5px;">
            <?php if (empty($logs)): ?>
                <p>No logs available.</p>
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
            'Fortnite Stats Debug',
            'Fortnite Stats Debug',
            'manage_options',
            'fortnite-stats-debug',
            'fortnite_stats_debug_page'
        );
    }
}
add_action('admin_menu', 'fortnite_stats_add_debug_menu');
