<?php

/**
 * @file
 * Administration pages for isfield settings.
 */

/**
 * Form callback; List/modify predefined dimensions.
 */
function isfield_predefined_dimensions($form, &$form_state) {
  $video_dimensions = variable_get('isfield_video_dimensions', "420x315\n480x360\n640x480\n960x720");
  $form['isfield_video_dimensions'] = array(
    '#title' => t('Predefined video dimensions'),
    '#type' => 'textarea',
    '#default_value' => $video_dimensions,
  );

  $thumbnail_dimensions = variable_get('isfield_thumbnail_dimensions', "140x105\n120x90");
  $form['isfield_thumbnail_dimensions'] = array(
    '#title' => t('Predefined thumbnail dimensions'),
    '#type' => 'textarea',
    '#default_value' => $thumbnail_dimensions,
  );

  return system_settings_form($form);
}

