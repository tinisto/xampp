<?php
function renderButton(
    $text,
    $url,
    $size = "sm",
    $color = "primary", // Default to Bootstrap's 'primary' color
    $isButton = true
) {
    // Define common button classes
    $class = "btn ";

    // Apply size class
    if ($size === "sm") {
        $class .= "btn-sm ";
    } elseif ($size === "lg") {
        $class .= "btn-lg ";
    }

    // Apply color class (including outline versions)
    if (strpos($color, "outline-") === 0) {
        // For outline colors, prepend `btn-outline-` to color
        $class .= $color . " "; // No need to add `btn-outline-` because it's already there
    } else {
        // For regular colors, just add `btn-<color>`
        if ($color === "primary") {
            $class .= "btn-primary ";
        } elseif ($color === "secondary") {
            $class .= "btn-secondary ";
        } elseif ($color === "success") {
            $class .= "btn-success ";
        } elseif ($color === "danger") {
            $class .= "btn-danger ";
        } elseif ($color === "warning") {
            $class .= "btn-warning ";
        } elseif ($color === "info") {
            $class .= "btn-info ";
        } elseif ($color === "light") {
            $class .= "btn-light ";
        } elseif ($color === "dark") {
            $class .= "btn-dark ";
        }
    }

    // Render either a button or a link
    if ($isButton) {
        echo "<button class=\"$class\" onclick=\"window.location.href='$url';\">$text</button>";
    } else {
        echo "<a href=\"$url\" class=\"$class\">$text</a>";
    }
}
?>
