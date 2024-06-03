
            <div class="text-center mt-2">
                <button type="button" id="addSubjectBtn" class="btn btn-outline-primary"><i class="fad fa-plus"></i> Add Subject</button>
            </div>
            <ul class="list-group mt-4 mx-2 subjects-list">
                <!-- Subjects list will be loaded dynamically -->
            </ul>


<script>
$('.subjectsPill').click(function() {
    let tabId = $(this).attr('href')
    if($(tabId).attr('data-loaded') == ''){
    //  $(tabId).attr('data-loaded', 'true')
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
    }
});
</script>