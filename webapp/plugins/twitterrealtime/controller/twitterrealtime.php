<?php
/*
 Plugin Name: Twitter Realtime
 Plugin URI: http://github.com/ginatrapani/thinkup/tree/master/webapp/plugins/twitterrealtime/
 Description: Capture and display tweets in realtime.
 Icon: assets/img/twitter_icon.png
 Version: 0.01
 Author: Amy Unruh, Mark Wilkie
 */

/**
 *
 * ThinkUp/webapp/plugins/twitterrealtime/controller/twitterrealtime.php
 *
 * Copyright (c) 2009-2011 Gina Trapani, Amy Unruh
 *
 * LICENSE:
 *
 * This file is part of ThinkUp (http://thinkupapp.com).
 *
 * ThinkUp is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, either version 2 of the License, or (at your option) any
 * later version.
 *
 * ThinkUp is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with ThinkUp.  If not, see
 * <http://www.gnu.org/licenses/>.
 *
 * @author Amy Unruh
 */
$config = Config::getInstance();
//@TODO: For the testing sake, check if mock class has already been loaded
//@TODO: Figure out a better way to do this
if (!class_exists('TwitterOAuth')) {
    Utils::defineConstants();
    require_once THINKUP_WEBAPP_PATH.'plugins/twitter/extlib/twitteroauth/twitteroauth.php';
}

$webapp = Webapp::getInstance();
$webapp->registerPlugin('twitterrealtime', 'TwitterRealtimePlugin');

$streamer = Streamer::getInstance();
$streamer->registerStreamerPlugin('TwitterRealtimePlugin');
