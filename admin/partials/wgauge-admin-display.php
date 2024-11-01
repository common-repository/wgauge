<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       www.jacobanderson.co.uk
 * @since      1.0.0
 *
 * @package    Wgauge
 * @subpackage Wgauge/admin/partials
 */

if( !current_user_can( 'manage_options' ) ) {
    wp_die( 'You do not have suggicient permissions to access this page.' );
}

if ( isset( $_POST['submit_logo_selector'] ) && isset( $_POST['logo_attachment_id'] ) ){
  update_option( 'media_selector_attachment_id', sanitize_text_field($_POST['logo_attachment_id'] ));
}

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
<div id="wg-stage">
  <div class="wg-row">
    <div class="wg-tile wg-logo-hero wg20">
      <a href="http://wgauge.com/"><img src="<?php echo Wgauge_Admin::wgGetImage('logo-big.png');?>"/></a>
      <!-- <img src="<?php //echo Wgauge_Admin::wgGetImage('i.svg');?>" class="wg-info"/> -->
    </div>
    <div id="wg-link-cards" class="wg-column wg20">
      
      <div class="wg-tile wg-button-tile wg-settings-button">
        <img src="<?php echo Wgauge_Admin::wgGetImage('menu-frame.png');?>"/>
        <p>Settings</p>
      </div>
      <a href="https://wgauge.com/" target="_BLANK">
      <div class="wg-tile wg-button-tile">
        <img src="<?php echo Wgauge_Admin::wgGetImage('premium.png');?>"/>
        <p>Premium</p>
      </div></a>
    </div>
    <div class="wg-tile wg60">
      <div class="wg-tile--head">
        <h2 class="wg-tile--title">Site Sentiment</h2>
        <div class="reportrange-wrapper">
          <div id="reportrange-sitesentiment" class="pull-right reportrange">
            <span></span> <b class="caret"></b>
          </div>
        </div>
      </div>
      <div class="wg-tile--content">
        <div class="wg-row">
          <div class="wg-component-graph wg-component wg100">
            <canvas id="siteGraph"></canvas>
          </div>
          <!-- <div class="wg-tile--rating wg-component wg15">
            <div class="wg-component-rating">
              <span class="wg-component-rating--rating"><p>B</p></span>
              <span class="wg-component-rating--score"><p>76/100</p></span>
            </div>
          </div> -->
        </div>
      </div>
    </div>
  </div>
  <div class="wg-row">
    <div class="wg-tile wg40">
      <div class="wg-tile--head">
        <h2 class="wg-tile--title">Pages</h2>
      </div>
      <ul class="wg-list">
        <ul id="wg-page-list--header" class="wg-list--row wg-list--header">
          <li>Page</li>
          <li> <img src="<?php echo Wgauge_Admin::wgGetImage('gauge-icon.png');?>"/></li>
          <li> <img style="width: 1rem" src="<?php echo Wgauge_Admin::wgGetImage('comment-icon.svg');?>"/></li>
          <li>Score</li>
          <li>Rating</li>
        </ul>
        <div class="page-list">
        <?php Wgauge_Admin::wgGetPages();?>
        </div>
      </ul>
    </div>
    <div class="wg-tile wg60">
      <div class="wg-tile--head">
        <h2 class="wg-tile--title"><span class="wg-tile-page--title">Home Page</span> Sentiment</h2>
        <div class="reportrange-wrapper">
        <div id="reportrange-pages" class="pull-right reportrange">
        <!-- <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp; -->
          <span></span> <b class="caret"></b>
        </div>
      </div>
      </div>
      <div class="wg-tile--content">
        <div class="wg-row">
          <div class="wg-tile--graph wg-component wg100">
          <canvas id="pageGraph"></canvas>
        </div>
        </div>
      </div>
    </div>
  </div>
  <div class="wg-row">
    <div class="wg-tile wg100">
      <div class="wg-tile--head">
        <h2 class="wg-tile--title"><span class="wg-tile-page--title">Home Page</span> Feedback</h2>
      </div>
      <div class="wg-tile--content">
        <div class="wg-row">
          <div class="wg-tile--messages wg-component wg100">
          <ul id="wg-page-comment-list" class="wg-list">
          </ul>
        </div>
        </div>
      </div>
    </div>
  </div>
 </div>
</div>
<div id="wg-settings-modal" class="wg-modal-toggle wg-modal-not-active">
  <div class="wg-tile wg-modal-toggle wg-modal-not-active">
    <div class="wg-tile--head">
      <h2 class="wg-tile--title">wGauge Settings</h2>
    </div>
    <form method="POST">
    <img class="wg-logo-preview" src="<?php //echo get_option( 'media_selector_attachment_id' )?>"/>
      <label for="logo_attachment_id">Change Feedback Dialogue Logo</label>
      <span><input type="text" name="logo_attachment_id" class="media-input" value="<?php echo get_option( 'media_selector_attachment_id' ); ?>"/>
      <button class="media-button">Select image</button></span>
      
      <input type="submit" name="submit_logo_selector" value="Save Settings">
    </form>
  </div>
</div>