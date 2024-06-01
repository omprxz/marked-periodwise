<?php
require('../libs/fpdf/fpdf.php');
require('conn.php');
require_once('vars.php');

if (isset($_GET['resultType_id']) && isset($_GET['sUserId'])) {
    $resultType_id = $_GET['resultType_id'];
    $sUserId = $_GET['sUserId'];

    $user_query = "SELECT name, branch, semester, session, roll FROM users WHERE id = '$sUserId'";
    $user_result = mysqli_query($conn, $user_query);
    $user = mysqli_fetch_assoc($user_result);

    $marks_query = "SELECT * FROM marks WHERE s_id = '$sUserId' AND resultType_id = '$resultType_id'";
    $marks_result = mysqli_query($conn, $marks_query);

    $subject_query = "SELECT id, subject FROM subjects";
    $subject_result = mysqli_query($conn, $subject_query);
    $subjects = array();
    while ($row = mysqli_fetch_assoc($subject_result)) {
        $subjects[$row['id']] = $row['subject'];
    }

    $result_type_query = "SELECT type FROM result_types WHERE id = '$resultType_id'";
    $result_type_result = mysqli_query($conn, $result_type_query);
    $result_type_row = mysqli_fetch_assoc($result_type_result);
    $result_type = $result_type_row['type'];

    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(40, 10, 'Result: ' . $result_type);
    $pdf->Ln();
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(40, 10, 'Name: ' . $user['name']);
    $pdf->Ln();
    $pdf->Cell(40, 10, 'Branch: ' . $user['branch']);
    $pdf->Ln();
    $pdf->Cell(40, 10, 'Semester: ' . ordinal($user['semester']));
    $pdf->Ln();
    $pdf->Cell(40, 10, 'Session: ' . $user['session']);
    $pdf->Ln();
    $pdf->Cell(40, 10, 'Roll No.: ' . $user['roll']);
    $pdf->Ln(20);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(100, 10, 'Subject', 1);
    $pdf->Cell(30, 10, 'Marks', 1);
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 12);
    $total_marks = 0;
    while ($row = mysqli_fetch_assoc($marks_result)) {
        $subject_id = $row['subject_id'];
        if (isset($subjects[$subject_id]) && !empty($subjects[$subject_id])) {
            $subject_name = $subjects[$subject_id];
            $marks = is_null($row['marks']) ? "-" : $row['marks'];
            $total_marks += is_numeric($marks) ? $marks : 0;
            $pdf->Cell(100, 10, $subject_name, 1);
            $pdf->Cell(30, 10, $marks, 1);
            $pdf->Ln();
        }
    }

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(100, 10, 'Total', 1);
    $pdf->Cell(30, 10, $total_marks, 1);

    $filename = $user['name'] . '-' . $result_type . '.pdf';
    $pdf->Output('D', $filename);
}

function ordinal($number) {
    $ends = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');
    if ((($number % 100) >= 11) && (($number % 100) <= 13))
        return $number . 'th';
    else
        return $number . $ends[$number % 10];
}
?>