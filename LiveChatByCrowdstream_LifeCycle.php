<?php
/*
    "Live Chat by Crowdstream" Copyright (C) 2016 Crowdstream Ltd  (email : alex@crowdstream.io)

    This file is part of Live Chat by Crowdstream for WordPress.

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

include_once('LiveChatByCrowdstream_InstallIndicator.php');

class LiveChatByCrowdstream_LifeCycle extends LiveChatByCrowdstream_InstallIndicator {

    public function install() {

        // Initialize Plugin Options
        $this->initOptions();

        // Initialize DB Tables used by the plugin
        $this->installDatabaseTables();

        // Other Plugin initialization - for the plugin writer to override as needed
        $this->otherInstall();

        // Record the installed version
        $this->saveInstalledVersion();

        // To avoid running install() more then once
        $this->markAsInstalled();
    }

    public function uninstall() {
        $this->otherUninstall();
        $this->unInstallDatabaseTables();
        $this->deleteSavedOptions();
        $this->markAsUnInstalled();
    }

    /**
     * Perform any version-upgrade activities prior to activation (e.g. database changes)
     * @return void
     */
    public function upgrade() {
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=105
     * @return void
     */
    public function activate() {
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=105
     * @return void
     */
    public function deactivate() {
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=31
     * @return void
     */
    protected function initOptions() {
    }

    public function addActionsAndFilters() {
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Called by install() to create any database tables if needed.
     * Best Practice:
     * (1) Prefix all table names with $wpdb->prefix
     * (2) make table names lower case only
     * @return void
     */
    protected function installDatabaseTables() {
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Drop plugin-created tables on uninstall.
     * @return void
     */
    protected function unInstallDatabaseTables() {
    }

    /**
     * Override to add any additional actions to be done at install time
     * See: http://plugin.michael-simpson.com/?page_id=33
     * @return void
     */
    protected function otherInstall() {
    }

    /**
     * Override to add any additional actions to be done at uninstall time
     * See: http://plugin.michael-simpson.com/?page_id=33
     * @return void
     */
    protected function otherUninstall() {
    }

    /**
     * Puts the configuration page in the Plugins menu by default.
     * Override to put it elsewhere or create a set of submenus
     * Override with an empty implementation if you don't want a configuration page
     * @return void
     */
    public function addSettingsSubMenuPage() {
        $this->addSettingsSubMenuPageToPluginsMenu();
        //$this->addSettingsSubMenuPageToSettingsMenu();
    }

    public function renderCodeTag() {
        $appId = $this->getOption('cs_chat_app_id');
        $enablePlugin = $this->getOption('cs_chat_enabled');
        $enableTrack = $this->getOption('cs_chat_track_pv');

        if(!$appId || !$enablePlugin || 'no' == $enablePlugin) {
            return;
        }

        $code = '<script type="text/javascript">'
            .'!function(){var a=window.crowdstream=window.crowdstream||{};'
            .'if("function"!=typeof a.load){a._preload=[],a.events={};'
            .'for(var b=["page","track","custom","identify","logout","cart",'
            .'"checkout","addItems","addItem","prospect","form","on"];b.length;)'
            .'{var c=b.shift();a.events[c]=function(b){return function(){a._preload.push'
            .'([b,arguments])}}(c)}a.load=function(b){var c=document.createElement'
            .'("script");c.type="text/javascript",c.async=!0,c.src=("https:"==='
            .'document.location.protocol?"https://":"http://")+"s3.eu-central-1.amazonaws.'
            .'com/crowdstream/crowdstream.js";var d=document.getElementsByTagName("script")'
            .'[0];d.parentNode.insertBefore(c,d),a.ready=function(){a.appId(b)}},a.load'
            .'("' . $appId . '")}}();'
            .'</script>';

        if('yes' == $enableTrack) {
            $code .= '<script type="text/javascript">crowdstream.events.page();</script>';
        }

        $logged_in = (is_user_logged_in()) ? 'yes' : 'no';

		if('yes' == $logged_in) {
			$userId      = get_current_user_id();
			$currentUser = get_userdata($userId);

            $userData = array(
                'username'   => $currentUser->user_login,
                'email'      => $currentUser->user_email,
                'first_name' => $currentUser->first_name,
                'last_name'  => $currentUser->last_name,
                'website'    => $currentUser->user_url
            );

            $code .= '<script type="text/javascript">'
                .'crowdstream.events.identify("' . $userId . '", '.json_encode($userData).');</script>';
		}

        echo $code;
    }

    protected function requireExtraPluginFiles() {
        require_once(ABSPATH . 'wp-includes/pluggable.php');
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }

    /**
     * @return string Slug name for the URL to the Setting page
     * (i.e. the page for setting options)
     */
    protected function getSettingsSlug() {
        return get_class($this) . 'Settings';
    }

    protected function addSettingsSubMenuPageToPluginsMenu() {
        $this->requireExtraPluginFiles();
        $displayName = $this->getPluginDisplayName();
        add_submenu_page('plugins.php',
                         $displayName,
                         $displayName,
                         'manage_options',
                         $this->getSettingsSlug(),
                         array(&$this, 'settingsPage'));
    }


    protected function addSettingsSubMenuPageToSettingsMenu() {
        $this->requireExtraPluginFiles();
        $displayName = $this->getPluginDisplayName();
        add_options_page($displayName,
                         $displayName,
                         'manage_options',
                         $this->getSettingsSlug(),
                         array(&$this, 'settingsPage'));
    }

    /**
     * @param  $name string name of a database table
     * @return string input prefixed with the WordPress DB table prefix
     * plus the prefix for this plugin (lower-cased) to avoid table name collisions.
     * The plugin prefix is lower-cases as a best practice that all DB table names are lower case to
     * avoid issues on some platforms
     */
    protected function prefixTableName($name) {
        global $wpdb;
        return $wpdb->prefix .  strtolower($this->prefix($name));
    }


    /**
     * Convenience function for creating AJAX URLs.
     *
     * @param $actionName string the name of the ajax action registered in a call like
     * add_action('wp_ajax_actionName', array(&$this, 'functionName'));
     *     and/or
     * add_action('wp_ajax_nopriv_actionName', array(&$this, 'functionName'));
     *
     * If have an additional parameters to add to the Ajax call, e.g. an "id" parameter,
     * you could call this function and append to the returned string like:
     *    $url = $this->getAjaxUrl('myaction&id=') . urlencode($id);
     * or more complex:
     *    $url = sprintf($this->getAjaxUrl('myaction&id=%s&var2=%s&var3=%s'), urlencode($id), urlencode($var2), urlencode($var3));
     *
     * @return string URL that can be used in a web page to make an Ajax call to $this->functionName
     */
    public function getAjaxUrl($actionName) {
        return admin_url('admin-ajax.php') . '?action=' . $actionName;
    }

}
