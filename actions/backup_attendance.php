<?php
date_default_timezone_set('Asia/Kolkata');
require_once('conn.php');
require_once('vars.php');

if (date('N') == $attendanceBackupOnDay) {
    $timestamp = date('Ymd_His');
    $directory = '../backup/attendance/'.date('Y-m-d').'/';
    $filename = "Attendance_till_$timestamp";
    if (!is_dir($directory)) {
        mkdir($directory, 0777, true);
    }

    $filename_csv = $directory . "$filename.csv";
    $filename_dat = $directory . "$filename.dat";

    $filename_csv = fopen($filename_csv, 'w');
    if (!$filename_csv) {
    }

    $filename_dat = fopen($filename_dat, 'w');
    if (!$filename_dat) {
    }

    $headers = array('Name', 'Role', 'Branch', 'Semester', 'Session', 'Total Present', 'Percentage');
    $startDate = new DateTime($startAttendenceDate);
    $endDate = new DateTime();
    $interval = new DateInterval('P1D');
    $period = new DatePeriod($startDate, $interval, $endDate);
    $totalWorkingDays = getTotalWorkingDaysCron($startDate);

    foreach ($period as $date) {
        if ($date->format('N') != 7) {
            $headers[] = $date->format('Y-m-d');
        }
    }

    fputcsv($filename_csv, $headers);

    $query = "SELECT users.id, users.name, users.role, users.branch, users.semester, users.session, COUNT(attendance.id) AS total_present,
        (COUNT(attendance.id) / $totalWorkingDays) * 100 AS percentage
        FROM users
        LEFT JOIN attendance ON users.id = attendance.s_id
        WHERE users.role = 'student'
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

        fputcsv($filename_csv, $rowData);
        fwrite($filename_dat, implode(' ', $rowData) . "\n");
    }

    fclose($filename_csv);
    fclose($filename_dat);

}

function getTotalWorkingDaysCron($startAttendenceDate) {
    $startDate = $startAttendenceDate;
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
?>