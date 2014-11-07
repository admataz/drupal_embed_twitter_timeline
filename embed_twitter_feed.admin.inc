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
    'defaults' => array(
      'consumer_key' => '',
      'consumer_secret' => '',
      'timelines' => array(),
      'tweetcount' => 20,
      'retweets' => 0,
      'replies' => 0,

    ) 
  );

  
  $options = array_merge($default_settings, $saved_settings);
  $form['defaults'] = array(
    '#type' => 'markup',
    '#markup' => t('<p>Use the form below to configure the connection to the Twitter API.</p>')
  );
  
  $form['defaults'] = array(
    '#type' => 'fieldset',
    '#title' => 'Twitter Settings',
    '#tree' => TRUE
  );
  
  $form['defaults']['consumer_key'] = array(
    '#type' => 'textfield',
    '#title' => t('Twitter App Consumer Key') ,
    '#default_value' => $options['defaults']['consumer_key'],
  );
  
  $form['defaults']['consumer_secret'] = array(
    '#type' => 'textfield',
    '#title' => t('Twitter App Consumer Secret') ,
    '#default_value' => $options['defaults']['consumer_secret'],
  );
  
  $form['defaults']['timelines'] = array(
    '#type' => 'textfield',
    '#title' => t('Default timeline to follow') ,
    '#description' => t('This module will allow you to follow and embed a user timeline. Add the twitter screen name for the account that you want to follow. eg: drupal, barakobama, mrscruff1. Further timelines can be added using hooks from this module\'s API' ),
    '#default_value' => $options['defaults']['timelines'],
  );

  $form['defaults']['tweetcount'] = array(
    '#type' => 'textfield',
    '#size' => 3,
    '#title' => t('Tweets to get') ,
    '#description' => t('How many tweets do you want to get? (default 10, max 100)' ),
    '#default_value' => $options['defaults']['tweetcount'],
  );

   $form['defaults']['retweets'] = array(
    '#type' => 'checkbox',
    '#title' => t('Include retweets?') ,
    '#description' => t('Off by default' ),
    '#default_value' => $options['defaults']['retweets'],
  );

   $form['defaults']['replies'] = array(
    '#type' => 'checkbox',
    '#title' => t('Include replies?') ,
    '#description' => t('Off by default' ),
    '#default_value' => $options['defaults']['replies'],
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
    'embed_twitter_feed_config_submit'
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



