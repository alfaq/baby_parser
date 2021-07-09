<?php
// Change format date from 29.09.15 07:54 to 2015-09-29 07:54:00
$path_to_file = 'date.csv';
$csv = [];
if (($handle = fopen($path_to_file, 'r')) !== FALSE) {
  while (($data = fgetcsv($handle, 0, ';', '~')) !== FALSE) {
    $parsed = date_parse_from_format('d.m.y H:i', $data[0]);//29.09.15 07:54
    $timestamp = mktime(
      $parsed['hour'],
      $parsed['minute'],
      $parsed['second'],
      $parsed['month'],
      $parsed['day'],
      $parsed['year']
    );
    $csv[] = date('Y-m-d H:i:s', $timestamp);
  }
  fclose($handle);
}

if (!empty($csv)) {
  $handle_new = fopen(str_replace('.csv', '_NEW.csv', $path_to_file), 'w');
  foreach ($csv as $new_c) {
    fputcsv($handle_new, [$new_c], ';');
  }
  fclose($handle_new);
}