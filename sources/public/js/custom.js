/* VPN Client app for YunoHost 
 * Copyright (C) 2015 Julien Vaubourg <julien@vaubourg.com>
 * Contribute at https://github.com/labriqueinternet/piratebox_ynh
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

$(document).ready(function() {
  $('.btn-group').button();
  $('[data-toggle="tooltip"]').tooltip();

  $('.switch').bootstrapToggle();

  $('#save').click(function() {
    $(this).prop('disabled', true);
    $('#save-loading').show();
    $('#form').submit();
  });

  $('#status .close').click(function() {
    $(this).parent().hide();
  });

  $('#statusbtn').click(function() {
    if($('#status-loading').is(':hidden')) {
      $('#status').hide();
      $('#status-loading').show();

      $.ajax({
        url: '?/status',
      }).done(function(data) {
        $('#status-loading').hide();
        $('#status-text').html('<ul>' + data + '</ul>');
        $('#status').show('slow');
      });
    }
  });

  function showSliders() {
    $('#opt_maxspace').slider({
      formater: function(value) {
        return value + '%';
      }
    });
  }

  $('#service_enabled').change(function() {
    if($('#service_enabled').parent().hasClass('off')) {
      $('.enabled').hide('slow');
    } else {
      $('.slider').hide();
      $('.txtslider').hide();
      $('.enabled').show('slow', showSliders);
    }
  });

  if(!$('#service_enabled').parent().hasClass('off')) {
    showSliders();
  }

  $('.dropdown-menu li').click(function() {
    var menu = $(this).parent();
    var items = menu.children();
    var button = menu.prev();
    var input = button.prev();
  
    items.removeClass('active');
    $(this).addClass('active');
  
    button.text($(this).text());
    button.append(' <span class="caret"></span>');
  
    input.val($(this).data('device-id'));
  });
});
