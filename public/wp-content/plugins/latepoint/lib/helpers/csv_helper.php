<?php 
class OsCSVHelper {
  public static function array_to_csv($data) {
    $output = fopen("php://output", "wb");
    foreach ($data as $row)
      fputcsv($output, $row); // here you can change delimiter/enclosure
    fclose($output);
  }
}