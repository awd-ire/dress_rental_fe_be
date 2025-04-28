<?php
session_start();
include "C:/xampp/htdocs/Dress_rental1/config.php";

if (!isset($_GET['dress_id'])) {
    echo json_encode(['error' => 'Missing dress ID']);
    exit;
}

$dress_id = intval($_GET['dress_id']);

$kept_dates = [];
$cleaning_dates = [];
$transit_dates = [];

$query = $conn->prepare("
    SELECT start_date, end_date, ri.dress_status,d.availability 
    FROM rentals r 
    JOIN rental_items ri ON r.id = ri.rent_id
    join dresses d on ri.dress_id=d.id 
    WHERE ri.dress_id = ? 
    AND (r.delivery_status IN ('delivered', 'ready') 
    or ri.dress_status IN ('kept'))
");
$query->bind_param("i", $dress_id);
$query->execute();
$result = $query->get_result();

while ($row = $result->fetch_assoc()) {
    $start = new DateTime($row['start_date']);
    $end = new DateTime($row['end_date']);

    while ($start <= $end) {
        $dateStr = $start->format('Y-m-d');

        switch ($row['dress_status']) {
            case 'kept':
                $kept_dates[] = $dateStr;
                break;
            case 'available_soon':
                $cleaning_dates[] = $dateStr;
                break;
            case 'may_be_available':
                $transit_dates[] = $dateStr;
                break;
        }

        $start->modify('+1 day');
    }
}

echo json_encode([
    'redDates' => array_values(array_unique($kept_dates)),
    'orangeDates' => array_values(array_unique($cleaning_dates)),
    'blueDates' => array_values(array_unique($transit_dates)),
    'kept_dates' => array_values(array_unique($kept_dates)),
    'cleaning_dates' => array_values(array_unique($cleaning_dates)),
    'transit_dates' => array_values(array_unique($transit_dates))
]);

error_reporting(E_ALL);
ini_set('display_errors', 1);

// TEMPORARY DEBUG LOGGING:
file_put_contents("debug_log.txt", "Request received\n", FILE_APPEND)
    ?>