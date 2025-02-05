<?php
function getIQRating($score, $totalQuestions)
{
    $percentage = ($score / $totalQuestions) * 100;

    if ($percentage >= 90) {
        return "высокий";
    } elseif ($percentage >= 70) {
        return "выше среднего";
    } elseif ($percentage >= 50) {
        return "средний";
    } else {
        return "ниже среднего";
    }
}
?>
