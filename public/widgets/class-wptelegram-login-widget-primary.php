<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
* Adds WP Telegram Login widget
*/
class WPTelegram_Login_Widget_Primary extends WP_Widget {

    /**
    * Register widget with WordPress
    */
    public function __construct() {
        parent::__construct(
            'wptelegram_login_primary', // Base ID
            esc_html__( 'WP Telegram Login', 'wptelegram-login' ), // Name
            array( 'description' => esc_html__( 'Display the Telegram Log in button', 'wptelegram-login' ),
            ) // Args
        );
    }

    /**
     * Outputs the content for the widget.
     *
     * @since 1.0.0
     *
     * @param array $args     Display arguments including 'before_title', 'after_title',
     *                        'before_widget', and 'after_widget'.
     * @param array $instance Settings for the current Pages widget instance.
     */
    public function widget( $args, $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : '';

        /**
         * Filters the widget title.
         *
         * @since 1.0.0
         *
         * @param string $title    The widget title. Default 'Pages'.
         * @param array  $instance Array of settings for the current widget.
         * @param mixed  $id_base  The widget ID.
         */
        $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

        unset( $instance['title'] );

        $content = wptelegram_login( $instance, false );

        if ( ! empty( $content ) ) {
            echo $args['before_widget'];
            if ( $title ) {
                echo $args['before_title'] . $title . $args['after_title'];
            }
        ?>
        <div>
            <?php echo $content; ?>
        </div>
        <?php
            echo $args['after_widget'];
        }
    }

    /**
     * Handles updating settings for the widget instance.
     *
     * @since 1.0.0
     *
     * @param array $new_instance New settings for this instance as input by the user via
     *                            WP_Widget::form().
     * @param array $old_instance Old settings for this instance.
     * @return array Updated settings to save.
     */
    public function update( $new_instance, $old_instance ) {
        
        $instance = $old_instance;

        $instance['title'] = sanitize_text_field( $new_instance['title'] );

        if ( ! empty( $new_instance['button_style'] ) && in_array( $new_instance['button_style'], array( 'large', 'medium', 'small' ) ) ) {
            $instance['button_style'] = $new_instance['button_style'];
        } else {
            $instance['button_style'] = 'large';
        }

        $instance['show_user_photo'] = empty( $new_instance['show_user_photo'] ) ? 'off' : sanitize_text_field( $new_instance['show_user_photo'] );

        $instance['show_if_user_is'] = isset( $new_instance['show_if_user_is'] ) ? sanitize_text_field( $new_instance['show_if_user_is'] ) : 'logged_out';

        $corner_radius = empty( $new_instance['corner_radius'] ) ? '' : sanitize_text_field( $new_instance['corner_radius'] );
        if ( ! empty( $corner_radius ) ) {
            $corner_radius = absint( $corner_radius );
            if ( 'large' == $instance['button_style'] && $corner_radius > 20 )
                $corner_radius = 20;
            elseif ( 'medium' == $instance['button_style'] && $corner_radius > 14 ) {
                $corner_radius = 14;
            }
            elseif ( 'small' == $instance['button_style'] && $corner_radius > 10 ) {
                $corner_radius = 10;
            }
        }
        $instance['corner_radius'] = $corner_radius;

        return $instance;
    }

    /**
     * Outputs the settings form for the widget.
     *
     * @since 1.0.0
     *
     * @param array $instance Current settings.
     */
    public function form( $instance ) {

        $defaults = array(
            'title'             => '',
            'button_style'      => 'large',
            'show_user_photo'   => 'on',
            'show_if_user_is'   => '',
            'corner_radius'     => '',
        );

        $opts = WPTG_Login()->options();

        // use global options
        foreach ( $defaults as $key => $value ) {
            $defaults[ $key ] = $opts->get( $key );
        }
        $instance = wp_parse_args( (array) $instance, $defaults );

        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title' ); ?>:</label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
        </p>
        <hr>
        <p>
            <label for="button_style"><?php esc_html_e( 'Button Style', 'wptelegram-login' ); ?></label></br>

           <input type="radio" id="<?php echo $this->get_field_id( 'button_style1' ); ?>" value="large" name="<?php echo $this->get_field_name( 'button_style' ); ?>" <?php  checked( $instance[ 'button_style' ] ,'large' ); ?> >
            <label for="<?php echo $this->get_field_id( 'button_style1' ); ?>"><?php esc_html_e( 'Large', 'wptelegram-login' ); ?></label><br>

           <input type="radio" id="<?php echo $this->get_field_id( 'button_style2' ); ?>" value="medium" name="<?php echo $this->get_field_name( 'button_style' ); ?>" <?php  checked( $instance[ 'button_style' ] ,'medium' ); ?> >
            <label for="<?php echo $this->get_field_id( 'button_style2' ); ?>"><?php esc_html_e( 'Medium', 'wptelegram-login' ); ?></label><br>

           <input type="radio" id="<?php echo $this->get_field_id( 'button_style3' ); ?>" value="small" name="<?php echo $this->get_field_name( 'button_style' ); ?>" <?php  checked( $instance[ 'button_style' ] ,'small' ); ?> >
            <label for="<?php echo $this->get_field_id( 'button_style3' ); ?>"><?php esc_html_e( 'Small', 'wptelegram-login' ); ?></label>
        </p>
        <hr>
        <p>
            <input class="checkbox" type="checkbox" <?php checked( $instance[ 'show_user_photo' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'show_user_photo' ); ?>" name="<?php echo $this->get_field_name( 'show_user_photo' ); ?>" /> 
            <label for="<?php echo $this->get_field_id( 'show_user_photo' ); ?>"><?php esc_attr_e( 'Show User Photo', 'wptelegram-login' ); ?></label>
        </p>
        <hr>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'corner_radius' ) ); ?>"><?php _e( 'Corner Radius', 'wptelegram-login' ); ?></label>
            <input type="number" value="<?php echo esc_attr( $instance['corner_radius'] ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'corner_radius' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'corner_radius' ) ); ?>" class="widefat" />
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'show_if_user_is' ) ); ?>"><?php _e( 'Show if user is', 'wptelegram-login' ); ?></label>
            <?php $options = WPTelegram_Login_Admin::show_if_user_is_options_cb(); ?>
            <select name="<?php echo esc_attr( $this->get_field_name( 'show_if_user_is' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'show_if_user_is' ) ); ?>" class="widefat">
                <?php
                    $output = '';

                    foreach ( $options as $value => $label ) {
                        $output .= '<option value="' . $value . '" ' . selected( $instance['show_if_user_is'], $value, false ) . '>' . $label . '</option>';
                    }
                    echo $output;
                ?>
            </select>
        </p>
        <?php
    }
}