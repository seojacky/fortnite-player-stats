<?php
/**
 * Plugin Name: Fortnite Stats for WordPress
 * Plugin URI: https://yourwebsite.com/fortnite-stats-wp
 * Description: Displays Fortnite player statistics using Epic Online Services (EOS) API
 * Version: 1.1.0
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * License: GPL2
 */

// Запрет прямого доступа к файлу
if (!defined('ABSPATH')) {
    exit;
}

// Регистрация стилей и скриптов
function fortnite_stats_enqueue_scripts() {
    wp_enqueue_style('fortnite-stats-style', plugin_dir_url(__FILE__) . 'assets/css/style.css', array(), '1.1.0');
    wp_enqueue_script('fortnite-stats-script', plugin_dir_url(__FILE__) . 'assets/js/script.js', array('jquery'), '1.1.0', true);
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
                <label for="fortnite-platform">Platform:</label>
                <select id="fortnite-platform" name="platform" required>
                    <option value="pc">PC</option>
                    <option value="ps4">PlayStation</option>
                    <option value="xb1">Xbox</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="fortnite-timewindow">Time Window:</label>
                <select id="fortnite-timewindow" name="timewindow">
                    <option value="alltime">All Time</option>
                    <option value="weekly">Weekly</option>
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
    add_option('fortnite_stats_client_id', 'xyza7891efcel0tEJEM8cE3pcbpO4l7m');
    add_option('fortnite_stats_client_secret', '');
    add_option('fortnite_stats_deployment_id', 'c259c4b3ea014fdfa7eec3de9158c058');
    add_option('fortnite_stats_cache_time', '900'); // 15 минут кэширования по умолчанию
    
    register_setting('fortnite_stats_options_group', 'fortnite_stats_client_id');
    register_setting('fortnite_stats_options_group', 'fortnite_stats_client_secret');
    register_setting('fortnite_stats_options_group', 'fortnite_stats_deployment_id');
    register_setting('fortnite_stats_options_group', 'fortnite_stats_cache_time');
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
                    <th scope="row"><label for="fortnite_stats_client_id">Client ID</label></th>
                    <td><input type="text" id="fortnite_stats_client_id" name="fortnite_stats_client_id" value="<?php echo esc_attr(get_option('fortnite_stats_client_id')); ?>" class="regular-text" required /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="fortnite_stats_client_secret">Client Secret</label></th>
                    <td><input type="password" id="fortnite_stats_client_secret" name="fortnite_stats_client_secret" value="<?php echo esc_attr(get_option('fortnite_stats_client_secret')); ?>" class="regular-text" required /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="fortnite_stats_deployment_id">Deployment ID</label></th>
                    <td><input type="text" id="fortnite_stats_deployment_id" name="fortnite_stats_deployment_id" value="<?php echo esc_attr(get_option('fortnite_stats_deployment_id')); ?>" class="regular-text" required /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="fortnite_stats_cache_time">Cache Time (seconds)</label></th>
                    <td><input type="number" id="fortnite_stats_cache_time" name="fortnite_stats_cache_time" value="<?php echo esc_attr(get_option('fortnite_stats_cache_time')); ?>" class="regular-text" required /></td>
                </tr>
            </table>
            <p class="description">
                Введите учетные данные Epic Online Services (EOS) для доступа к API. Вы можете получить эти данные в <a href="https://dev.epicgames.com/portal/" target="_blank">Developer Portal</a>.
            </p>
            <p><strong>Примечание:</strong> Время кэширования указывается в секундах. Рекомендуется значение от 300 (5 минут) до 3600 (1 час), чтобы уменьшить нагрузку на API.</p>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Функция для получения авторизационного токена
function fortnite_stats_get_auth_token() {
    $client_id = get_option('fortnite_stats_client_id');
    $client_secret = get_option('fortnite_stats_client_secret');
    $deployment_id = get_option('fortnite_stats_deployment_id');
    
    // Проверяем существующий токен в transients
    $auth_token = get_transient('fortnite_stats_auth_token');
    if ($auth_token !== false) {
        return $auth_token;
    }
    
    // Формируем запрос на получение токена
    $auth_url = 'https://api.epicgames.dev/auth/v1/oauth/token';
    $auth_headers = array(
        'Content-Type' => 'application/x-www-form-urlencoded',
        'Authorization' => 'Basic ' . base64_encode($client_id . ':' . $client_secret)
    );
    
    $auth_body = array(
        'grant_type' => 'client_credentials',
        'deployment_id' => $deployment_id
    );
    
    $auth_response = wp_remote_post($auth_url, array(
        'headers' => $auth_headers,
        'body' => $auth_body,
        'timeout' => 15
    ));
    
    // Проверяем ответ
    if (is_wp_error($auth_response)) {
        return array('error' => $auth_response->get_error_message());
    }
    
    $auth_body = wp_remote_retrieve_body($auth_response);
    $auth_data = json_decode($auth_body, true);
    
    if (isset($auth_data['error'])) {
        return array('error' => $auth_data['error_description'] ?? $auth_data['error']);
    }
    
    if (empty($auth_data['access_token'])) {
        return array('error' => 'Failed to get access token');
    }
    
    // Сохраняем токен в transients (на 50 минут, обычный срок жизни 1 час)
    set_transient('fortnite_stats_auth_token', $auth_data['access_token'], 3000);
    
    return $auth_data['access_token'];
}

// Функция для конвертации времени из минут в человекочитаемый формат
function fortnite_stats_time_convert($minutes) {
    if (!$minutes || !is_numeric($minutes)) {
        return '0m';
    }
    
    $result = '';
    $d = intval($minutes / 24 / 60);
    $h = intval(($minutes / 60) % 24);
    $m = intval($minutes % 60);
    
    if ($d > 0) {
        $result .= $d . 'd ';
    }
    if ($h > 0) {
        $result .= $h . 'h ';
    }
    if ($m > 0) {
        $result .= $m . 'm';
    }
    else if ($result === '') {
        $result = '0m';
    }
    
    return trim($result);
}

// Функция для расчета соотношений
function fortnite_stats_ratio($a, $b) {
    if (intval($b) === 0) {
        return 0;
    }
    return number_format(intval($a) / intval($b), 2);
}

function fortnite_stats_rate($a, $b) {
    if (intval($b) === 0) {
        return 0;
    }
    return number_format((intval($a) / intval($b)) * 100, 2);
}

// Функция для конвертации статистики в нужный формат
function fortnite_stats_convert_stats($stats, $user, $platform) {
    $result = array(
        'group' => array(
            'solo' => array(
                'wins' => 0,
                'top3' => 0,
                'top5' => 0,
                'top6' => 0,
                'top10' => 0,
                'top12' => 0,
                'top25' => 0,
                'k/d' => 0,
                'win%' => 0,
                'matches' => 0,
                'kills' => 0,
                'timePlayed' => 0,
                'killsPerMatch' => 0,
                'killsPerMin' => 0,
                'score' => 0,
            ),
            'duo' => array(
                'wins' => 0,
                'top3' => 0,
                'top5' => 0,
                'top6' => 0,
                'top10' => 0,
                'top12' => 0,
                'top25' => 0,
                'k/d' => 0,
                'win%' => 0,
                'matches' => 0,
                'kills' => 0,
                'timePlayed' => 0,
                'killsPerMatch' => 0,
                'killsPerMin' => 0,
                'score' => 0,
            ),
            'squad' => array(
                'wins' => 0,
                'top3' => 0,
                'top5' => 0,
                'top6' => 0,
                'top10' => 0,
                'top12' => 0,
                'top25' => 0,
                'k/d' => 0,
                'win%' => 0,
                'matches' => 0,
                'kills' => 0,
                'timePlayed' => 0,
                'killsPerMatch' => 0,
                'killsPerMin' => 0,
                'score' => 0,
            ),
        ),
        'info' => array(
            'accountId' => $user['accountId'] ?? $user['id'] ?? '',
            'username' => $user['displayName'] ?? $user['username'] ?? '',
            'platform' => $platform,
        ),
        'lifetimeStats' => array(
            'wins' => 0,
            'top3s' => 0,
            'top5s' => 0,
            'top6s' => 0,
            'top10s' => 0,
            'top12s' => 0,
            'top25s' => 0,
            'k/d' => 0,
            'win%' => 0,
            'matches' => 0,
            'kills' => 0,
            'killsPerMin' => 0,
            'timePlayed' => 0,
            'score' => 0,
        ),
    );

    // Проверяем наличие данных в ответе
    if (!$stats || empty($stats['stats'])) {
        return $result;
    }

    // Извлекаем данные из формата API
    $gameStats = $stats['stats'];
    
    // Заполняем статистику для соло режима
    if (!empty($gameStats['br_solo'])) {
        $result['group']['solo']['wins'] = $gameStats['br_solo']['placetop1'] ?? 0;
        $result['group']['solo']['top3'] = $gameStats['br_solo']['placetop3'] ?? 0;
        $result['group']['solo']['top5'] = $gameStats['br_solo']['placetop5'] ?? 0;
        $result['group']['solo']['top10'] = $gameStats['br_solo']['placetop10'] ?? 0;
        $result['group']['solo']['top25'] = $gameStats['br_solo']['placetop25'] ?? 0;
        $result['group']['solo']['matches'] = $gameStats['br_solo']['matchesplayed'] ?? 0;
        $result['group']['solo']['kills'] = $gameStats['br_solo']['kills'] ?? 0;
        $result['group']['solo']['timePlayed'] = $gameStats['br_solo']['minutesplayed'] ?? 0;
        $result['group']['solo']['score'] = $gameStats['br_solo']['score'] ?? 0;
        
        // Расчет дополнительных метрик
        $result['group']['solo']['k/d'] = fortnite_stats_ratio(
            $result['group']['solo']['kills'],
            $result['group']['solo']['matches'] - $result['group']['solo']['wins']
        );
        $result['group']['solo']['win%'] = fortnite_stats_rate($result['group']['solo']['wins'], $result['group']['solo']['matches']);
        $result['group']['solo']['killsPerMin'] = fortnite_stats_ratio(
            $result['group']['solo']['kills'],
            $result['group']['solo']['timePlayed']
        );
        $result['group']['solo']['killsPerMatch'] = fortnite_stats_ratio(
            $result['group']['solo']['kills'],
            $result['group']['solo']['matches']
        );
        $result['group']['solo']['timePlayed'] = fortnite_stats_time_convert($result['group']['solo']['timePlayed']);
    }
    
    // Заполняем статистику для дуо режима
    if (!empty($gameStats['br_duo'])) {
        $result['group']['duo']['wins'] = $gameStats['br_duo']['placetop1'] ?? 0;
        $result['group']['duo']['top3'] = $gameStats['br_duo']['placetop3'] ?? 0;
        $result['group']['duo']['top5'] = $gameStats['br_duo']['placetop5'] ?? 0;
        $result['group']['duo']['top10'] = $gameStats['br_duo']['placetop10'] ?? 0;
        $result['group']['duo']['top12'] = $gameStats['br_duo']['placetop12'] ?? 0;
        $result['group']['duo']['matches'] = $gameStats['br_duo']['matchesplayed'] ?? 0;
        $result['group']['duo']['kills'] = $gameStats['br_duo']['kills'] ?? 0;
        $result['group']['duo']['timePlayed'] = $gameStats['br_duo']['minutesplayed'] ?? 0;
        $result['group']['duo']['score'] = $gameStats['br_duo']['score'] ?? 0;
        
        // Расчет дополнительных метрик
        $result['group']['duo']['k/d'] = fortnite_stats_ratio(
            $result['group']['duo']['kills'],
            $result['group']['duo']['matches'] - $result['group']['duo']['wins']
        );
        $result['group']['duo']['win%'] = fortnite_stats_rate($result['group']['duo']['wins'], $result['group']['duo']['matches']);
        $result['group']['duo']['killsPerMin'] = fortnite_stats_ratio(
            $result['group']['duo']['kills'],
            $result['group']['duo']['timePlayed']
        );
        $result['group']['duo']['killsPerMatch'] = fortnite_stats_ratio(
            $result['group']['duo']['kills'],
            $result['group']['duo']['matches']
        );
        $result['group']['duo']['timePlayed'] = fortnite_stats_time_convert($result['group']['duo']['timePlayed']);
    }
    
    // Заполняем статистику для сквад режима
    if (!empty($gameStats['br_squad'])) {
        $result['group']['squad']['wins'] = $gameStats['br_squad']['placetop1'] ?? 0;
        $result['group']['squad']['top3'] = $gameStats['br_squad']['placetop3'] ?? 0;
        $result['group']['squad']['top6'] = $gameStats['br_squad']['placetop6'] ?? 0;
        $result['group']['squad']['top10'] = $gameStats['br_squad']['placetop10'] ?? 0;
        $result['group']['squad']['matches'] = $gameStats['br_squad']['matchesplayed'] ?? 0;
        $result['group']['squad']['kills'] = $gameStats['br_squad']['kills'] ?? 0;
        $result['group']['squad']['timePlayed'] = $gameStats['br_squad']['minutesplayed'] ?? 0;
        $result['group']['squad']['score'] = $gameStats['br_squad']['score'] ?? 0;
        
        // Расчет дополнительных метрик
        $result['group']['squad']['k/d'] = fortnite_stats_ratio(
            $result['group']['squad']['kills'],
            $result['group']['squad']['matches'] - $result['group']['squad']['wins']
        );
        $result['group']['squad']['win%'] = fortnite_stats_rate($result['group']['squad']['wins'], $result['group']['squad']['matches']);
        $result['group']['squad']['killsPerMin'] = fortnite_stats_ratio(
            $result['group']['squad']['kills'],
            $result['group']['squad']['timePlayed']
        );
        $result['group']['squad']['killsPerMatch'] = fortnite_stats_ratio(
            $result['group']['squad']['kills'],
            $result['group']['squad']['matches']
        );
        $result['group']['squad']['timePlayed'] = fortnite_stats_time_convert($result['group']['squad']['timePlayed']);
    }
    
    // Рассчитываем общую статистику
    $totalTime = ($gameStats['br_solo']['minutesplayed'] ?? 0) + 
                 ($gameStats['br_duo']['minutesplayed'] ?? 0) + 
                 ($gameStats['br_squad']['minutesplayed'] ?? 0);
                 
    $result['lifetimeStats']['wins'] = 
        $result['group']['solo']['wins'] + $result['group']['duo']['wins'] + $result['group']['squad']['wins'];
    $result['lifetimeStats']['top3s'] = 
        $result['group']['solo']['top3'] + $result['group']['duo']['top3'] + $result['group']['squad']['top3'];
    $result['lifetimeStats']['top5s'] = 
        $result['group']['solo']['top5'] + $result['group']['duo']['top5'] + 0; // В squad нет top5
    $result['lifetimeStats']['top6s'] = 
        ($result['group']['solo']['top6'] ?? 0) + ($result['group']['duo']['top6'] ?? 0) + $result['group']['squad']['top6'];
    $result['lifetimeStats']['top10s'] = 
        $result['group']['solo']['top10'] + $result['group']['duo']['top10'] + $result['group']['squad']['top10'];
    $result['lifetimeStats']['top12s'] = 
        ($result['group']['solo']['top12'] ?? 0) + $result['group']['duo']['top12'] + 0; // В squad нет top12
    $result['lifetimeStats']['top25s'] = 
        $result['group']['solo']['top25'] + ($result['group']['duo']['top25'] ?? 0) + 0; // В squad нет top25
    $result['lifetimeStats']['matches'] = 
        $result['group']['solo']['matches'] + $result['group']['duo']['matches'] + $result['group']['squad']['matches'];
    $result['lifetimeStats']['kills'] = 
        $result['group']['solo']['kills'] + $result['group']['duo']['kills'] + $result['group']['squad']['kills'];
    $result['lifetimeStats']['score'] = 
        $result['group']['solo']['score'] + $result['group']['duo']['score'] + $result['group']['squad']['score'];
    $result['lifetimeStats']['timePlayed'] = $totalTime;
    
    // Расчет общих метрик
    $result['lifetimeStats']['k/d'] = fortnite_stats_ratio(
        $result['lifetimeStats']['kills'],
        $result['lifetimeStats']['matches'] - $result['lifetimeStats']['wins']
    );
    $result['lifetimeStats']['win%'] = fortnite_stats_rate(
        $result['lifetimeStats']['wins'],
        $result['lifetimeStats']['matches']
    );
    $result['lifetimeStats']['killsPerMin'] = fortnite_stats_ratio(
        $result['lifetimeStats']['kills'],
        $totalTime
    );
    $result['lifetimeStats']['killsPerMatch'] = fortnite_stats_ratio(
        $result['lifetimeStats']['kills'],
        $result['lifetimeStats']['matches']
    );
    $result['lifetimeStats']['timePlayed'] = fortnite_stats_time_convert($totalTime);
    
    return $result;
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
    $platform = isset($_POST['platform']) ? sanitize_text_field($_POST['platform']) : 'pc';
    $timewindow = isset($_POST['timewindow']) ? sanitize_text_field($_POST['timewindow']) : 'alltime';
    
    if (empty($username)) {
        wp_send_json_error(array('message' => 'Username is required'));
        wp_die();
    }
    
    // Проверка кэша
    $cache_key = 'fortnite_stats_' . md5($username . '_' . $platform . '_' . $timewindow);
    $cache_time = intval(get_option('fortnite_stats_cache_time', 900));
    $cached_data = get_transient($cache_key);
    
    if ($cached_data !== false) {
        $cached_data['cached'] = true;
        $cached_data['cacheTime'] = time();
        wp_send_json_success($cached_data);
        wp_die();
    }
    
    // Получение токена авторизации
    $auth_token = fortnite_stats_get_auth_token();
    
    if (is_array($auth_token) && isset($auth_token['error'])) {
        wp_send_json_error(array('message' => 'Authentication error: ' . $auth_token['error']));
        wp_die();
    }
    
    // Поиск пользователя по имени
    $lookup_url = 'https://api.epicgames.dev/epic/id/v2/accounts?displayName=' . urlencode($username);
    $lookup_response = wp_remote_get($lookup_url, array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $auth_token
        ),
        'timeout' => 15
    ));
    
    if (is_wp_error($lookup_response)) {
        wp_send_json_error(array('message' => 'Error finding player: ' . $lookup_response->get_error_message()));
        wp_die();
    }
    
    $lookup_body = wp_remote_retrieve_body($lookup_response);
    $lookup_data = json_decode($lookup_body, true);
    
    if (empty($lookup_data) || !is_array($lookup_data) || count($lookup_data) === 0) {
        wp_send_json_error(array('message' => 'Player not found'));
        wp_die();
    }
    
    $account_id = $lookup_data[0]['accountId'];
    
    // Получение статистики игрока
    $stats_url = "https://api.epicgames.dev/fortnite/v2/stats/{$account_id}/{$platform}/{$timewindow}";
    $stats_response = wp_remote_get($stats_url, array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $auth_token
        ),
        'timeout' => 15
    ));
    
    if (is_wp_error($stats_response)) {
        wp_send_json_error(array('message' => 'Error fetching stats: ' . $stats_response->get_error_message()));
        wp_die();
    }
    
    $stats_body = wp_remote_retrieve_body($stats_response);
    $stats_data = json_decode($stats_body, true);
    
    if (isset($stats_data['error'])) {
        wp_send_json_error(array('message' => 'API Error: ' . ($stats_data['errorMessage'] ?? $stats_data['error'])));
        wp_die();
    }
    
    // Преобразуем статистику в нужный формат
    $formatted_stats = fortnite_stats_convert_stats($stats_data, $lookup_data[0], $platform);
    
    // Сохраняем в кэш
    set_transient($cache_key, $formatted_stats, $cache_time);
    
    wp_send_json_success($formatted_stats);
    wp_die();
}
add_action('wp_ajax_fortnite_stats', 'fortnite_stats_ajax_handler');
add_action('wp_ajax_nopriv_fortnite_stats', 'fortnite_stats_ajax_handler');