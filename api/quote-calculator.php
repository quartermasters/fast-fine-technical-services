<?php
/**
 * Fast and Fine Technical Services FZE - Quote Calculator API
 *
 * Real-time price estimation endpoint for services
 *
 * Features:
 * - Service-based pricing calculation
 * - Duration-based hourly rate
 * - Urgency multiplier (Regular, Priority, Emergency)
 * - Property size factor
 * - VAT calculation (5%)
 * - Total estimate with breakdown
 *
 * @package FastAndFine
 * @version 1.0.0
 */

// Prevent direct access
define('FAST_FINE_APP', true);

// Load dependencies
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db-connect.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/functions.php';

// Set JSON headers
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Initialize response
$response = [
    'success' => false,
    'message' => '',
    'data' => null,
    'errors' => []
];

try {
    // Check request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method. POST required.');
    }

    // Verify CSRF token
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        throw new Exception('Invalid security token. Please refresh the page.');
    }

    // Rate limiting check
    $clientIP = getClientIP();
    if (!checkRateLimit($clientIP, 'quote_calculator', 60, 30)) { // 30 requests per minute
        throw new Exception('Too many requests. Please wait a moment.');
    }

    // Get and validate input
    $serviceId = filter_input(INPUT_POST, 'service_id', FILTER_VALIDATE_INT);
    $duration = filter_input(INPUT_POST, 'duration', FILTER_VALIDATE_FLOAT);
    $urgency = filter_input(INPUT_POST, 'urgency', FILTER_SANITIZE_STRING);
    $propertySize = filter_input(INPUT_POST, 'property_size', FILTER_SANITIZE_STRING);

    // Validation
    if (!$serviceId || $serviceId <= 0) {
        $response['errors'][] = 'Invalid service selected.';
    }

    if (!$duration || $duration <= 0 || $duration > 24) {
        $response['errors'][] = 'Duration must be between 1 and 24 hours.';
    }

    if (!in_array($urgency, ['regular', 'priority', 'emergency'])) {
        $response['errors'][] = 'Invalid urgency level.';
    }

    // If validation errors, return early
    if (!empty($response['errors'])) {
        $response['message'] = 'Validation errors occurred.';
        echo json_encode($response);
        exit;
    }

    // Service pricing data (should ideally be from database)
    $services = [
        1 => ['name' => 'Building Cleaning', 'base_price' => 150, 'hourly_rate' => 50, 'category' => 'residential'],
        2 => ['name' => 'Carpentry', 'base_price' => 200, 'hourly_rate' => 80, 'category' => 'residential'],
        3 => ['name' => 'Plumbing', 'base_price' => 180, 'hourly_rate' => 75, 'category' => 'residential'],
        4 => ['name' => 'Air Conditioning', 'base_price' => 250, 'hourly_rate' => 90, 'category' => 'residential'],
        5 => ['name' => 'Electromechanical', 'base_price' => 300, 'hourly_rate' => 100, 'category' => 'commercial'],
        6 => ['name' => 'Painting', 'base_price' => 120, 'hourly_rate' => 60, 'category' => 'residential'],
        7 => ['name' => 'Electrical', 'base_price' => 180, 'hourly_rate' => 70, 'category' => 'residential'],
        8 => ['name' => 'Gypsum & Partition', 'base_price' => 220, 'hourly_rate' => 85, 'category' => 'commercial'],
        9 => ['name' => 'Tiling', 'base_price' => 200, 'hourly_rate' => 75, 'category' => 'residential']
    ];

    // Check if service exists
    if (!isset($services[$serviceId])) {
        throw new Exception('Service not found.');
    }

    $service = $services[$serviceId];

    // Calculate urgency multiplier
    $urgencyMultipliers = [
        'regular' => 1.0,      // No extra charge
        'priority' => 1.25,    // +25%
        'emergency' => 1.5     // +50%
    ];
    $urgencyMultiplier = $urgencyMultipliers[$urgency] ?? 1.0;

    // Calculate property size factor
    $sizeFactor = 1.0;
    if ($propertySize === 'large') {
        $sizeFactor = 1.15; // +15% for large properties
    }

    // Calculate base charges
    $baseCharge = $service['base_price'];
    $hourlyCharge = $service['hourly_rate'] * $duration;

    // Apply size factor to hourly charge
    $hourlyCharge = $hourlyCharge * $sizeFactor;

    // Calculate subtotal before urgency
    $subtotalBeforeUrgency = $baseCharge + $hourlyCharge;

    // Calculate urgency charge
    $urgencyCharge = $subtotalBeforeUrgency * ($urgencyMultiplier - 1);

    // Calculate subtotal
    $subtotal = $subtotalBeforeUrgency + $urgencyCharge;

    // Calculate VAT (5%)
    $vat = $subtotal * 0.05;

    // Calculate total
    $total = $subtotal + $vat;

    // Build response data
    $response['success'] = true;
    $response['message'] = 'Quote calculated successfully.';
    $response['data'] = [
        'service' => [
            'id' => $serviceId,
            'name' => $service['name'],
            'category' => $service['category']
        ],
        'calculation' => [
            'base_charge' => round($baseCharge, 2),
            'hourly_rate' => $service['hourly_rate'],
            'duration' => $duration,
            'hourly_charge' => round($hourlyCharge, 2),
            'size_factor' => $sizeFactor,
            'urgency' => $urgency,
            'urgency_multiplier' => $urgencyMultiplier,
            'urgency_charge' => round($urgencyCharge, 2),
            'subtotal' => round($subtotal, 2),
            'vat_rate' => 0.05,
            'vat_amount' => round($vat, 2),
            'total' => round($total, 2),
            'currency' => 'AED'
        ],
        'breakdown' => [
            [
                'label' => 'Service Charge',
                'amount' => round($baseCharge, 2),
                'description' => 'Base service fee'
            ],
            [
                'label' => 'Hourly Rate',
                'amount' => round($hourlyCharge, 2),
                'description' => sprintf('%s hours × AED %s/hr%s',
                    $duration,
                    $service['hourly_rate'],
                    $sizeFactor > 1.0 ? ' (Large property +15%)' : ''
                )
            ],
            [
                'label' => 'Urgency Charge',
                'amount' => round($urgencyCharge, 2),
                'description' => sprintf('%s (+%d%%)',
                    ucfirst($urgency),
                    ($urgencyMultiplier - 1) * 100
                ),
                'visible' => $urgencyMultiplier > 1.0
            ],
            [
                'label' => 'Subtotal',
                'amount' => round($subtotal, 2),
                'type' => 'subtotal'
            ],
            [
                'label' => 'VAT (5%)',
                'amount' => round($vat, 2),
                'description' => 'Value Added Tax'
            ],
            [
                'label' => 'Total Estimate',
                'amount' => round($total, 2),
                'type' => 'total'
            ]
        ],
        'notes' => [
            'This is a preliminary estimate based on the information provided.',
            'Final price may vary based on actual scope of work and site conditions.',
            'All prices are in UAE Dirhams (AED).',
            'VAT is included in the total amount.'
        ],
        'timestamp' => date('Y-m-d H:i:s')
    ];

    // Log quote request (optional - for analytics)
    if (ANALYTICS_ENABLED) {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("
                INSERT INTO quote_requests
                (service_id, duration, urgency, property_size, estimated_total, client_ip, created_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $serviceId,
                $duration,
                $urgency,
                $propertySize ?? 'small',
                $total,
                $clientIP
            ]);
        } catch (Exception $e) {
            // Log error but don't fail the request
            error_log("Quote logging failed: " . $e->getMessage());
        }
    }

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();

    // Log error
    error_log("Quote Calculator Error: " . $e->getMessage());

    // Set appropriate HTTP status code
    http_response_code(400);
}

// Output JSON response
echo json_encode($response, JSON_PRETTY_PRINT);
exit;
