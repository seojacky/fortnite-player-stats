<?php
/**
 * Plugin Name: Fortnite Player Stats 
 * Description: Displays Fortnite player statistics using Fortnite-API.com
 * Version: 4.0
 * Author: seo_jacky
 * Author URI: https://t.me/big_jacky
 * Plugin URI: https://github.com/seojacky/fortnite-player-stats
 * GitHub Plugin URI: https://github.com/seojacky/fortnite-player-stats
 * Text Domain: fortnite-player-stats
 * Domain Path: /languages
 * License: GPL2
 */

// Запрет прямого доступа к файлу
if (!defined('ABSPATH')) {
    exit;
}

// Определяем константы плагина
define('FORTNITE_STATS_VERSION', '3.0');
define('FORTNITE_STATS_PATH', plugin_dir_path(__FILE__));
define('FORTNITE_STATS_URL', plugin_dir_url(__FILE__));


// Получаем базовое имя файла плагина для фильтра
$plugin_base_name = plugin_basename(__FILE__);
add_filter("plugin_action_links_{$plugin_base_name}", 'fortnite_stats_add_settings_link');


// Определяем базовое имя плагина для повторного использования
$fortnite_stats_base_name = plugin_basename(__FILE__);

/**
 * Загрузка текстового домена для перевода плагина
 */
function fortnite_stats_load_textdomain() {
    global $fortnite_stats_base_name;
    load_plugin_textdomain('fortnite-player-stats', false, dirname($fortnite_stats_base_name) . '/languages');
}
add_action('plugins_loaded', 'fortnite_stats_load_textdomain');

/**
 * Добавляет ссылку "Настройки" на странице плагинов
 */
function fortnite_stats_add_settings_link($links) {
    $settings_link = '<a href="' . admin_url('options-general.php?page=fortnite-stats') . '">' . __('Settings', 'fortnite-stats-wp') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}

// Используем сохраненное базовое имя плагина
add_filter("plugin_action_links_{$fortnite_stats_base_name}", 'fortnite_stats_add_settings_link');

// Подключаем файл настроек
require_once FORTNITE_STATS_PATH . 'settings.php';

// Регистрация стилей и скриптов
function fortnite_stats_enqueue_scripts() {
    wp_enqueue_style('fortnite-stats-style', FORTNITE_STATS_URL . 'assets/css/style.css', array(), FORTNITE_STATS_VERSION);
    wp_enqueue_script('fortnite-stats-script', FORTNITE_STATS_URL . 'assets/js/script.js', array('jquery'), FORTNITE_STATS_VERSION, true);
    
    // Массив строк для перевода в JavaScript
    $i18n_strings = array(
        'loading' => __('Loading stats...', 'fortnite-player-stats'),
        'errorRetrieving' => __('Error retrieving data.', 'fortnite-player-stats'),
        'networkError' => __('Network error. Please try again.', 'fortnite-player-stats'),
        'noAvailableData' => __('No available data for this player.', 'fortnite-player-stats'),
        'level' => __('Level', 'fortnite-player-stats'),
        'battlePass' => __('Battle Pass', 'fortnite-player-stats'),
        'progress' => __('Progress', 'fortnite-player-stats'),
        'solo' => __('Solo', 'fortnite-player-stats'),
        'duo' => __('Duo', 'fortnite-player-stats'),
        'squad' => __('Squad', 'fortnite-player-stats'),
        'overall' => __('Overall', 'fortnite-player-stats'),
        'wins' => __('Wins', 'fortnite-player-stats'),
        'matches' => __('Matches', 'fortnite-player-stats'),
        'winRate' => __('Win Rate', 'fortnite-player-stats'),
        'kdRatio' => __('K/D Ratio', 'fortnite-player-stats'),
        'kills' => __('Kills', 'fortnite-player-stats'),
        'totalWins' => __('Total Wins', 'fortnite-player-stats'),
        'totalMatches' => __('Total Matches', 'fortnite-player-stats'),
        'totalKills' => __('Total Kills', 'fortnite-player-stats'),
        'dataUpdated' => __('Data updated', 'fortnite-player-stats')
    );
    
    wp_localize_script('fortnite-stats-script', 'fortniteStats', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('fortnite-stats-nonce'),
        'pluginUrl' => FORTNITE_STATS_URL, // URL плагина для доступа к изображениям
        'i18n' => $i18n_strings // Переводы для JavaScript
    ));
}
add_action('wp_enqueue_scripts', 'fortnite_stats_enqueue_scripts');

// Регистрация шорткода [fortnite_stats_form]
function fortnite_stats_shortcode() {
    ob_start();
    ?>
<div class="fortnite-stats-container">
    <h2><?php _e('Fortnite Player Stats', 'fortnite-player-stats'); ?></h2>
    <form id="fortnite-stats-form" class="fortnite-stats-form">
        <div class="form-group">
            <label for="fortnite-username" class="visually-hidden"><?php _e('Username:', 'fortnite-player-stats'); ?></label>
            <input type="text" id="fortnite-username" name="username" placeholder="<?php _e('Username', 'fortnite-player-stats'); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="fortnite-platform" class="visually-hidden"><?php _e('Account Type:', 'fortnite-player-stats'); ?></label>
            <select id="fortnite-platform" name="accountType" required>
                <option value="epic"><?php _e('Epic', 'fortnite-player-stats'); ?></option>
                <option value="psn"><?php _e('PlayStation', 'fortnite-player-stats'); ?></option>
                <option value="xbl"><?php _e('Xbox', 'fortnite-player-stats'); ?></option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="fortnite-timewindow" class="visually-hidden"><?php _e('Time Window:', 'fortnite-player-stats'); ?></label>
            <select id="fortnite-timewindow" name="timeWindow">
                <option value="lifetime"><?php _e('Lifetime', 'fortnite-player-stats'); ?></option>
                <option value="season"><?php _e('Current Season', 'fortnite-player-stats'); ?></option>
            </select>
        </div>
        
        <button type="submit" class="fortnite-stats-submit"><?php _e('Get Stats', 'fortnite-player-stats'); ?></button>
    </form>
    
    <div class="form-notice">
        <strong><?php _e('Note:', 'fortnite-player-stats'); ?></strong> <?php _e('For PlayStation and Xbox searches, you must use the actual PSN/Xbox Live username, which may differ from the Epic Games username.', 'fortnite-player-stats'); ?>
    </div>
    
    <div id="fortnite-stats-results" class="fortnite-stats-results"></div>
    <div id="fortnite-stats-error" class="fortnite-stats-error"></div>
</div>
    <?php
    return ob_get_clean();
}
add_shortcode('fortnite_stats_form', 'fortnite_stats_shortcode');

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
 * Получает изображение скина для пользователя на основе его имени
 * 
 * @param string $username Имя игрока Fortnite
 * @return string URL изображения скина
 */
function fortnite_stats_get_default_skin_image_by_username($username) {
    // Используем только подтвержденные доступные скины
    $default_skins = array(
        'CID_001_Athena_Commando_F_Default', // Ramirez
        'CID_004_Athena_Commando_F_Default', // Wildcat
        'CID_005_Athena_Commando_M_Default', // Spitfire
        'CID_007_Athena_Commando_M_Default'  // Renegade
    );
    
    // Используем имя пользователя для выбора скина
    $skin_index = abs(crc32($username)) % count($default_skins);
    $skin_id = $default_skins[$skin_index];
    
    // Формируем URL напрямую
    $image_url = "https://fortnite-api.com/images/cosmetics/br/{$skin_id}/smallicon.png";
    
    return $image_url;
}

/**
 * Функция для получения статистики игрока
 */
function fortnite_stats_get_player_stats($username, $accountType, $timeWindow) {
    $api_key = get_option('fortnite_stats_api_key');
    
    if (empty($api_key)) {
        fortnite_stats_log('error', 'API key is not set');
        return array('error' => __('API key is not set. Please configure the plugin in admin settings.', 'fortnite-player-stats'));
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
        return array('error' => __("Error:", 'fortnite-player-stats') . " " . $response->get_error_message());
    }
    
    $status = wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);
    
    fortnite_stats_log('debug', 'API response status', $status);
    
    $stats_data = json_decode($body, true);
    
    if ($status != 200 || !isset($stats_data['data'])) {
        $error_message = __('Failed to retrieve stats', 'fortnite-player-stats');
        
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
    $username = $api_data['account']['name'] ?? '';
    
    $result = array(
        'account' => array(
            'id' => $api_data['account']['id'] ?? '',
            'name' => $username,
            'level' => $api_data['account']['level'] ?? 0,
            'avatar' => fortnite_stats_get_default_skin_image_by_username($username)
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
        wp_send_json_error(array('message' => __('Security check failed', 'fortnite-player-stats')));
        wp_die();
    }
    
    // Получение параметров
    $username = isset($_POST['username']) ? sanitize_text_field($_POST['username']) : '';
    $accountType = isset($_POST['accountType']) ? sanitize_text_field($_POST['accountType']) : 'epic';
    $timeWindow = isset($_POST['timeWindow']) ? sanitize_text_field($_POST['timeWindow']) : 'lifetime';
    
    if (empty($username)) {
        wp_send_json_error(array('message' => __('Username is required', 'fortnite-player-stats')));
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
        $error_message = $player_stats['error'];
        
        // Улучшенные сообщения об ошибках
        if (strpos($error_message, 'account does not exist') !== false) {
            if ($accountType === 'psn') {
                $error_message = __('PlayStation Network account not found. Try searching by Epic Games username instead.', 'fortnite-player-stats');
            } elseif ($accountType === 'xbl') {
                $error_message = __('Xbox Live account not found. Try searching by Epic Games username instead.', 'fortnite-player-stats');
            } else {
                $error_message = __('Account not found. Please check the username and try again.', 'fortnite-player-stats');
            }
        }
        
        wp_send_json_error(array('message' => $error_message));
        wp_die();
    }
    
    // Сохраняем в кэш
    set_transient($cache_key, $player_stats, $cache_time);
    
    wp_send_json_success($player_stats);
    wp_die();
}
add_action('wp_ajax_fortnite_stats', 'fortnite_stats_ajax_handler');
add_action('wp_ajax_nopriv_fortnite_stats', 'fortnite_stats_ajax_handler');