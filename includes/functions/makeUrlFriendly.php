<?php
function makeUrlFriendly($text)
{
  // Check if $text is null or not a string
  if (empty($text)) {
    return ''; // Return an empty string if $text is null or empty
  }

  // Transliterate non-ASCII characters
  $transliteratedText = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);

  // Replace spaces with dashes and remove non-alphanumeric characters
  $urlFriendlyText = preg_replace('/[^a-zA-Z0-9]+/', '-', $transliteratedText);

  // Remove leading and trailing dashes
  $urlFriendlyText = trim($urlFriendlyText, '-');

  // Convert to lowercase
  $urlFriendlyText = strtolower($urlFriendlyText);

  return $urlFriendlyText;
}
