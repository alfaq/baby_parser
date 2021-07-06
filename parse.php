<?php

$file_name = 'PH_content_export_elearn_article_2021-06-15.csv';
$path_to_file = 'content/' . $file_name;
$field_image_name = 'field_image';

$title_field = 'title_field';
$field_image_alt_name = 'field_image_alt';

$new_path = '/sites/default/files/migration_images/';

$field_title_key = NULL;
$field_image_key = NULL;
$field_image_alt_key = NULL;

//Read csv in array
$csv = [];
if (($handle = fopen($path_to_file, 'r')) !== FALSE) {
  while (($data = fgetcsv($handle, 0, ';', '~')) !== FALSE) {
    $csv[] = $data;
  }

  //Try to find key for column with $field_image_name
  $field_key = NULL;
  if (!empty($csv[0])) {
    $field_title_key = array_search($title_field, $csv[0]);
    $field_image_key = array_search($field_image_name, $csv[0]);
    $field_image_alt_key = array_search($field_image_alt_name, $csv[0]);
  }
  else {
    print '<h2 style="color: red">Empty headers</h2>';
  }

  //Get all values from column $field_image_name
  $field_values = [];
  if (!empty($field_image_key)) {
    print '<h2 style="color: green">Count rows '.count($csv).'</h2>';
    print '<table border="1"><tr><th>Old path</th><th>New Path</th></tr>';
    foreach ($csv as $key => &$c) {
      if ($key == 0) {//don't get header
        continue;
      }
      if (!empty($c[$field_image_key])) {
        $value = $c[$field_image_key];
        $arr_srt = explode('/', $value);
        $new_str = $new_path . $arr_srt[count($arr_srt) - 1];
        $c[$field_image_key] = $new_str;
        print '<tr><td>' . $value . '</td><td>' . $new_str . '</td></tr>';
      }
    }
    print '</table>';
  }
  else {
    print '<h2 style="color: red">Can\'t find key for your field:'.$field_image_name.'</h2>';
  }

  //Fill alt for image from title
  if (!empty($field_title_key) && !empty($field_image_alt_key)) {
    foreach ($csv as $key => &$c) {
      if ($key == 0) {//don't change header
        continue;
      }
      if (empty($c[$field_image_alt_key]) && !empty($c[$field_image_key])) {
        $c[$field_image_alt_key] = $c[$field_title_key];
      }
    }


  }
  fclose($handle);
  if (!empty($csv)) {
    $handle_new = fopen(str_replace('.csv', '_NEW.csv', $path_to_file), 'w');
    foreach ($csv as $c) {
      fputcsv($handle_new, $c, ';', '~');
    }
    fclose($handle_new);
  }
}