<?php



/**
 * Implements hook_form().
 */
function embed_twitter_feed_config_form($form, &$form_state) {
  // Wrapper div that goes around the entire form.
  $form['#prefix'] = '<div class="fapi-form">';
  $form['#suffix'] = '</div>';
  
  $saved_settings = variable_get('embed_twitter_feed_settings', array());
  
  $default_settings = array(
    'twitter' => array(
      'consumer_key' => '',
      'consumer_secret' => ''
    ) 
  );
  
  $options = array_merge($default_settings, $saved_settings);
  $form['overview'] = array(
    '#type' => 'markup',
    '#markup' => t('<p>Use the form below to configure the connection to the Twitter API.</p>')
  );
  
  $form['twitter'] = array(
    '#type' => 'fieldset',
    '#title' => 'Twitter Settings',
    '#tree' => TRUE
  );
  
  $form['twitter']['consumer_key'] = array(
    '#type' => 'textfield',
    '#title' => t('Twitter App Consumer Key') ,
    '#default_value' => $options['twitter']['consumer_key'],
  );
  
  $form['twitter']['consumer_secret'] = array(
    '#type' => 'textfield',
    '#title' => t('Twitter App Consumer Secret') ,
    '#default_value' => $options['twitter']['consumer_secret'],
  );
  
  $form['twitter']['timelines'] = array(
    '#type' => 'textfield',
    '#title' => t('Timelines to follow') ,
    '#description' => t('This module will allow you to follow and embed user timelines. Add a comma, separated, list of twitter screen names that you want to follow. Each timeline will be available as a separate variable. eg: drupal, barakobama, mrscruff1' ),
    '#default_value' => $options['twitter']['timelines'],
  );

  $form['twitter']['tweetcount'] = array(
    '#type' => 'textfield',
    '#size' => 3,
    '#title' => t('Tweets to get') ,
    '#description' => t('How many tweets do you want to get? (default 10, max 100)' ),
    '#default_value' => $options['twitter']['tweetcount'],
  );

   $form['twitter']['retweets'] = array(
    '#type' => 'checkbox',
    '#title' => t('Include retweets?') ,
    '#description' => t('Off by default' ),
    '#default_value' => $options['twitter']['retweets'],
  );

   $form['twitter']['replies'] = array(
    '#type' => 'checkbox',
    '#title' => t('Include replies?') ,
    '#description' => t('Off by default' ),
    '#default_value' => $options['twitter']['replies'],
  );
 
  
  $form['#theme'] = 'system_settings_form';
  
  $form['actions'] = array(
    '#type' => 'actions',
    'submit' => array(
      '#type' => 'submit',
      '#value' => 'Save API settings'
    )
  );
  
  $form['#submit'] = array(
    'embed_twitter_feed_settings_submit'
  );
  return $form;
}



/**
 * Save the settings 
 */
function embed_twitter_feed_config_submit($form, &$form_state) {
  form_state_values_clean($form_state);
  variable_set('embed_twitter_feed_settings', $form_state['values']);
  drupal_set_message(t('Twitter API Settings and options were saved') , 'status', FALSE);
}



