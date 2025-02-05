<?php
function renderInputField($type, $name, $placeholder, $required = true)
{
  $inputField = '<div class="mb-3">';
  $inputField .= '<input type="' . $type . '" class="form-control" name="' . $name . '" placeholder="' . $placeholder . '"';

  if ($required) {
    $inputField .= ' required';
  }

  $inputField .= '>';
  $inputField .= '</div>';

  return $inputField;
}
?>