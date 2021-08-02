<?php
/**
 * plugin name: Censor word
 * description: Creates an interfaces to manage store / business locations on your website. Useful for showing location based information quickly. Includes both a widget and shortcode for ease of use.
 * version: 1.1.0
 * author: hicham
 * author uri: https://www.google.com
 */

if ( ! defined("ABSPATH") ) {
    exit("you cannot acces this page direclty");
}

 class WPCensorWordPlugin {
     function __construct() {
         add_action("admin_menu", array($this, "addmenu"));
         add_action("admin_init", array($this, "wpcensorsettings"));
         add_filter("the_content", array($this, "contenthandler"));
     }

     function contenthandler($content) {
         if(get_option("wp_censor_text")) {
             $wordstoarray = explode(",", get_option("wp_censor_text"));
             $trimwordstoarray = array_map('trim', $wordstoarray);
             return str_ireplace($trimwordstoarray, esc_html(get_option("wp_censor_setting_text", "***")), $content);
         }
     }


     function addmenu() {
         $mainmenupage = add_menu_page( "WP censor word", "WP censor", "manage_options", "wp-censor-word", array($this, "menuHTML"), 'data:image/svg+xml;base64,PHN2ZyBhcmlhLWhpZGRlbj0idHJ1ZSIgaGVpZ2h0PSIyMHB4IiB3aWR0aD0iMjBweCIgZm9jdXNhYmxlPSJmYWxzZSIgZGF0YS1wcmVmaXg9ImZhciIgZGF0YS1pY29uPSJoYW5kLXBhcGVyIiBjbGFzcz0ic3ZnLWlubGluZS0tZmEgZmEtaGFuZC1wYXBlciBmYS13LTE0IiByb2xlPSJpbWciIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgdmlld0JveD0iMCAwIDQ0OCA1MTIiPjxwYXRoIGZpbGw9ImN1cnJlbnRDb2xvciIgZD0iTTM3Mi41NyAxMTIuNjQxdi0xMC44MjVjMC00My42MTItNDAuNTItNzYuNjkxLTgzLjAzOS02NS41NDYtMjUuNjI5LTQ5LjUtOTQuMDktNDcuNDUtMTE3Ljk4Mi43NDdDMTMwLjI2OSAyNi40NTYgODkuMTQ0IDU3Ljk0NSA4OS4xNDQgMTAydjEyNi4xM2MtMTkuOTUzLTcuNDI3LTQzLjMwOC01LjA2OC02Mi4wODMgOC44NzEtMjkuMzU1IDIxLjc5Ni0zNS43OTQgNjMuMzMzLTE0LjU1IDkzLjE1M0wxMzIuNDggNDk4LjU2OWEzMiAzMiAwIDAgMCAyNi4wNjIgMTMuNDMyaDIyMi44OTdjMTQuOTA0IDAgMjcuODM1LTEwLjI4OSAzMS4xODItMjQuODEzbDMwLjE4NC0xMzAuOTU4QTIwMy42MzcgMjAzLjYzNyAwIDAgMCA0NDggMzEwLjU2NFYxNzljMC00MC42Mi0zNS41MjMtNzEuOTkyLTc1LjQzLTY2LjM1OXptMjcuNDI3IDE5Ny45MjJjMCAxMS43MzEtMS4zMzQgMjMuNDY5LTMuOTY1IDM0Ljg4NkwzNjguNzA3IDQ2NGgtMjAxLjkyTDUxLjU5MSAzMDIuMzAzYy0xNC40MzktMjAuMjcgMTUuMDIzLTQyLjc3NiAyOS4zOTQtMjIuNjA1bDI3LjEyOCAzOC4wNzljOC45OTUgMTIuNjI2IDI5LjAzMSA2LjI4NyAyOS4wMzEtOS4yODNWMTAyYzAtMjUuNjQ1IDM2LjU3MS0yNC44MSAzNi41NzEuNjkxVjI1NmMwIDguODM3IDcuMTYzIDE2IDE2IDE2aDYuODU2YzguODM3IDAgMTYtNy4xNjMgMTYtMTZWNjdjMC0yNS42NjMgMzYuNTcxLTI0LjgxIDM2LjU3MS42OTFWMjU2YzAgOC44MzcgNy4xNjMgMTYgMTYgMTZoNi44NTZjOC44MzcgMCAxNi03LjE2MyAxNi0xNlYxMDEuMTI1YzAtMjUuNjcyIDM2LjU3LTI0LjgxIDM2LjU3LjY5MVYyNTZjMCA4LjgzNyA3LjE2MyAxNiAxNiAxNmg2Ljg1N2M4LjgzNyAwIDE2LTcuMTYzIDE2LTE2di03Ni4zMDljMC0yNi4yNDIgMzYuNTctMjUuNjQgMzYuNTctLjY5MXYxMzEuNTYzeiI+PC9wYXRoPjwvc3ZnPg==', 50 );
         add_submenu_page( "wp-censor-word", "WP censor word", "Censor", "manage_options", "wp-censor-word", array($this, "menuHTML"));
         add_submenu_page( "wp-censor-word", "WP censor settings", "settings", "manage_options", "wp-censor-word-settings", array($this, "submenuHTML"));
         add_action("load-{$mainmenupage}", array($this, "loadscripts"));
     }

     function loadscripts() {
         wp_enqueue_style("wpcensorcss", plugin_dir_url(__FILE__) . "styles.css");
     }

     function formhandle() {
         if(wp_verify_nonce( $_POST["mynonce"], "noncefieldmethod") && current_user_can("manage_options")) {
            update_option( "wp_censor_text", sanitize_textarea_field($_POST["wp_censor_word_text"]) ) ?>
            <div class="updated">
                <p>words succesfully saved to database.</p>
            </div>
         <?php } else { ?>
            <div class="error">
                <p>somthing went wrong.</p>
            </div>
         <?php }
    }

     function menuHTML() { ?>
         <div class="wrap">
             <h1>WP censor word</h1>
             <?php 
                if($_POST["checkvalidinput"] == "true") $this->formhandle() ?>
             <form method="POST">
                 <input type="hidden" name="checkvalidinput" value="true">
                 <?php wp_nonce_field( "noncefieldmethod", "mynonce") ?>
                 <label for="censortextarea"><p>Enter a word followed by <strong>comma, space</strong> to censor your desired words.</p></label>
                 <textarea name="wp_censor_word_text" id="censortextarea" placeholder="bad, mean, silly"><?php echo esc_textarea(get_option( "wp_censor_text", "bad, mean" )) ?></textarea>
                 <input type="submit" value="save changes" class="button button-primary">
             </form>
         </div>
     <?php }

     function wpcensorsettings() {
         add_settings_section( "wp_settings_section", null, null, "wp-censor-word-settings" );
         register_setting( "wpcensorgroup", "wp_censor_setting_text", array(
             "sanitize_callback" => "sanitize_text_field",
             "default" => "***"
         ));
         add_settings_field( "wp_censor_setting_text", "Choose your text", array($this, "settingHTML"), "wp-censor-word-settings", "wp_settings_section");
     }

     function settingHTML() { ?>
        <input type="text" name="wp_censor_setting_text" value="<?php echo get_option("wp_censor_setting_text", "***") ?>">
        <p class="description">leave blank to disable censor word.</p>
     <?php }

     function submenuHTML() { ?>
        <div class="wrap">
            <h1>WP censor settings</h1>
            <form action="options.php" method="POST">
                <?php
                    settings_errors();
                    settings_fields( "wpcensorgroup" );
                    do_settings_sections( "wp-censor-word-settings" );
                    submit_button();
                ?>
            </form>
        </div>
     <?php }
 }

 $wpcensorwordplugin = new WPCensorWordPlugin();