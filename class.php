<?php
session_start();
if (!isset($_SESSION['userid'])) {
    header('Location: login.php');
    exit();
} else {
    $sUserId = $_SESSION['userid'];
}
require('actions/conn.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Manage Classes</title>
  <link href="components/libs/font-awesome-pro/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <link href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css" rel="stylesheet">
  <style>
    *{
      font-family: 'Roboto';
    }
      .table-responsive {
            overflow-x: auto;
            font-size: 0.9rem;
        }
        .dataTables_wrapper .dataTables_scroll {
           width: 100% !important;
        }
        .dataTables_length{
          margin-top: 10px;
          margin-bottom: 5px;
          text-align: center;
        }
        .dataTables_filter{
          margin: 10px auto;
        }
         #tables-container { font-size: 0.9rem; }
        .tableAtt td, .tableAtt th { white-space: nowrap; }
        .nav-pills-cust li a{
          padding: 5px 10px;
        }
</style>
</head>
<body>
   <?php include 'nav.php'; ?>
   <?php 
    $roleQ = "SELECT role from users where id = '$sUserId' limit 1";
    $roleE = mysqli_fetch_assoc(mysqli_query($conn, $roleQ));
    $role = $roleE['role'];
    if($role == 'faculty'){
  ?>
<div class="container">
    <?php 
        if(isset($_GET['id'])) {
            $classId = $_GET['id'];
            $classQuery = "SELECT session, branch FROM classes WHERE id = '$classId'";
            $classResult = mysqli_query($conn, $classQuery);
            if(mysqli_num_rows($classResult) > 0) {
                $classData = mysqli_fetch_assoc($classResult);
                $session = $classData['session'];
                $branch = $classData['branch'];
    ?>
    <h1 class="text-center fw-bold my-2"><a href="classes.php" class="nav-link"><?php echo $session . " - " . $branch; ?></a></h1>
    <ul class="nav nav-pills nav-pills-cust justify-content-center mb-4">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="pill" href="#students">Students</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="pill" href="#routines">Routines</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="pill" href="#subjects">Subjects</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="pill" href="#attendance">Attendance</a>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="students">
          <div class="table-responsive mb-2 auto">
        <table id="studentsTable" class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Session</th>
                    <th>Branch</th>
                    <th>Semester</th>
                    <th>Roll</th>
                    <th>Email</th>
                    <th>Joined On</th>
                </tr>
            </thead>
            <tbody>
                <!-- Table body content will be dynamically populated -->
            </tbody>
        </table>
    </div>
        </div>
        
        <div class="tab-pane fade" id="routines">
    <div class="text-center mt-4">
        <button type="button" id="addToRoutineBtn" class="btn btn-outline-primary"><i class="fas fa-plus"></i> Add to Routine</button>
    </div>
    <div class="mt-4 routines-table-div">
        
    </div>
</div>

        <div class="tab-pane fade" id="subjects">
            <div class="text-center mt-4">
                <button type="button" id="addSubjectBtn" class="btn btn-outline-primary"><i class="fas fa-plus"></i> Add Subject</button>
            </div>
            <ul class="list-group mt-4 mx-2 subjects-list">
                <!-- Subjects list will be loaded dynamically -->
            </ul>
        </div>
        
        <div class="tab-pane fade overflow-scroll" id="attendance">
        <h2 class="text-center fw-bold my-2">Attendance</h2>
        <div id="tables-container" class="overflow-scroll position-relative"></div>
        </div>
       
        </div>
    <?php 
        } else {
    ?>
    <div class="alert alert-warning mt-4" role="alert">
        Invalid class ID.
    </div>
    <?php 
        }
    } else {
    ?>
    <div class="alert alert-warning mt-4" role="alert">
        Empty class ID.
    </div>
    <?php } ?>
</div>

 <?php
    } else {
  ?>
  <div class="alert alert-danger m-4">
    This page is only for faculties.
  </div>
  <?php
    }
  ?>

<script src="/eruda.js"></script>
<script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 1500,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    function loadSubjects() {
        $.ajax({
            url: 'actions/getSubjects.php?id=<?php echo $_GET['id']; ?>',
            type: 'GET',
            success: function(response) {
                $('.subjects-list').html(response);
            },
            error: function(err){
              console.log("Error: ", err)
            }
        });
    }

    $('#addSubjectBtn').click(function() {
    Swal.fire({
        title: 'Add Subject',
        html:
            `<input id="code" class="form-control mb-3" type="number" placeholder="Code" required>` +
            `<input id="name" class="form-control mb-3" type="text" placeholder="Name" required>` +
            `<select id="class_id" class="form-select mb-3" required>` +
            `<option selected value="" disabled>Select Class</option>` +
            `<?php
                $classQuery = "SELECT id, CONCAT(session, ' - ', branch) AS class_info FROM classes";
                $classResult = mysqli_query($conn, $classQuery);
                while ($classRow = mysqli_fetch_assoc($classResult)) {
                    echo "<option value='".$classRow['id']."'>".$classRow['class_info']."</option>";
                }
            ?>` +
            `</select>` +
            `<select id="teacher_id" class="form-select mb-3" required>` +
            `<option selected value="" disabled>Select Teacher</option>` +
            `<?php
                $teacherQuery = "SELECT id, name FROM users WHERE role != 'student'";
                $teacherResult = mysqli_query($conn, $teacherQuery);
                while ($teacherRow = mysqli_fetch_assoc($teacherResult)) {
                    echo "<option value='".$teacherRow['id']."'>".$teacherRow['name']."</option>";
                }
            ?>` +
            `</select>` +
            `<select id="hpw" class="form-select mb-3" required>` +
            `<option selected value="" disabled>Select Hours/week</option>` +
            `<option value="1">1 hour per week</option>` +
            `<option value="2">2 hours per week</option>` +
            `<option value="3">3 hours per week</option>` +
            `<option value="4">4 hours per week</option>` +
            `<option value="5">5 hours per week</option>` +
            `<option value="6">6 hours per week</option>` +
            `</select>` +
            `<select id="type" class="form-select mb-3" required>` +
            `<option selected value="" disabled>Select Type</option>` +
            `<option value="Theory">Theory</option>` +
            `<option value="Lab">Lab</option>` +
            `</select>`,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Add',
        preConfirm: () => {
            const code = Swal.getPopup().querySelector('#code').value;
            const name = Swal.getPopup().querySelector('#name').value;
            const class_id = Swal.getPopup().querySelector('#class_id').value;
            const teacher_id = Swal.getPopup().querySelector('#teacher_id').value;
            const hpw = Swal.getPopup().querySelector('#hpw').value;
            const type = Swal.getPopup().querySelector('#type').value;
            if (!code || !name || !class_id || !teacher_id || !hpw || !type) {
                Swal.showValidationMessage('All fields are required');
                return false;
            }
            return { code: code, name: name, class_id: class_id, teacher_id: teacher_id, hpw: hpw, type: type };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'actions/addSubject.php',
                type: 'POST',
                dataType: 'json',
                data: result.value,
                success: function(response) {
                    Toast.fire({ title: response.message, icon: response.icon });
                    loadSubjects();
                }
            });
        }
    });
});

    $(document).on('click', '.edit-subject', function() {
    const subjectId = $(this).data('id');

    $.ajax({
        url: 'actions/getSubjectDetails.php',
        type: 'POST',
        dataType: 'json',
        data: { id: subjectId },
        success: function(response) {
            if(response.icon != 'error'){
              const subjectDetails = response.subject;
            Swal.fire({
                title: 'Edit Subject',
                html:
                    `<input id="editCode" class="form-control mb-3" type="number" placeholder="Code" value="${subjectDetails.code}" required>` +
                    `<input id="editName" class="form-control mb-3" type="text" placeholder="Name" value="${subjectDetails.name}" required>` +
                    `<select id="editClassId" class="form-select mb-3" required>` +
                    `<option selected value="" disabled>Select Class</option>` +
                    `<?php
                        $classQuery = "SELECT id, CONCAT(session, ' - ', branch) AS class_info FROM classes";
                        $classResult = mysqli_query($conn, $classQuery);
                        while ($classRow = mysqli_fetch_assoc($classResult)) {
                            echo "<option value='".$classRow['id']."'>".$classRow['class_info']."</option>";
                        }
                    ?>` +
                    `</select>` +
                    `<select id="editTeacherId" class="form-select mb-3" required>` +
                    `<option selected value="" disabled>Select Teacher</option>` +
                    `<?php
                        $teacherQuery = "SELECT id, name FROM users WHERE role != 'student'";
                        $teacherResult = mysqli_query($conn, $teacherQuery);
                        while ($teacherRow = mysqli_fetch_assoc($teacherResult)) {
                            echo "<option value='".$teacherRow['id']."'>".$teacherRow['name']."</option>";
                        }
                    ?>` +
                    `</select>` +
                    `<select id="editHpw" class="form-select mb-3" required>` +
                    `<option selected value="" disabled>Select Hours/week</option>` +
                    `<option value="1" ${(subjectDetails.hpw === '1') ? 'selected' : ''}>1 hour per week</option>` +
                    `<option value="2" ${(subjectDetails.hpw === '2') ? 'selected' : ''}>2 hours per week</option>` +
                    `<option value="3" ${(subjectDetails.hpw === '3') ? 'selected' : ''}>3 hours per week</option>` +
                    `<option value="4" ${(subjectDetails.hpw === '4') ? 'selected' : ''}>4 hours per week</option>` +
                    `<option value="5" ${(subjectDetails.hpw === '5') ? 'selected' : ''}>5 hours per week</option>` +
                    `<option value="6" ${(subjectDetails.hpw === '6') ? 'selected' : ''}>6 hours per week</option>` +
                    `</select>` +
                    `<select id="editType" class="form-select mb-3" required>` +
                    `<option selected value="" disabled>Select Type</option>` +
                    `<option value="Theory" ${(subjectDetails.type === 'Theory') ? 'selected' : ''}>Theory</option>` +
                    `<option value="Lab" ${(subjectDetails.type === 'Lab') ? 'selected' : ''}>Lab</option>` +
                    `</select>`,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Save',
                preConfirm: () => {
                    const code = Swal.getPopup().querySelector('#editCode').value;
                    const name = Swal.getPopup().querySelector('#editName').value;
                    const class_id = Swal.getPopup().querySelector('#editClassId').value;
                    const teacher_id = Swal.getPopup().querySelector('#editTeacherId').value;
                    const hpw = Swal.getPopup().querySelector('#editHpw').value;
                    const type = Swal.getPopup().querySelector('#editType').value;
                    if (!code || !name || !class_id || !teacher_id || !hpw || !type) {
                        Swal.showValidationMessage('All fields are required');
                        return false;
                    }
                    return { id: subjectDetails.id, code: code, name: name, class_id: class_id, teacher_id: teacher_id, hpw: hpw, type: type };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'actions/editSubject.php',
                        type: 'POST',
                        dataType: 'json',
                        data: result.value,
                        success: function(response) {
                            Toast.fire({ title: response.message, icon: response.icon });
                            loadSubjects();
                        }
                    });
                }
            });

            $('#editClassId').val(subjectDetails.c_id);
            $('#editTeacherId').val(subjectDetails.f_id);
        }else{
          Toast.fire({
            title:response.message,
            icon:response.icon
          })
        }
        }
    });
});

    $(document).on('click', '.delete-subject', function() {
        const subjectId = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'actions/deleteSubject.php',
                    type: 'POST',
                    dataType: 'json',
                    data: { id: subjectId },
                    success: function(response) {
                        Toast.fire({ title: response.message, icon: response.icon });
                        loadSubjects();
                    }
                });
            }
        });
    });
    
    $(document).on('click', '.view-subject', function() {
      const subjectId = $(this).data('id');
      $.ajax({
        url: 'actions/getSubjectDetails.php',
        data: {
          id: subjectId
        },
        dataType: 'json',
        type: 'post',
        success: function(data){
          if(data.icon == 'success'){
            const details = data.subject
             const timestamp = details.created;
             const formatDate = (timestamp) => {
             const date = new Date(timestamp);
             const options = {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: false
    };
    return date.toLocaleString('en-IN', options).replace(',', '');
};
    const formattedDate = formatDate(timestamp);

            Swal.fire({
  title: "<strong class='text-center'>Subject details</strong>",
  icon: "info",
  html: `
    <ul class="list-group text-start">
    <li class="list-group-item">Name: ${details.name}</li>
    <li class="list-group-item">Code: ${details.code}</li>
    <li class="list-group-item">Type: ${details.type}</li>
    <li class="list-group-item">Class: ${details.session} - ${details.branch}</li>
    <li class="list-group-item">Teacher: ${details.teacher}</li>
    <li class="list-group-item">Created by: ${details.byname}</li>
    <li class="list-group-item">Created at: ${formattedDate}</li>
    </ul>
  `,
  showCloseButton: true,
  focusConfirm: false,
  confirmButtonText: `
    <i class="fa fa-thumbs-up"></i> Great!
  `,
  confirmButtonAriaLabel: "Thumbs up, great!"
});
          }else{
            Toast.fire({
              title: data.message,
              icon: data.icon
            })
          }
        }
      })
    });

    loadSubjects();
});
</script>
<script>
  $(document).ready(function() {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 1500,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });
    
    function loadRoutines() {
    $.ajax({
        url: 'actions/getRoutine.php?id=<?php echo $_GET['id']; ?>',
        type: 'GET',
        success: function(response) {
            $('.routines-table-div').html(response);
        }
    });
}

   $('#addToRoutineBtn').on('click', function() {
    Swal.fire({
        title: 'Add to Routine',
        html:
        `
        <select id="day" class="form-select my-2" required>
        <option selected value="" disabled>Select Day</option>
        <option value="1">Monday</option>
        <option value="2">Tuesday</option>
        <option value="3">Wednesday</option>
        <option value="4">Thursday</option>
        <option value="5">Friday</option>
        <option value="6">Saturday</option>
        </select>
        `+
            `
            <label for="fromTime">From:</label>
            <input id="fromTime" class="form-control mb-3" type="time" value="09:00" required>` +
            `
            <label for="toTime">To:</label>
            <input id="toTime" class="form-control mb-3" type="time" value="10:00" required>` +
            `<select id="subjectId" class="form-select mb-3" required>` +
            `<option selected value="" disabled>Select Subject</option>` +
            `<?php
              $class_id = $_GET['id'];
              $subjectsQuery = "SELECT id, code, type, name FROM subjects WHERE c_id = '$class_id'";
              $result = mysqli_query($conn, $subjectsQuery);
              if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                   echo "<option value='".$row['id']."'>".$row['name']." - ".$row['code']."</option>";
                  }
                } 
            ?>`
            +
            `</select>`,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Add',
        preConfirm: () => {
            const day = Swal.getPopup().querySelector('#day').value;
            const fromTime = Swal.getPopup().querySelector('#fromTime').value;
            const toTime = Swal.getPopup().querySelector('#toTime').value;
            const subjectId = Swal.getPopup().querySelector('#subjectId').value;
            if (!day || !fromTime || !toTime || !subjectId) {
                Swal.showValidationMessage('All fields are required');
                return false;
            }
            return { day: day, fromTime: fromTime, toTime: toTime, subjectId: subjectId };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const data = result.value;
            const classId = <?php echo $_GET['id']; ?>;
            $.ajax({
                url: 'actions/addRoutine.php',
                type: 'POST',
                dataType: 'json',
                data: { day: data.day, fromTime: data.fromTime, toTime: data.toTime, subjectId: data.subjectId, classId: classId },
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({ title: response.message, icon: 'success' });
                        loadRoutines();
                    } else {
                        Swal.fire({ title: response.message, icon: 'error' });
                    }
                }
            });
        }
    });
   })
   
   $(document).on('click', '.edit-routine', function() {
    const routineId = $(this).data('id');
    $.ajax({
        url: 'actions/getRoutineDetails.php',
        type: 'POST',
        dataType: 'json',
        data: { routineId: routineId },
        success: function(response) {
            if (response.status === 'success') {
                const routineDetails = response.routine;
                Swal.fire({
                    title: 'Edit Routine',
                    html:
                        `<select id="day" class="form-select my-2" required>` +
                        `<option selected value="" disabled>Select Day</option>` +
                        `<option value="1">Monday</option>` +
                        `<option value="2">Tuesday</option>` +
                        `<option value="3">Wednesday</option>` +
                        `<option value="4">Thursday</option>` +
                        `<option value="5">Friday</option>` +
                        `<option value="6">Saturday</option>` +
                        `</select>` +
                        `<label for="fromTime">From:</label>` +
                        `<input id="fromTime" class="form-control mb-3" type="time" required>` +
                        `<label for="toTime">To:</label>` +
                        `<input id="toTime" class="form-control mb-3" type="time" required>` +
                        `<select id="subjectId" class="form-select mb-3" required>` +
                        `<option selected value="" disabled>Select Subject</option>` +
                        `
                        <?php
                         $class_id = $_GET['id'];
              $subjectsQuery = "SELECT id, code, type, name FROM subjects WHERE c_id = '$class_id'";
              $result = mysqli_query($conn, $subjectsQuery);
              if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                   echo "<option value='".$row['id']."'>".$row['name']." - ".$row['code']."</option>";
                  }
                } 
                        ?>
                        `+
                        `</select>`,
                    focusConfirm: false,
                    showCancelButton: true,
                    confirmButtonText: 'Update',
                    preConfirm: () => {
                        const day = Swal.getPopup().querySelector('#day').value;
                        const fromTime = Swal.getPopup().querySelector('#fromTime').value;
                        const toTime = Swal.getPopup().querySelector('#toTime').value;
                        const subjectId = Swal.getPopup().querySelector('#subjectId').value;
                        if (!day || !fromTime || !toTime || !subjectId) {
                            Swal.showValidationMessage('All fields are required');
                            return false;
                        }
                        return { routineId: routineId, day: day, fromTime: fromTime, toTime: toTime, subjectId: subjectId };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const classId = <?php echo $_GET['id']; ?>;
                        const data = result.value;
                        $.ajax({
                            url: 'actions/editRoutine.php',
                            type: 'POST',
                            dataType: 'json',
                            data: { routineId: data.routineId, day: data.day, fromTime: data.fromTime, toTime: data.toTime, subjectId: data.subjectId, classId: classId },
                            success: function(response) {
                                Swal.fire({ title: response.message, icon: response.icon });
                                loadRoutines();
                            }
                        });
                    }
                });

                $('#day').val(routineDetails.day);
                $('#subjectId').val(routineDetails.subject.id);
                $('#fromTime').val(routineDetails.fromTime);
                $('#toTime').val(routineDetails.toTime);
            } else {
                Swal.fire({ title: response.message, icon: 'error' });
            }
        }
    });
});

   $(document).on('click', '.delete-routine', function() {
        const routineId = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'actions/deleteRoutine.php',
                    type: 'POST',
                    dataType: 'json',
                    data: { id: routineId },
                    success: function(response) {
                        Toast.fire({ title: response.message, icon: response.icon });
                        loadRoutines();
                    }
                });
            }
        });
    });
   
    loadRoutines();
});
</script>
<script>
  $(document).ready(function() {
    const classId = <?php echo($_GET['id']) ?>;
    $.ajax({
        url: "actions/fetch_students_by_class.php",
        type: "GET",
        data: {
            classId: classId
        },
        dataType: "json",
        success: function(data) {
            $('#studentsTable').DataTable({
                data: data,
                columns: [
                    { "data": "name" },
                    { "data": "session" },
                    { "data": "branch" },
                    { "data": "semester" },
                    { "data": "roll" },
                    { "data": "email" },
                    { "data": "joinedon" }
                ],
                scrollX: true,
                autoWidth: true // Add this line
            });
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("Error fetching data: ", textStatus, errorThrown);
        }
    });
});
</script>
<script>
        $(document).ready(function() {
            <?php if (isset($_GET['id'])) { ?>
                const classId = <?php echo json_encode($_GET['id']); ?>;
            <?php } ?>

            $.ajax({
                url: 'actions/get_attendances.php',
                type: 'GET',
                dataType: 'json',
                data: { classId: classId },
                success: function(data) {
                    if (!data || data.length === 0) {
                        alert('No data available.');
                        return;
                    }

                    data.forEach(subject => {
                        $('#tables-container').append(createTable(subject));
                    });

                    $('.search-bar').on('keyup', function() {
                        const value = $(this).val().toLowerCase();
                        $(this).next('table').find('tbody tr').filter(function() {
                            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                        });
                    });
                },
                error: function() {
                    alert('Failed to fetch data.');
                }
            });

            function createTable(subject) {
                let table = `<h3 class="position-sticky start-0 mb-1 ms-1">${subject.subjectName}</h3>
                                <p class="my-1 ms-1 position-sticky start-0">Total periods: ${subject.totalPeriods}</p>
                                <div class="d-flex justify-content-between position-sticky start-0">
                                    <input type="text" class="form-control mb-2 ms-1 mt-1 w-50 search-bar position-sticky start-0" placeholder="Search in ${subject.subjectName}">
                                    <button class="btn btn-primary btn-sm download-csv ms-1 mt-1 mb-2 position-sticky start-0 d-none">Download CSV</button>
                                </div>
                            <table class="table table-bordered table-hover table-striped overflow-x-scroll table-primary tableAtt">`;
                table += `<thead><tr><th>Name</th><th>Roll</th><th>Semester</th><th>Present</th><th>Percentage</th>`;

                subject.attendance[0].attendance.forEach(att => { table += `<th class="text-center" colspan="${att.periods.length}">${att.date}</th>`; });

                table += `</tr></thead><tbody>`;

                subject.attendance.forEach(student => {
                    let attendanceDetails = '';
                    student.attendance.forEach(att => {
                        att.periods.forEach(session => {
                            attendanceDetails += `<td>${session.title} (${session.timing})</td>`;
                        });
                    });

                    table += `<tr><td>${student.studentName}</td><td>${student.roll}</td><td>${student.semester}</td><td>${student.totalPresent}</td><td>${student.percentage}%</td>${attendanceDetails}</tr>`;
                });

                table += `</tbody></table> <hr class="border-primary position-sticky start-0">`;
                return table;
            }

        });
    </script>
</body>
</html>