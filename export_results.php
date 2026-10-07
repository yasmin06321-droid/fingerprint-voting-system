<?php
include('db.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION['official_id'])) {
    header("HTTP/1.1 403 Forbidden");
    exit("Access denied");
}

$type = isset($_GET['type']) ? $_GET['type'] : 'csv';

// Get results data
$results = [];
$sql = "SELECT p.party_name, COUNT(v.vote_id) as vote_count 
        FROM votes v
        JOIN parties p ON v.party_id = p.party_id
        GROUP BY p.party_id
        ORDER BY vote_count DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $results[] = $row;
    }
}

// Get total votes for percentage calculation
$total_votes = 0;
$sql = "SELECT COUNT(*) as total FROM votes";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_votes = $row['total'];
}

if ($type === 'csv') {
    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="election_results_' . date('Y-m-d') . '.csv"');
    
    // Open output stream
    $output = fopen('php://output', 'w');
    
    // Write CSV header
    fputcsv($output, ['Rank', 'Party Name', 'Votes', 'Percentage']);
    
    // Write data rows
    foreach ($results as $index => $row) {
        $percentage = $total_votes > 0 ? ($row['vote_count'] / $total_votes) * 100 : 0;
        fputcsv($output, [
            $index + 1,
            $row['party_name'],
            $row['vote_count'],
            round($percentage, 2) . '%'
        ]);
    }
    
    fclose($output);
} elseif ($type === 'pdf') {
    // For PDF, we'd typically use a library like TCPDF or Dompdf
    // This is a simplified example - you'd need to install the library
    
    // Example with TCPDF (would need to be installed via composer)
    /*
    require_once('tcpdf/tcpdf.php');
    
    $pdf = new TCPDF();
    $pdf->AddPage();
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'Election Results', 0, 1, 'C');
    
    $html = '<table border="1">
                <tr>
                    <th>Rank</th>
                    <th>Party</th>
                    <th>Votes</th>
                    <th>Percentage</th>
                </tr>';
    
    foreach ($results as $index => $row) {
        $percentage = $total_votes > 0 ? ($row['vote_count'] / $total_votes) * 100 : 0;
        $html .= '<tr>
                    <td>'.($index+1).'</td>
                    <td>'.$row['party_name'].'</td>
                    <td>'.$row['vote_count'].'</td>
                    <td>'.round($percentage, 2).'%</td>
                  </tr>';
    }
    
    $html .= '</table>';
    
    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('election_results.pdf', 'D');
    */
    
    // Since we can't assume TCPDF is installed, we'll just offer to download as CSV
    header("Location: export_results.php?type=csv");
}

// Log export action
$audit_stmt = $conn->prepare("INSERT INTO audit_log (official_id, action, timestamp) VALUES (?, ?, NOW())");
$action = "export_results_" . $type;
$audit_stmt->bind_param("is", $_SESSION['official_id'], $action);
$audit_stmt->execute();
$audit_stmt->close();
?>