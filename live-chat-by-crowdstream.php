<?php
/*
   Plugin Name: Live Chat by Crowdstream
   Plugin URI: http://wordpress.org/extend/plugins/live-chat-by-crowdstream/
   Version: 0.1
   Author: <a href="https://www.crowdstream.io">Crowdstream</a>
   Description: Talk to visitors on your site. Create, support and engage customers, make them happy.
   Text Domain: live-chat-by-crowdstream
   License: GPLv3
  */

/*
    "Live Chat by Crowdstream" Copyright (C) 2016 Crowdstream Ltd  (email : alex@crowdstream.io)

    This following part of this file is part of Live Chat by Crowdstream for WordPress.

    Live Chat by Crowdstream is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Live Chat by Crowdstream is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Contact Form to Database Extension.
    If not, see http://www.gnu.org/licenses/gpl-3.0.html
*/

$LiveChatByCrowdstream_minimalRequiredPhpVersion = '5.0';

/**
 * Check the PHP version and give a useful error message if the user's version is less than the required version
 * @return boolean true if version check passed. If false, triggers an error which WP will handle, by displaying
 * an error message on the Admin page
 */
function LiveChatByCrowdstream_noticePhpVersionWrong() {
    global $LiveChatByCrowdstream_minimalRequiredPhpVersion;
    echo '<div class="updated fade">' .
      __('Error: plugin "Live Chat by Crowdstream" requires a newer version of PHP to be running.',  'live-chat-by-crowdstream').
            '<br/>' . __('Minimal version of PHP required: ', 'live-chat-by-crowdstream') . '<strong>' . $LiveChatByCrowdstream_minimalRequiredPhpVersion . '</strong>' .
            '<br/>' . __('Your server\'s PHP version: ', 'live-chat-by-crowdstream') . '<strong>' . phpversion() . '</strong>' .
         '</div>';
}


function LiveChatByCrowdstream_PhpVersionCheck() {
    global $LiveChatByCrowdstream_minimalRequiredPhpVersion;
    if (version_compare(phpversion(), $LiveChatByCrowdstream_minimalRequiredPhpVersion) < 0) {
        add_action('admin_notices', 'LiveChatByCrowdstream_noticePhpVersionWrong');
        return false;
    }
    return true;
}


/**
 * Initialize internationalization (i18n) for this plugin.
 * References:
 *      http://codex.wordpress.org/I18n_for_WordPress_Developers
 *      http://www.wdmac.com/how-to-create-a-po-language-translation#more-631
 * @return void
 */
function LiveChatByCrowdstream_i18n_init() {
    $pluginDir = dirname(plugin_basename(__FILE__));
    load_plugin_textdomain('live-chat-by-crowdstream', false, $pluginDir . '/languages/');
}


//////////////////////////////////
// Run initialization
/////////////////////////////////

// Initialize i18n
add_action('plugins_loadedi','LiveChatByCrowdstream_i18n_init');

// Run the version check.
// If it is successful, continue with initialization for this plugin
if (LiveChatByCrowdstream_PhpVersionCheck()) {
    // Only load and run the init function if we know PHP version can parse it
    include_once('live-chat-by-crowdstream_init.php');
    LiveChatByCrowdstream_init(__FILE__);
}
