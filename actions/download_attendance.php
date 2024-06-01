<?php
require('../libs/fpdf/fpdf.php');
require('vars.php');
require('conn.php');

class PDF extends FPDF
{
    function Header()
    {
        global $totalWorkingDays;
        global $startAttendenceDate;

        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'Attendance Report', 0, 1, 'C');
        $this->Ln(5);

        $this->SetFont('Arial', 'I', 10);
        $this->Cell(0, 10, 'Date: '.$startAttendenceDate.' -> ' . date('Y-m-d'), 0, 1, 'C');
        $this->Ln(5);

        $this->Cell(0, 10, 'Total Working Days: ' . $totalWorkingDays, 0, 1, 'C');
        $this->Ln(5);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

function getTotalWorkingDays($startAttendenceDate) {
    $startDate = new DateTime($startAttendenceDate);
    $endDate = new DateTime();
    $interval = new DateInterval('P1D');
    $period = new DatePeriod($startDate, $interval, $endDate);
    $workingDays = 0;
    foreach ($period as $date) {
        if ($date->format('N') != 7) {
            $workingDays++;
        }
    }
    return $workingDays;
}

$totalWorkingDays = getTotalWorkingDays($startAttendenceDate);

if(isset($_GET['download'])) {
    if($_GET['download'] == 'pdf') {
        $pdf = new PDF('L');
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 12);

        $query = "SELECT users.id, users.name, users.role, users.branch, users.semester, users.session, COUNT(attendance.id) AS total_present,
                  (COUNT(attendance.id) / $totalWorkingDays) * 100 AS percentage
                  FROM users
                  LEFT JOIN attendance ON users.id = attendance.s_id
                  WHERE users.role != 'faculty'
                  GROUP BY users.id";
        $result = $conn->query($query);

        $widths = [
            'name' => 30,
            'role' => 30,
            'branch' => 30,
            'semester' => 30,
            'session' => 30,
            'total_present' => 30,
            'percentage' => 30
        ];
        while ($row = $result->fetch_assoc()) {
            $widths['name'] = max($widths['name'], $pdf->GetStringWidth($row['name']) + 10);
            $widths['role'] = max($widths['role'], $pdf->GetStringWidth($row['role']) + 10);
            $widths['branch'] = max($widths['branch'], $pdf->GetStringWidth($row['branch']) + 10);
            $widths['semester'] = max($widths['semester'], $pdf->GetStringWidth($row['semester']) + 10);
            $widths['session'] = max($widths['session'], $pdf->GetStringWidth($row['session']) + 10);
            $widths['total_present'] = max($widths['total_present'], $pdf->GetStringWidth($row['total_present']) + 10);
            $widths['percentage'] = max($widths['percentage'], $pdf->GetStringWidth(round($row['percentage'], 2) . '%') + 10);
        }
        $result->data_seek(0);

        $pdf->Cell($widths['name'], 10, 'Name', 1, 0, 'C');
        $pdf->Cell($widths['role'], 10, 'Role', 1, 0, 'C');
        $pdf->Cell($widths['branch'], 10, 'Branch', 1, 0, 'C');
        $pdf->Cell($widths['semester'], 10, 'Semester', 1, 0, 'C');
        $pdf->Cell($widths['session'], 10, 'Session', 1, 0, 'C');
        $pdf->Cell($widths['total_present'], 10, 'Total Present', 1, 0, 'C');
        $pdf->Cell($widths['percentage'], 10, 'Percentage', 1, 1, 'C');

        while ($row = $result->fetch_assoc()) {
            $pdf->Cell($widths['name'], 10, $row['name'], 1, 0, 'L', false, '', array(5, 5, 5, 5));
            $pdf->Cell($widths['role'], 10, $row['role'], 1, 0, 'C', false, '', array(5, 5, 5, 5));
            $pdf->Cell($widths['branch'], 10, $row['branch'], 1, 0, 'C', false, '', array(5, 5, 5, 5));
            $pdf->Cell($widths['semester'], 10, $row['semester'], 1, 0, 'C', false, '', array(5, 5, 5, 5));
            $pdf->Cell($widths['session'], 10, $row['session'], 1, 0, 'C', false, '', array(5, 5, 5, 5));
            $pdf->Cell($widths['total_present'], 10, $row['total_present'], 1, 0, 'C', false, '', array(5, 5, 5, 5));
            $pdf->Cell($widths['percentage'], 10, round($row['percentage'], 2) . '%', 1, 1, 'C', false, '', array(5, 5, 5, 5));
        }

        $pdf->Output('Attendance_Report_Till_' . date('Y-m-d_H:i:s') . '.pdf', 'D');
    } elseif($_GET['download'] == 'csv' || $_GET['download'] == 'dat') {
        if($_GET['download'] == 'csv') {
            header('Content-Type: text/csv');
            $extension = 'csv';
        } elseif($_GET['download'] == 'dat') {
            header('Content-Type: text/dat');
            $extension = 'dat';
        }
        $filename = 'Attendance_Report_Till_' . date('Y-m-d_H:i:s') . '.' . $extension;
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        $headers = array('Name', 'Role', 'Branch', 'Semester', 'Session', 'Total Present', 'Percentage');
        $startDate = new DateTime($startAttendenceDate);
        $endDate = new DateTime();
        $interval = new DateInterval('P1D');
        $period = new DatePeriod($startDate, $interval, $endDate);
        foreach ($period as $date) {
            if ($date->format('N') != 7) {
                    $headers[] = $date->format('Y-m-d');
            }
        }
        fputcsv($output, $headers);
        
        $query = "SELECT users.id,users.name, users.role, users.branch, users.semester, users.session, COUNT(attendance.id) AS total_present,
            (COUNT(attendance.id) / $totalWorkingDays) * 100 AS percentage
            FROM users
            LEFT JOIN attendance ON users.id = attendance.s_id
            WHERE users.role != 'faculty'
            GROUP BY users.id";
        $result = $conn->query($query);

        while ($row = $result->fetch_assoc()) {
            
            $rowData = array($row['name'], $row['role'], $row['branch'], $row['semester'], $row['session'], $row['total_present'], $row['percentage']);

            $dateAttendance = array();
            $startDate = new DateTime($startAttendenceDate);
            $endDate = new DateTime();
            $interval = new DateInterval('P1D');
            $period = new DatePeriod($startDate, $interval, $endDate);
            foreach ($period as $date) {
                if ($date->format('N') != 7) {
                    $formattedDate = $date->format('Y-m-d');
                    $sql_attendance = "SELECT * FROM attendance WHERE s_id = " . $row['id'] . " AND DATE(date) = '$formattedDate'";
                    $result_attendance = $conn->query($sql_attendance);
                    if ($result_attendance->num_rows > 0) {
                        $dateAttendance[$formattedDate] = 1;
                    } else {
                        $dateAttendance[$formattedDate] = 0;
                    }
                }
            }

            foreach ($period as $date) {
                if ($date->format('N') != 7) {
                    $formattedDate = $date->format('Y-m-d');
                    $rowData[] = $dateAttendance[$formattedDate];
                }
            }

            // Write the row to the .CSV or .DAT file
            if($extension == 'csv') {
                fputcsv($output, $rowData);
            } elseif($extension == 'dat') {
                fwrite($output, implode(' ', $rowData) . "\n");
            }
        }

        fclose($output);
    }
}
?>