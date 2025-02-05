<?php
if (!function_exists('getRussianDate')) {
  // Function to get formatted date
  function getRussianDate($timestamp)
  {
    // Check if $timestamp is numeric or convert it to a timestamp
    if (!is_numeric($timestamp)) {
      $timestamp = strtotime($timestamp);
    }

    // Verify if the conversion was successful
    if ($timestamp === false) {
      return ''; // Unable to parse timestamp, return an empty string or handle it as needed
    }

    // Define an array to map English month names to Russian
    $monthTranslations = [
      'January' => 'января',
      'February' => 'февраля',
      'March' => 'марта',
      'April' => 'апреля',
      'May' => 'мая',
      'June' => 'июня',
      'July' => 'июля',
      'August' => 'августа',
      'September' => 'сентября',
      'October' => 'октября',
      'November' => 'ноября',
      'December' => 'декабря',
    ];

    // Format the date using the array for month translation and 24-hour time format
    $formattedDate = date('j F, Y', $timestamp); // 'j' removes leading zeros from the day
    $formattedDate = str_replace(array_keys($monthTranslations), $monthTranslations, $formattedDate);

    return $formattedDate;
  }
}
?>