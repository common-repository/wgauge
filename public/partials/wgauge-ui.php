<?php

/**
 * Provide the wgauge UI element
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       www.jacobanderson.co.uk
 * @since      1.0.0
 *
 * @package    Wgauge
 * @subpackage Wgauge/public/partials
 */
?>
<div id="wg-gauge-attention" class="wg-gauge-tab"><p><?php echo $this->wg_get_attention_msg();?></p></div>
<div id="wg-gauge-box">
    <div id="wg-gauge-close" class="wg-gauge-tab"><img src="<?php echo plugins_url( 'img/close.svg', __FILE__ )?>" alt="wGauge Close Dialogue"/></div>
    <div class="wg-feedback">
        <div class="wg-logo"><img src="<?php echo Wgauge_Public::getLogo();?>" alt="Logo"/></div>
        <div id="wg-feedback-form">
            <form id = "wg-form">
                <span id="wgRange"><input type="range" min="1" max="100" value="50" class="slider" id="wgRangeInput" name="wg_rating"></span>
                <input type="hidden" name="wg_user" id="wg_user" value="<?php echo get_current_user_id();?>"/>   
                <textarea id='wg_feedback' name='wg_feedback' placeholder="Your comments"></textarea>
            </form>
            <div class="wg-feedback--actions">
                <button type="button" id="wgauge-reveal-feedback" class="wgauge-action-btn">Feedback</button>
                <button type="button" id="wgauge-submit-feedback" name='submit' class="wgauge-action-btn">Submit</button>
            </div>
        </div>
        <div id="wg-thank-you">
            <p>Thank you for your feedback, we will use this to improve our site</p>
        </div>
    </div>
</div>