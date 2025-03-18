jQuery(document).ready(function($) {
    $('#fortnite-stats-form').on('submit', function(e) {
        e.preventDefault();
        
        var username = $('#fortnite-username').val();
        var platform = $('#fortnite-platform').val();
        var timewindow = $('#fortnite-timewindow').val();
        
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
                platform: platform,
                timewindow: timewindow
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
        if (!stats || !stats.info || !stats.group || !stats.lifetimeStats) {
            $('#fortnite-stats-results').hide();
            $('#fortnite-stats-error').html('No available data for this player.').show();
            return;
        }
        
        var html = '<div class="player-info">' +
                   '<h3>' + stats.info.username + ' (' + stats.info.platform.toUpperCase() + ')</h3>' +
                   '</div>';
        
        // Общая статистика
        html += '<div class="stats-section">' +
                '<h3>Lifetime Stats</h3>' +
                '<div class="stats-grid">';
                
        html += createStatItem('Wins', stats.lifetimeStats.wins);
        html += createStatItem('K/D', stats.lifetimeStats['k/d']);
        html += createStatItem('Win %', stats.lifetimeStats['win%'] + '%');
        html += createStatItem('Matches', stats.lifetimeStats.matches);
        html += createStatItem('Kills', stats.lifetimeStats.kills);
        html += createStatItem('Kills Per Match', stats.lifetimeStats.killsPerMatch);
        html += createStatItem('Time Played', stats.lifetimeStats.timePlayed);
        
        html += '</div></div>';
        
        // Статистика соло
        if (parseInt(stats.group.solo.matches) > 0) {
            html += '<div class="stats-section">' +
                    '<h3>Solo Mode</h3>' +
                    '<div class="stats-grid">';
                    
            html += createStatItem('Wins', stats.group.solo.wins);
            html += createStatItem('K/D', stats.group.solo['k/d']);
            html += createStatItem('Win %', stats.group.solo['win%'] + '%');
            html += createStatItem('Matches', stats.group.solo.matches);
            html += createStatItem('Kills', stats.group.solo.kills);
            html += createStatItem('Top 10', stats.group.solo.top10);
            html += createStatItem('Top 25', stats.group.solo.top25);
            
            html += '</div></div>';
        }
        
        // Статистика дуо
        if (parseInt(stats.group.duo.matches) > 0) {
            html += '<div class="stats-section">' +
                    '<h3>Duo Mode</h3>' +
                    '<div class="stats-grid">';
                    
            html += createStatItem('Wins', stats.group.duo.wins);
            html += createStatItem('K/D', stats.group.duo['k/d']);
            html += createStatItem('Win %', stats.group.duo['win%'] + '%');
            html += createStatItem('Matches', stats.group.duo.matches);
            html += createStatItem('Kills', stats.group.duo.kills);
            html += createStatItem('Top 5', stats.group.duo.top5);
            html += createStatItem('Top 12', stats.group.duo.top12);
            
            html += '</div></div>';
        }
        
        // Статистика отрядов
        if (parseInt(stats.group.squad.matches) > 0) {
            html += '<div class="stats-section">' +
                    '<h3>Squad Mode</h3>' +
                    '<div class="stats-grid">';
                    
            html += createStatItem('Wins', stats.group.squad.wins);
            html += createStatItem('K/D', stats.group.squad['k/d']);
            html += createStatItem('Win %', stats.group.squad['win%'] + '%');
            html += createStatItem('Matches', stats.group.squad.matches);
            html += createStatItem('Kills', stats.group.squad.kills);
            html += createStatItem('Top 3', stats.group.squad.top3);
            html += createStatItem('Top 6', stats.group.squad.top6);
            
            html += '</div></div>';
        }
        
        // Отображаем информацию о кэшировании, если она есть
        if (stats.cached) {
            var cacheDate = new Date(stats.cacheTime * 1000);
            html += '<div class="cache-info">Data updated: ' + cacheDate.toLocaleString() + '</div>';
        }
        
        $('#fortnite-stats-results').html(html).show();
    }
    
    // Вспомогательная функция для создания элемента статистики
    function createStatItem(label, value) {
        return '<div class="stats-item">' +
               '<div class="label">' + label + '</div>' +
               '<div class="value">' + value + '</div>' +
               '</div>';
    }
});