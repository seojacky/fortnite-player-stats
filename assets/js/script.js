jQuery(document).ready(function($) {
    $('#fortnite-stats-form').on('submit', function(e) {
        e.preventDefault();
        
        var username = $('#fortnite-username').val();
        var accountType = $('#fortnite-platform').val();
        var timeWindow = $('#fortnite-timewindow').val();
        
        // Показываем загрузочное сообщение
        $('#fortnite-stats-results').html('<div class="loading">Loading stats...</div>').show();
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
                    $('#fortnite-stats-error').html(response.data.message || 'Error retrieving data.').show();
                }
            },
            error: function() {
                $('#fortnite-stats-results').hide();
                $('#fortnite-stats-error').html('Network error. Please try again.').show();
            }
        });
    });
    
    // Функция для отображения статистики
    function displayStats(stats) {
        // Если нет данных для отображения
        if (!stats || !stats.account) {
            $('#fortnite-stats-results').hide();
            $('#fortnite-stats-error').html('No available data for this player.').show();
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
                '<div class="player-level">Level ' + stats.account.level + '</div>' +
                '</div>' +
                '</div>';
        
        // Battle Pass
        html += '<div class="battle-pass">' +
                '<h3>Battle Pass</h3>' +
                '<div class="stat-row">' +
                '<div class="stat-label">Level</div>' +
                '<div class="stat-value">' + stats.battlePass.level + '</div>' +
                '</div>' +
                '<div class="stat-row">' +
                '<div class="stat-label">Progress</div>' +
                '<div class="stat-value">' + stats.battlePass.progress + '%</div>' +
                '</div>' +
                '</div>';
        
        // Создаем сетку статистики
        html += '<div class="stats-grid">';
        
        // Соло статистика
        if (stats.matches.solo > 0) {
            html += '<div class="stats-card">' +
                    '<h3>Solo</h3>' +
                    '<div class="stat-row">' +
                    '<div class="stat-label">Wins</div>' +
                    '<div class="stat-value">' + stats.wins.solo + '</div>' +
                    '</div>' +
                    '<div class="stat-row">' +
                    '<div class="stat-label">Matches</div>' +
                    '<div class="stat-value">' + stats.matches.solo + '</div>' +
                    '</div>' +
                    '<div class="stat-row">' +
                    '<div class="stat-label">Win Rate</div>' +
                    '<div class="stat-value">' + stats.winRate.solo.toFixed(1) + '%</div>' +
                    '</div>' +
                    '<div class="stat-row">' +
                    '<div class="stat-label">K/D Ratio</div>' +
                    '<div class="stat-value">' + stats.kd.solo.toFixed(2) + '</div>' +
                    '</div>' +
                    '<div class="stat-row">' +
                    '<div class="stat-label">Kills</div>' +
                    '<div class="stat-value">' + stats.kills.solo + '</div>' +
                    '</div>' +
                    '</div>';
        }
        
        // Дуо статистика
        if (stats.matches.duo > 0) {
            html += '<div class="stats-card">' +
                    '<h3>Duo</h3>' +
                    '<div class="stat-row">' +
                    '<div class="stat-label">Wins</div>' +
                    '<div class="stat-value">' + stats.wins.duo + '</div>' +
                    '</div>' +
                    '<div class="stat-row">' +
                    '<div class="stat-label">Matches</div>' +
                    '<div class="stat-value">' + stats.matches.duo + '</div>' +
                    '</div>' +
                    '<div class="stat-row">' +
                    '<div class="stat-label">Win Rate</div>' +
                    '<div class="stat-value">' + stats.winRate.duo.toFixed(1) + '%</div>' +
                    '</div>' +
                    '<div class="stat-row">' +
                    '<div class="stat-label">K/D Ratio</div>' +
                    '<div class="stat-value">' + stats.kd.duo.toFixed(2) + '</div>' +
                    '</div>' +
                    '<div class="stat-row">' +
                    '<div class="stat-label">Kills</div>' +
                    '<div class="stat-value">' + stats.kills.duo + '</div>' +
                    '</div>' +
                    '</div>';
        }
        
        // Отряд статистика
        if (stats.matches.squad > 0) {
            html += '<div class="stats-card">' +
                    '<h3>Squad</h3>' +
                    '<div class="stat-row">' +
                    '<div class="stat-label">Wins</div>' +
                    '<div class="stat-value">' + stats.wins.squad + '</div>' +
                    '</div>' +
                    '<div class="stat-row">' +
                    '<div class="stat-label">Matches</div>' +
                    '<div class="stat-value">' + stats.matches.squad + '</div>' +
                    '</div>' +
                    '<div class="stat-row">' +
                    '<div class="stat-label">Win Rate</div>' +
                    '<div class="stat-value">' + stats.winRate.squad.toFixed(1) + '%</div>' +
                    '</div>' +
                    '<div class="stat-row">' +
                    '<div class="stat-label">K/D Ratio</div>' +
                    '<div class="stat-value">' + stats.kd.squad.toFixed(2) + '</div>' +
                    '</div>' +
                    '<div class="stat-row">' +
                    '<div class="stat-label">Kills</div>' +
                    '<div class="stat-value">' + stats.kills.squad + '</div>' +
                    '</div>' +
                    '</div>';
        }
        
        // Общая статистика
        html += '<div class="stats-card">' +
                '<h3>Overall</h3>' +
                '<div class="stat-row">' +
                '<div class="stat-label">Total Wins</div>' +
                '<div class="stat-value">' + stats.wins.total + '</div>' +
                '</div>' +
                '<div class="stat-row">' +
                '<div class="stat-label">Total Matches</div>' +
                '<div class="stat-value">' + stats.matches.total + '</div>' +
                '</div>' +
                '<div class="stat-row">' +
                '<div class="stat-label">Win Rate</div>' +
                '<div class="stat-value">' + stats.winRate.total.toFixed(1) + '%</div>' +
                '</div>' +
                '<div class="stat-row">' +
                '<div class="stat-label">K/D Ratio</div>' +
                '<div class="stat-value">' + stats.kd.total.toFixed(2) + '</div>' +
                '</div>' +
                '<div class="stat-row">' +
                '<div class="stat-label">Total Kills</div>' +
                '<div class="stat-value">' + stats.kills.total + '</div>' +
                '</div>' +
                '</div>';
                
        html += '</div>'; // Закрываем stats-grid
        
        // Информация о кэшировании
        if (stats.cached) {
            var cacheDate = new Date(stats.cacheTime * 1000);
            html += '<div class="cache-info">Data updated: ' + cacheDate.toLocaleString() + '</div>';
        }
        
        $('#fortnite-stats-results').html(html).show();
    }
});