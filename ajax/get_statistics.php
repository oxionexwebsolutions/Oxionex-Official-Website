<?php
/**
 * Oxionex Web Solutions - Get Statistics AJAX Handler
 * Fetches current statistics for the website
 */

header('Content-Type: application/json');

require_once '../includes/db.php';

$response = [
    'success' => false,
    'statistics' => [],
    'message' => 'Error fetching statistics'
];

try {
    $query = "SELECT label, value, suffix FROM statistics ORDER BY sort_order ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $statistics = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($statistics) {
        $response['success'] = true;
        $response['statistics'] = $statistics;
        $response['message'] = 'Statistics fetched successfully';
    }
} catch (PDOException $e) {
    error_log('Error fetching statistics: ' . $e->getMessage());
}

echo json_encode($response);
exit;
