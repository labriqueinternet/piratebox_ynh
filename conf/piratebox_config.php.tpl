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

$options = [
  'app_name'           => "<TPL:OPT_NAME>",

  'base_path'          => "<TPL:NGINX_REALPATH>",
  'base_uri'           => "/",

  'allow_renaming'     => <TPL:OPT_RENAMING>,
  'allow_deleting'     => <TPL:OPT_DELETING>,
  'allow_newfolders'   => true,

  'enable_chat'        => <TPL:OPT_CHAT>,
  'default_pseudo'     => "anonymous",

  'time_format'        => "d/m/y H:i",
  'fancyurls'          => true,
];

?>
