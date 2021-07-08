<?php
$directory = 'content';
$files = [];
$dir = opendir($directory);
while($file = readdir($dir)){
  if ($file != '.' && $file != '..') {
    $files[] = $file;
  }
}

if (!empty($files)) {
  foreach ($files as $file_name) {
    print '************************************************************************************************';
    print '<h1 style="color: green">' . $file_name . '</h1>';
    $path_to_file = $directory . '/' . $file_name;
    $field_body_name = 'body_value';
    $field_body_name_2 = 'field_cooking_text';

    $title_field = 'title_field';
    $field_image_alt_name = 'field_image_alt';

    $new_path = '/sites/default/files/migration_images/';

    $space_fields = ['field_article_subtitle', 'field_brand_subtitle_value'];

    $image_field = 'field_image';
    $product_images_field = 'field_images';

    $field_title_key = NULL;
    $field_body_key = NULL;
    $field_image_alt_key = NULL;
    $space_field_keys = [];

    $original_language_suffix = 'en';
    //$second_language_suffix = 'en'; //TODO

    $product_images_field_key = NULL;

    //Read csv in array
    $csv = [];
    if (($handle = fopen($path_to_file, 'r')) !== FALSE) {
      while (($data = fgetcsv($handle, 0, ';', '~')) !== FALSE) {
        $csv[] = $data;
      }

      //Try to find key for column with $field_body_name
      if (!empty($csv[0])) {
        $field_title_key = array_search($title_field, $csv[0]);
        $field_body_key = array_search($field_body_name, $csv[0]);
        $field_body_key_2 = array_search($field_body_name_2, $csv[0]);
        $field_image_alt_key = array_search($field_image_alt_name, $csv[0]);
        $product_images_field_key =  array_search($product_images_field, $csv[0]);
        $image_field_key =  array_search($image_field, $csv[0]);

        if (!empty($space_fields)) {
          foreach ($space_fields as $space_field) {
            if (array_search($space_field, $csv[0])) {
              $space_field_keys[$space_field] = array_search($space_field, $csv[0]);
            }
          }
        }

        print '<h2 style="color: green">Change headers</h2>';
        print '<table border="1"><tr><th>Old header</th><th>New header</th></tr>';
        //change suffix for headers
        foreach ($csv[0] as $key => $header) {
          $pos = strpos($header, '_'.$original_language_suffix);
          if ($pos !== false) {
            $new_header = str_replace('_'.$original_language_suffix, '', $header);

            $csv[0][$key] = $new_header;
            print '<tr><td>' . $header . '</td><td>' . $new_header . '</td></tr>';
          }
        }
        print '</table>';

      }
      else {
        print '<h2 style="color: red">Empty headers</h2>';
      }

      //Get all values from column $field_body_name
      if (!empty($field_body_key)) {
        $re = '!(https?:)?//\S+\.(?:jpe?g|jpg|png|gif)!Ui';
        print '<h2 style="color: green">Count rows ' . count($csv) . '</h2>';
        print '<table border="1"><tr><th>Old path</th><th>New Path</th></tr>';
        foreach ($csv as $key => &$c) {
          if ($key == 0) {//don't get header
            continue;
          }
          if (!empty($c[$field_body_key])) {
            $value = $c[$field_body_key];
            preg_match_all($re, $value, $matches);
            if (!empty($matches[0])) {
              foreach ($matches[0] as $match) {
                $arr_srt = explode('/', $match);
                $new_str = '/sites/default/files/migration_images/' . $arr_srt[count($arr_srt) - 1];
                $value = str_replace($match, $new_str, $value);
                $c[$field_body_key] = $value;
                print '<tr><td>' . $match . '</td><td>' . $new_str . '</td></tr>';
              }
            }
          }
        }
        print '</table>';
      }
      else {
        print '<h2 style="color: red">Can\'t find key for your field:' . $field_body_name . '</h2>';
      }

      //Get all values from column $field_body_name_2
      if (!empty($field_body_key_2)) {
        $re = '!(https?:)?//\S+\.(?:jpe?g|jpg|png|gif)!Ui';
        print '<h2 style="color: green">Count rows ' . count($csv) . '</h2>';
        print '<table border="1"><tr><th>Old path</th><th>New Path</th></tr>';
        foreach ($csv as $key => &$c) {
          if ($key == 0) {//don't get header
            continue;
          }
          if (!empty($c[$field_body_key_2])) {
            $value = $c[$field_body_key_2];
            preg_match_all($re, $value, $matches);
            if (!empty($matches[0])) {
              foreach ($matches[0] as $match) {
                $arr_srt = explode('/', $match);
                $new_str = '/sites/default/files/migration_images/' . $arr_srt[count($arr_srt) - 1];
                $value = str_replace($match, $new_str, $value);
                $c[$field_body_key_2] = $value;
                print '<tr><td>' . $match . '</td><td>' . $new_str . '</td></tr>';
              }
            }
          }
        }
        print '</table>';
      }
      else {
        print '<h2 style="color: red">Can\'t find key for your field 2:' . $field_body_name_2 . '</h2>';
      }

      //Fill alt for image from title
      if (!empty($field_title_key) && !empty($field_image_alt_key)) {
        print '<h2 style="color: green">Fill alt for image from title</h2>';
        print '<table border="1"><tr><th>Value</th></tr>';
        foreach ($csv as $key => &$c) {
          if ($key == 0) {//don't change header
            continue;
          }
          if (empty($c[$field_image_alt_key]) && !empty($c[$field_body_key])) {
            $c[$field_image_alt_key] = $c[$field_title_key];
            print '<tr><td>' . $c[$field_title_key] . '</td></tr>';
          }
        }
        print '</table>';
      }

      if (!empty($space_field_keys)) {
        foreach ($space_field_keys as $space_field => $space_field_key) {
          print '<h2 style="color: green">Add spaces for column ' . $space_field . '</h2>';
          foreach ($csv as $key => &$c) {
            if ($key == 0) {//don't change header
              continue;
            }
            if (empty($c[$space_field_key])) {
              $c[$space_field_key] = ' ';
            }
          }
        }
      }

      if (!empty($product_images_field_key) && !empty($image_field_key)) {
        $rows = [];
        foreach ($csv as $key => &$c) {
          if ($key == 0) {//don't change header
            continue;
          }
          if (!empty($c[$product_images_field_key])) {
             $images = $c[$product_images_field_key];
             $rows = array_merge($rows, explode('|',$images));
          }
        }


        if (!empty($rows)) {
          $new_csv_rows = [];
          foreach ($rows as $key => $row) {
            $empty_key = 0;
            while ($empty_key < $image_field_key) {
              $new_csv_rows[$key][$empty_key] = '';
              $empty_key++;
            }
            $new_csv_rows[$key][$image_field_key] = $row;
            $pieces = explode('/',$row);
            if (!empty($pieces)) {
              $new_csv_rows[$key][$field_image_alt_key] = array_pop($pieces);
            }
          }

          $csv = array_merge($csv, $new_csv_rows);
        }
      }

      fclose($handle);

      if (!empty($csv)) {
        $handle_new = fopen(str_replace('.csv', '_NEW.csv', $path_to_file), 'w');
        foreach ($csv as $c) {
          fputcsv($handle_new, $c, ';');
        }
        fclose($handle_new);
      }
    }
  }
}