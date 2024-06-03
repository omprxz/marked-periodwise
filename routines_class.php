
    <div class="text-center mt-2">
        <button type="button" id="addToRoutineBtn" class="btn btn-outline-primary"><i class="fad fa-plus"></i> Add to Routine</button>
    </div>
    <div class="mt-4 routines-table-div">
        
    </div>


<script>
  $('.routinesPill').click(function() {
    let tabId = $(this).attr('href')
    if($(tabId).attr('data-loaded') == ''){
     // $(tabId).attr('data-loaded', 'true')
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
    }
});
</script>