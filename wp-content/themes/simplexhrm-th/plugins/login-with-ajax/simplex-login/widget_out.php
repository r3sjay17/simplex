<?php
/*
 * This is the page users will see logged out.
 * You can edit this, but for upgrade safety you should copy and modify this file into your template folder.
 * The location from within your template folder is plugins/login-with-ajax/ (create these directories if they don't exist)
*/


$lwa_data['redirect'] = admin_url();

?>
<div class="lwa lwa-simplex-login out"><?php //class must be here, and if this is a template, class name should be that of template directory ?>
    <form class="lwa-form" action="<?php echo esc_attr(LoginWithAjax::$url_login); ?>" method="post">

        <div>
            <span class="lwa-status"></span>


            <div class="top-label">
                <div class="row " >
                    <div class="columns medium-6"><span class="login-welcome"><?php esc_html_e( 'Welcome','simplex-hrm' ) ?></span></div>
                    <div class="columns medium-6 text-right"><span class="login-account-label"><i class="fa fa-user"></i> <?php esc_html_e( 'My Account','simplex-hrm' ) ?></span></div>
                </div>
            </div>

            <div class="row" >
                <div class="column" ><input type="text" name="log" placeholder="<?php esc_html_e( 'Username','login-with-ajax' ) ?>" /></div>
                <div class="column" ><input type="password" name="pwd" placeholder="<?php esc_html_e( 'Password','login-with-ajax' ) ?>" /></div>

                <?php do_action('login_form'); ?>

                <div class="column" >
                    <a class="lwa-links-remember" href="<?php echo esc_attr(LoginWithAjax::$url_remember); ?>" title="<?php esc_attr_e('Password Lost and Found','login-with-ajax') ?>"><?php esc_attr_e('Forget your password?','login-with-ajax') ?></a>
                </div>

                <div class="column" >
                    <button type="submit" name="wp-submit" id="lwa_wp-submit" class="button primary login-submit" tabindex="100" ><?php esc_attr_e('Log In', 'login-with-ajax'); ?> <i class="fa fa-caret-right" aria-hidden="true"></i></button>
                    <input type="hidden" name="lwa_profile_link" value="<?php echo esc_attr($lwa_data['profile_link']); ?>" />
                    <input type="hidden" name="login-with-ajax" value="login" />
                    <?php if( !empty($lwa_data['redirect']) ): ?>
                    <input type="hidden" name="redirect_to" value="<?php echo esc_url($lwa_data['redirect']); ?>" />
                    <?php endif; ?>
                </div>

                <div class="column" >
                    <?php if( !empty($lwa_data['remember']) ): ?>
                    <input name="rememberme" type="checkbox" class="lwa-rememberme" value="forever" /> <label><?php esc_html_e( 'Remember Me','login-with-ajax' ) ?></label>
                    <?php endif; ?>
                    <?php if ( get_option('users_can_register') && !empty($lwa_data['registration']) ) : ?>
                    <a href="<?php echo esc_attr(LoginWithAjax::$url_register); ?>" class="lwa-links-register lwa-links-modal"><?php esc_html_e('Register','login-with-ajax') ?></a>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </form>
    <?php if( !empty($lwa_data['remember']) && $lwa_data['remember'] == 1 ): ?>
    <form class="lwa-remember" action="<?php echo esc_attr(LoginWithAjax::$url_remember) ?>" method="post" style="display:none;">
        <div>
            <span class="lwa-status"></span>
            <table>
                <tr>
                    <td>
                        <strong><?php esc_html_e("Forgotten Password", 'login-with-ajax'); ?></strong>
                    </td>
                </tr>
                <tr>
                    <td class="lwa-remember-email">
                        <?php $msg = __("Enter username or email", 'login-with-ajax'); ?>
                        <input type="text" name="user_login" class="lwa-user-remember" value="<?php echo esc_attr($msg); ?>" onfocus="if(this.value == '<?php echo esc_attr($msg); ?>'){this.value = '';}" onblur="if(this.value == ''){this.value = '<?php echo esc_attr($msg); ?>'}" />
                        <?php do_action('lostpassword_form'); ?>
                    </td>
                </tr>
                <tr>
                    <td class="lwa-remember-buttons">
                        <input type="submit" value="<?php esc_attr_e("Get New Password", 'login-with-ajax'); ?>" class="lwa-button-remember" />
                        <a href="#" class="lwa-links-remember-cancel"><?php esc_html_e("Cancel", 'login-with-ajax'); ?></a>
                        <input type="hidden" name="login-with-ajax" value="remember" />
                    </td>
                </tr>
            </table>
        </div>
    </form>
    <?php endif; ?>
    <?php if( get_option('users_can_register') && !empty($lwa_data['registration']) && $lwa_data['registration'] == 1 ): ?>
    <div class="lwa-register lwa-register-default lwa-modal" style="display:none;">
        <h4><?php esc_html_e('Register For This Site','login-with-ajax') ?></h4>
        <p><em class="lwa-register-tip"><?php esc_html_e('A password will be e-mailed to you.','login-with-ajax') ?></em></p>
        <form class="lwa-register-form" action="<?php echo esc_attr(LoginWithAjax::$url_register); ?>" method="post">
            <div>
                <span class="lwa-status"></span>
                <p class="lwa-username">
                    <label><?php esc_html_e('Username','login-with-ajax') ?><br />
                        <input type="text" name="user_login" id="user_login" class="input" size="20" tabindex="10" /></label>
                </p>
                <p class="lwa-email">
                    <label><?php esc_html_e('E-mail','login-with-ajax') ?><br />
                        <input type="text" name="user_email" id="user_email" class="input" size="25" tabindex="20" /></label>
                </p>
                <?php do_action('register_form'); ?>
                <?php do_action('lwa_register_form'); ?>
                <p class="submit">
                    <input type="submit" name="wp-submit" id="wp-submit" class="button-primary" value="<?php esc_attr_e('Register', 'login-with-ajax'); ?>" tabindex="100" />
                </p>
                <input type="hidden" name="login-with-ajax" value="register" />
            </div>
        </form>
    </div>
    <?php endif; ?>
</div>
