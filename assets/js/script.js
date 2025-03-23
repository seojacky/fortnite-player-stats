jQuery(document).ready(function($) {
    $('#fortnite-stats-form').on('submit', function(e) {
        e.preventDefault();
        
        var username = $('#fortnite-username').val();
        var accountType = $('#fortnite-platform').val();
        var timeWindow = $('#fortnite-timewindow').val();
        
        // Показываем загрузочное сообщение
        $('#fortnite-stats-results').html('<div class="loading">' + fortniteStats.i18n.loading + '</div>').show();
        $('#fortnite-stats-error').hide();
        
        // Отправляем AJAX запрос
        $.ajax({
            url: fortniteStats.ajaxurl,
            type: 'POST',
            data: {
                action: 'fortnite_stats',
                nonce: fortniteStats.nonce,
                username: username,
                accountType: accountType,
                timeWindow: timeWindow
            },
            success: function(response) {
                if (response.success) {
                    displayStats(response.data);
                } else {
                    $('#fortnite-stats-results').hide();
                    $('#fortnite-stats-error').html(response.data.message || fortniteStats.i18n.errorRetrieving).show();
                }
            },
            error: function() {
                $('#fortnite-stats-results').hide();
                $('#fortnite-stats-error').html(fortniteStats.i18n.networkError).show();
            }
        });
    });
    
    // Функция для отображения статистики
    function displayStats(stats) {
        // Если нет данных для отображения
        if (!stats || !stats.account) {
            $('#fortnite-stats-results').hide();
            $('#fortnite-stats-error').html(fortniteStats.i18n.noAvailableData).show();
            return;
        }
        
        var html = '';
        
        // Информация об игроке с аватаром
        html += '<div class="player-info">';
		
		console.log('Avatar URL:', stats.account.avatar);
        
        // Добавляем аватар, если он доступен
        if (stats.account.avatar) {
            html += '<div class="player-avatar">' +
                    '<img src="' + stats.account.avatar + '" alt="' + stats.account.name + '">' +
                    '</div>';
        }
        
        html += '<div class="player-details">' +
                '<div class="player-name">' + stats.account.name + '</div>' +
                '<div class="player-level">' + fortniteStats.i18n.level + ' ' + stats.account.level + '</div>' +
                '</div>' +
                '</div>';
        
        // Battle Pass
        html += '<div class="battle-pass">' +
                '<h3>' + fortniteStats.i18n.battlePass + '</h3>' +
                '<div class="stat-row">' +
                '<div class="stat-label">' + fortniteStats.i18n.level + '</div>' +
                '<div class="stat-value">' + stats.battlePass.level + '</div>' +
                '</div>' +
                '<div class="stat-row">' +
                '<div class="stat-label">' + fortniteStats.i18n.progress + '</div>' +
                '<div class="stat-value">' + stats.battlePass.progress + '%</div>' +
                '</div>' +
                '</div>';
        
        // Создаем сетку статистики
        html += '<div class="stats-grid">';
        
        // Соло статистика
        if (stats.matches.solo > 0) {
            html += '<div class="stats-card">' +
                    '<h3>' + fortniteStats.i18n.solo + '</h3>' +
                    '<div class="stat-row">' +
                    '<div class="stat-label">' + fortniteStats.i18n.wins + '</div>' +
                    '<div class="stat-value">' + stats.wins.solo + '</div>' +
                    '</div>' +
                    '<div class="stat-row">' +
                    '<div class="stat-label">' + fortniteStats.i18n.matches + '</div>' +
                    '<div class="stat-value">' + stats.matches.solo + '</div>' +
                    '</div>' +
                    '<div class="stat-row">' +
                    '<div class="stat-label">' + fortniteStats.i18n.winRate + '</div>' +
                    '<div class="stat-value">' + stats.winRate.solo.toFixed(1) + '%</div>' +
                    '</div>' +
                    '<div class="stat-row">' +
                    '<div class="stat-label">' + fortniteStats.i18n.kdRatio + '</div>' +
                    '<div class="stat-value">' + stats.kd.solo.toFixed(2) + '</div>' +
                    '</div>' +
                    '<div class="stat-row">' +
                    '<div class="stat-label">' + fortniteStats.i18n.kills + '</div>' +
                    '<div class="stat-value">' + stats.kills.solo + '</div>' +
                    '</div>' +
                    '</div>';
        }
        
        // Дуо статистика
        if (stats.matches.duo > 0) {
            html += '<div class="stats-card">' +
                    '<h3>' + fortniteStats.i18n.duo + '</h3>' +
                    '<div class="stat-row">' +
                    '<div class="stat-label">' + fortniteStats.i18n.wins + '</div>' +
                    '<div class="stat-value">' + stats.wins.duo + '</div>' +
                    '</div>' +
                    '<div class="stat-row">' +
                    '<div class="stat-label">' + fortniteStats.i18n.matches + '</div>' +
                    '<div class="stat-value">' + stats.matches.duo + '</div>' +
                    '</div>' +
                    '<div class="stat-row">' +
                    '<div class="stat-label">' + fortniteStats.i18n.winRate + '</div>' +
                    '<div class="stat-value">' + stats.winRate.duo.toFixed(1) + '%</div>' +
                    '</div>' +
                    '<div class="stat-row">' +
                    '<div class="stat-label">' + fortniteStats.i18n.kdRatio + '</div>' +
                    '<div class="stat-value">' + stats.kd.duo.toFixed(2) + '</div>' +
                    '</div>' +
                    '<div class="stat-row">' +
                    '<div class="stat-label">' + fortniteStats.i18n.kills + '</div>' +
                    '<div class="stat-value">' + stats.kills.duo + '</div>' +
                    '</div>' +
                    '</div>';
        }
        
        // Отряд статистика
        if (stats.matches.squad > 0) {
            html += '<div class="stats-card">' +
                    '<h3>' + fortniteStats.i18n.squad + '</h3>' +
                    '<div class="stat-row">' +
                    '<div class="stat-label">' + fortniteStats.i18n.wins + '</div>' +
                    '<div class="stat-value">' + stats.wins.squad + '</div>' +
                    '</div>' +
                    '<div class="stat-row">' +
                    '<div class="stat-label">' + fortniteStats.i18n.matches + '</div>' +
                    '<div class="stat-value">' + stats.matches.squad + '</div>' +
                    '</div>' +
                    '<div class="stat-row">' +
                    '<div class="stat-label">' + fortniteStats.i18n.winRate + '</div>' +
                    '<div class="stat-value">' + stats.winRate.squad.toFixed(1) + '%</div>' +
                    '</div>' +
                    '<div class="stat-row">' +
                    '<div class="stat-label">' + fortniteStats.i18n.kdRatio + '</div>' +
                    '<div class="stat-value">' + stats.kd.squad.toFixed(2) + '</div>' +
                    '</div>' +
                    '<div class="stat-row">' +
                    '<div class="stat-label">' + fortniteStats.i18n.kills + '</div>' +
                    '<div class="stat-value">' + stats.kills.squad + '</div>' +
                    '</div>' +
                    '</div>';
        }
        
        // Общая статистика
        html += '<div class="stats-card">' +
                '<h3>' + fortniteStats.i18n.overall + '</h3>' +
                '<div class="stat-row">' +
                '<div class="stat-label">' + fortniteStats.i18n.totalWins + '</div>' +
                '<div class="stat-value">' + stats.wins.total + '</div>' +
                '</div>' +
                '<div class="stat-row">' +
                '<div class="stat-label">' + fortniteStats.i18n.totalMatches + '</div>' +
                '<div class="stat-value">' + stats.matches.total + '</div>' +
                '</div>' +
                '<div class="stat-row">' +
                '<div class="stat-label">' + fortniteStats.i18n.winRate + '</div>' +
                '<div class="stat-value">' + stats.winRate.total.toFixed(1) + '%</div>' +
                '</div>' +
                '<div class="stat-row">' +
                '<div class="stat-label">' + fortniteStats.i18n.kdRatio + '</div>' +
                '<div class="stat-value">' + stats.kd.total.toFixed(2) + '</div>' +
                '</div>' +
                '<div class="stat-row">' +
                '<div class="stat-label">' + fortniteStats.i18n.totalKills + '</div>' +
                '<div class="stat-value">' + stats.kills.total + '</div>' +
                '</div>' +
                '</div>';
                
        html += '</div>'; // Закрываем stats-grid
        
        // Информация о кэшировании
        if (stats.cached) {
            var cacheDate = new Date(stats.cacheTime * 1000);
            html += '<div class="cache-info">' + fortniteStats.i18n.dataUpdated + ': ' + cacheDate.toLocaleString() + '</div>';
        }
        
        $('#fortnite-stats-results').html(html).show();
    }
});