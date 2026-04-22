<?php

function timeAgo($datetime, $con = null) {
    if(empty($datetime)) return '';

    $diff = null;

    if($con) {
        $safe = mysqli_real_escape_string($con, $datetime);
        $res = mysqli_query($con, "SELECT TIMESTAMPDIFF(SECOND, '$safe', NOW()) AS diff");
        if($res && ($r = mysqli_fetch_assoc($res))) {
            $diff = (int) $r['diff'];
        }
    }

    if($diff === null) {
        $timestamp = is_numeric($datetime) ? (int) $datetime : strtotime($datetime);
        if(!$timestamp) return '';
        $diff = time() - $timestamp;
    }

    if($diff < 0) return 'just now';
    if($diff < 5)  return 'just now';

    if($diff < 60) {
        return $diff . ' second' . ($diff === 1 ? '' : 's') . ' ago';
    }

    $minutes = (int) floor($diff / 60);
    if($minutes < 60) {
        return $minutes . ' minute' . ($minutes === 1 ? '' : 's') . ' ago';
    }

    $hours = (int) floor($diff / 3600);
    if($hours < 24) {
        return $hours . ' hour' . ($hours === 1 ? '' : 's') . ' ago';
    }

    $days = (int) floor($diff / 86400);
    if($days < 30) {
        return $days . ' day' . ($days === 1 ? '' : 's') . ' ago';
    }

    $months = (int) floor($days / 30);
    if($months < 12) {
        return $months . ' month' . ($months === 1 ? '' : 's') . ' ago';
    }

    $years = (int) floor($days / 365);
    return $years . ' year' . ($years === 1 ? '' : 's') . ' ago';
}

?>
