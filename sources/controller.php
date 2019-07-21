<?php

/* PirateBox app for YunoHost 
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

function ynh_setting_get($setting, $app = 'piratebox') {
  $value = exec("sudo yunohost app setting $app $setting");

  return htmlspecialchars($value);
}

function ynh_setting_set($setting, $value) {
  return exec('sudo yunohost app setting piratebox '.escapeshellarg($setting).' -v '.escapeshellarg($value));
}

function stop_service() {
  exec('sudo systemctl stop ynh-piratebox');
}

function start_service() {
  exec('sudo systemctl start ynh-piratebox', $output, $retcode);

  return $retcode;
}

function service_status() {
  exec('sudo ynh-piratebox status', $output);

  return $output;
}

function service_faststatus() {
  exec('ls /etc/nginx/conf.d/captive-piratebox.conf', $output, $retcode);

  return $retcode;
}

dispatch('/', function() {
  $ssids = explode('|', ynh_setting_get('wifi_ssid', 'hotspot'));
  $wifi_device_id = ynh_setting_get('wifi_device_id');
  $wifi_ssid_list = '';
  $wifi_ssid = '';

  for($i = 0; $i < count($ssids); $i++) {
    $active = '';

    if($i == $wifi_device_id) {
      $active = 'class="active"';
      $wifi_ssid = htmlentities($ssids[$i]);
    }

    $wifi_ssid_list .= "<li $active data-device-id='$i'><a href='javascript:;'>".htmlentities($ssids[$i]).'</a></li>';
  }

  set('faststatus', service_faststatus() == 0);
  set('service_enabled', ynh_setting_get('service_enabled'));
  set('wifi_device_id', $wifi_device_id);
  set('wifi_ssid', $wifi_ssid);
  set('wifi_ssid_list', $wifi_ssid_list);
  set('opt_maxspace', ynh_setting_get('opt_maxspace'));
  set('opt_renaming', ynh_setting_get('opt_renaming'));
  set('opt_deleting', ynh_setting_get('opt_deleting'));
  set('opt_chat', ynh_setting_get('opt_chat'));
  set('opt_name', ynh_setting_get('opt_name'));
  set('opt_domain', ynh_setting_get('opt_domain'));

  return render('settings.html.php');
});

dispatch_put('/settings', function() {
  $service_enabled = isset($_POST['service_enabled']) ? 1 : 0;

  if($service_enabled == 1) {
    try {
      $_POST['opt_name'] = htmlentities(str_replace('"', '', $_POST['opt_name']));

      if(empty($_POST['opt_name'])) {
        throw new Exception(_('The name cannot be empty'));
      }

      if($_POST['wifi_device_id'] == -1) {
        throw new Exception(_('You need to select an associated hotspot'));
      }

    } catch(Exception $e) {
      flash('error', _('PirateBox')." $id: ".$e->getMessage().' ('._('configuration not updated').').');
      goto redirect;
    }
  }

  stop_service();
  
  ynh_setting_set('service_enabled', $service_enabled);

  if($service_enabled == 1) {
    ynh_setting_set('opt_name', $_POST['opt_name']);
    ynh_setting_set('opt_renaming', isset($_POST['opt_renaming']) ? 1 : 0);
    ynh_setting_set('opt_maxspace', $_POST['opt_maxspace']);
    ynh_setting_set('opt_deleting', isset($_POST['opt_deleting']) ? 1 : 0);
    ynh_setting_set('opt_chat', isset($_POST['opt_chat']) ? 1 : 0);
    ynh_setting_set('wifi_device_id', $_POST['wifi_device_id']);

    $retcode = start_service();

    if($retcode == 0) {
      flash('success', _('Configuration updated and service successfully reloaded'));
    } else {
      flash('error', _('Configuration updated but service reload failed'));
    }

  } else {
      flash('success', _('Service successfully disabled'));
  }

  redirect:
  redirect_to('/');
});

dispatch('/status', function() {
  $status_lines = service_status();
  $status_list = '';

  foreach($status_lines AS $status_line) {
    if(preg_match('/^\[INFO\]/', $status_line)) {
      $status_list .= '<li class="status-info">'.htmlspecialchars($status_line).'</li>';
    }
    elseif(preg_match('/^\[OK\]/', $status_line)) {
      $status_list .= '<li class="status-success">'.htmlspecialchars($status_line).'</li>';
    }
    elseif(preg_match('/^\[WARN\]/', $status_line)) {
      $status_list .= '<li class="status-warning">'.htmlspecialchars($status_line).'</li>';
    }
    elseif(preg_match('/^\[ERR\]/', $status_line)) {
      $status_list .= '<li class="status-danger">'.htmlspecialchars($status_line).'</li>';
    }
  }

  echo $status_list;
});

dispatch('/lang/:locale', function($locale = 'en') {
  switch($locale) {
    case 'fr':
      $_SESSION['locale'] = 'fr';
    break;

    default:
      $_SESSION['locale'] = 'en';
  }

  redirect_to('/');
});
