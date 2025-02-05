<?php
function displayIfNotEmpty($label, $value, $iconClass = '', $iconColor = '')
{
    if (is_array($value) && isset($value['name']) && !empty($value['name']) && $value['name'] !== '0000') {
        echo '<p><strong>' . $label . ':</strong> ' . $value['name'] . '</p>';
    } elseif (!empty($value) && $value !== '0000') {
        if ($iconClass) {
            echo '<span><i class="' . $iconClass . '" style="color: ' . $iconColor . ';"></i> <strong>' . $label . ':</strong></span> <span>' . $value . '</span>';
        } else {
            echo '<p><strong>' . $label . ':</strong> ' . $value . '</p>';
        }
    }
}

function displayIfNotEmptyDate($value)
{
    if (!empty($value) && $value !== '0000-00-00 00:00:00') {
        $formattedValue = formatDate($value);
        echo '<strong>Дата последнего обновления:</strong> ' . $formattedValue;
    } else {
        echo '<strong>Данные требуют уточнения</strong>';
    }
}

function getAddress($region, $area, $town, $street)
{
    $address = $region['region_name'];

    if ($area['name'] !== $region['region_name']) {
        $address .= ', ' . $area['name'];
    }

    if ($town['name'] !== $area['name']) {
        $address .= ', ' . $town['name'];
    }

    $address .= ', ' . $street;

    return $address;
}

function formatDate($dateString)
{
    $timestamp = strtotime($dateString);
    return date('d.m.Y', $timestamp);
}
?>
