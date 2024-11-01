<?php
/*
  Plugin Name: Stickybar Tool
  Plugin URI:  #
  Description: To show stickybar on your page
  Version:     1.0
  Author:      Ahmad Asjad
  Author URI:  https://profiles.wordpress.org/ahmadasjad
  License:     GPLv2 or later
  License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace StickybarTool;

use WP_Widget;

class Stickybar extends WP_Widget
{

    /**
     * Sets up the widgets name etc
     */
    public function __construct()
    {
        $widget_ops = array();
        parent::__construct('stickybar-tool_stickybar', 'Stickybar Tool', $widget_ops);
    }

    /**
     * Outputs the content of the widget
     *
     * @param array $args
     * @param array $instance
     */
    public function widget($args, $instance)
    {
        $messages = $instance['messages'];
        $msg_index = $this->get_msg_index($instance);
        $seconds = $instance['seconds'];
        $hide_delay = $instance['hide_delay'];
        $show_delay = $instance['show_delay'];
        $custom_css = $instance['custom_css'];
        $back_color = $instance['back_color'];
        $text_color = $instance['text_color'];
        $link_color = $instance['link_color'];
        ?>
        <style>
            .stickybar-tool_stickybar{
                position: fixed;
                top: 0;
                left: 0;
                z-index: 999;
                width: 100%;
                text-align: center;
                background-color: <?php echo $back_color; ?>;
                padding: 10px 6px;
                color: <?php echo $text_color; ?>;
                height: 50px;
                font-weight: 400;
                font-size: 17px;
                display: table;
            }
            .stickybar-tool_stickybar .close{
                position: absolute;
                right: 0;
                width: 30px;
                height: 30px;
                border-radius: 4px;
                border:1px solid #ccc;
                background-color: rgba(0,0,0,0.5);
                font-size: 20px;
                cursor: pointer;
            }
            .stickybar-tool_stickybar a{
                color:<?php echo $link_color; ?>;
            }
            <?php echo $custom_css; ?>
        </style>
        <script>
            jQuery(document).ready(function () {
                jQuery('.stickybar-tool_stickybar').hide();
                jQuery(document).on('click', '.stickybar-tool_stickybar .close', function () {
                    jQuery(this).parents('.stickybar-tool_stickybar').slideUp(<?php echo $hide_delay; ?>);
                    //                    jQuery(this).parents('.stickybar-tool_stickybar').remove();
                    jQuery('body').css('margin-top', '0');
                });
                jQuery(window).load(function () {
                    setTimeout(function () {
                        jQuery('.stickybar-tool_stickybar').slideDown(<?php echo $show_delay; ?>);
                    }, (<?php echo $seconds; ?> * 1000));
                });
            });
        </script>
        <div class="stickybar-tool_stickybar" style="<?php echo (is_user_logged_in()) ? 'top:30px;' : ''; ?>">
            <?php echo $messages[$msg_index]; ?>
            <span class="close">X</span>
        </div>
        <?php
    }

    private function get_msg_index($instance)
    {
        $msg_index = 0;
        $total_msg = count($instance['messages']);
        $msg_index = ($total_msg > 0) ? rand(0, $total_msg - 1) : 0;
        $stickybarTool_stickybar_sort = get_option('stickybar-tool_stickybar_sort');
        if ($instance['rotation'] == 'ascending') {
            $msg_index = $stickybarTool_stickybar_sort;
            $next_index = ($stickybarTool_stickybar_sort >= $total_msg - 1) ? 0 : $stickybarTool_stickybar_sort + 1;
            update_option("stickybar-tool_stickybar_sort", $next_index);
        } elseif ($instance['rotation'] == 'descending') {
            $msg_index = $stickybarTool_stickybar_sort;
            $next_index = ($stickybarTool_stickybar_sort <= 0) ? $total_msg - 1 : $stickybarTool_stickybar_sort - 1;
            update_option("stickybar-tool_stickybar_sort", $next_index);
        }
        return $msg_index;
    }

    /**
     * Outputs the options form on admin
     *
     * @param array $instance The widget options
     */
    public function form($instance)
    {
        $messages = (empty($instance['messages']) || !is_array($instance)) ? array('') : $instance['messages'];
        $rotation = (empty($instance['rotation'])) ? '' : $instance['rotation'];
        $seconds = (empty($instance['seconds'])) ? '2' : $instance['seconds'];
        $hide_delay = (empty($instance['hide_delay'])) ? '500' : $instance['hide_delay'];
        $show_delay = (empty($instance['show_delay'])) ? '500' : $instance['show_delay'];
        $custom_css = (empty($instance['custom_css'])) ? '' : $instance['custom_css'];
        $back_color = (empty($instance['back_color'])) ? '#51e643' : $instance['back_color'];
        $text_color = (empty($instance['text_color'])) ? '#000' : $instance['text_color'];
        $link_color = (empty($instance['link_color'])) ? '#fff' : $instance['link_color'];
        ?>

        <fieldset style="border:1px solid #CCC;padding: 5px;">
            <legend>Message Options</legend>
            <ul id="stickybar-tool_sticky_sortable"><?php
                foreach ($messages as $msg_key => $msg_val) {
                    ?>
                    <li class="clearfix js-field_option" style="border: 1px solid #CCC;clear: both;">
                        <div style="width: 80%;float: left;">
                            <textarea name="<?php echo esc_attr($this->get_field_name('messages[]')); ?>" type="text" placeholder="Option Name" class="form-control"><?php echo $msg_val; ?></textarea>
                        </div>
                        <div style="width: 20%;float: right;">
                            <a class="button button-primary" js-add=".js-field_option" style="width: 30px;">+</a>
                            <a class="button button-cancel" js-remove=".js-field_option" style="width: 30px;">-</a>
                        </div>
                    </li>
                    <?php
                }
                ?>
            </ul>
        </fieldset>

        <label style="">Rotation</label><br/><?php
        foreach (array('random' => 'Random', 'ascending' => 'Ascending', 'descending' => 'Descending',) as $key => $val) {
            $selected = ($rotation == $key) ? 'checked="checked"' : '';
            ?>
            <input type="radio" name="<?php echo esc_attr($this->get_field_name('rotation')); ?>" value="<?php echo $key; ?>" <?php echo $selected; ?>/> <?php echo $val; ?>
            <?php
        }
        ?>

        <label>Show after second</label>
        <input type="number" name="<?php echo esc_attr($this->get_field_name('seconds')); ?>" value="<?php echo $seconds; ?>" placeholder="in second"/>

        <label>Delay when hiding(in mili sec)</label>
        <input type="number" name="<?php echo esc_attr($this->get_field_name('hide_delay')); ?>" value="<?php echo $hide_delay; ?>" placeholder="in second"/>

        <label>Delay when showing(in mili sec)</label>
        <input type="number" name="<?php echo esc_attr($this->get_field_name('show_delay')); ?>" value="<?php echo $show_delay; ?>" placeholder="in second"/>

        <label>Custom CSS</label>
        <textarea name="<?php echo esc_attr($this->get_field_name('custom_css')); ?>" type="text" placeholder="Must use .stickybar-tool_stickybar as prefix before element style" class="form-control"><?php echo $custom_css; ?></textarea>

        <label>Background color</label><br/>
        <input class="color-field" name="<?php echo esc_attr($this->get_field_name('back_color')); ?>" value="<?php echo $back_color; ?>"/>

        <br/><label>Text color</label><br/>
        <input class="color-field" name="<?php echo esc_attr($this->get_field_name('text_color')); ?>" value="<?php echo $text_color; ?>"/>

        <br/><label>Link color</label><br/>
        <input class="color-field" name="<?php echo esc_attr($this->get_field_name('link_color')); ?>" value="<?php echo $link_color; ?>"/>

        <br/><br/>
        <?php
    }

    /**
     * Processing widget options on save
     *
     * @param array $new_instance The new options
     * @param array $old_instance The previous options
     */
    public function update($new_instance, $old_instance)
    {
        $instance['messages'] = (!empty($new_instance['messages'])) ? $new_instance['messages'] : '';
        $instance['rotation'] = (!empty($new_instance['rotation'])) ? $new_instance['rotation'] : '';
        $instance['seconds'] = (!empty($new_instance['seconds'])) ? $new_instance['seconds'] : '0';
        $instance['hide_delay'] = (!empty($new_instance['hide_delay'])) ? $new_instance['hide_delay'] : '0';
        $instance['show_delay'] = (!empty($new_instance['show_delay'])) ? $new_instance['show_delay'] : '0';
        $instance['custom_css'] = (!empty($new_instance['custom_css'])) ? strip_tags($new_instance['custom_css']) : '';
        $instance['back_color'] = (!empty($new_instance['back_color'])) ? strip_tags($new_instance['back_color']) : '#51e643';
        $instance['text_color'] = (!empty($new_instance['text_color'])) ? strip_tags($new_instance['text_color']) : '#000';
        $instance['link_color'] = (!empty($new_instance['link_color'])) ? strip_tags($new_instance['link_color']) : '#fff';
        return $instance;
    }

}

add_action('widgets_init', function() {
    register_widget(new Stickybar());
});

add_action('admin_enqueue_scripts', function () {
    wp_enqueue_script("stickybar-tool_stickybar_admin", plugin_dir_url(__FILE__) . '/js/admin_custom.js');
    wp_enqueue_style("stickybar-tool_stickybar_admin", plugin_dir_url(__FILE__) . '/css/admin_custom.css');

    // Add the color picker css file
    wp_enqueue_style('wp-color-picker');
    // Include our custom jQuery file with WordPress Color Picker dependency
    wp_enqueue_script("wp-color-picker");
});
