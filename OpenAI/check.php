<?php
$apiKey = $_GET['apikey'];
$currentMonth = date('Y-m');
$startDate = $_GET['startDate'] ?? $currentMonth . '-01';
$endDate = $_GET['endDate'] ?? $currentMonth . '-' . date('t', strtotime($currentMonth));

if (!$apiKey) {
  echo "Please provide an API key.";
  exit();
}

function getBillingUsage($startDate, $endDate, $apiKey) {
  $url = "https://api.openai.com/v1/dashboard/billing/usage?start_date=$startDate&end_date=$endDate";
  $headers = array(
    'Authorization: Bearer ' . $apiKey,
    'Content-Type: application/json'
  );

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  $response = curl_exec($ch);
  curl_close($ch);

  return $response;
}

$response = getBillingUsage($startDate, $endDate, $apiKey);

if (strstr($response, 'error')) {
  echo "Invalid API key.";
} else {
  $cost_data = json_decode($response);
  $total_usage = round($cost_data->total_usage / 100, 2);
  echo $total_usage . '$';
}
?>
