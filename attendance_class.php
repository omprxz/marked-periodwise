
    <div id="tables-container" class="position-relative">
        <div class="alert alert-warning mt-3 mx-2 noAttendanceAlert" role="alert">
            No attendance in this class till now.
        </div>
    </div>


<script>
  $('.attendancePill').click(function() {
    let tabId = $(this).attr('href')
    if($(tabId).attr('data-loaded') == ''){
      $(tabId).attr('data-loaded', 'true')
    <?php if (isset($_GET['id'])) { ?>
        const classId = <?php echo json_encode($_GET['id']); ?>;
    <?php } ?>

    $.ajax({
        url: 'actions/get_attendances.php',
        type: 'GET',
        dataType: 'json',
        data: { classId: classId },
        success: function(data) {
            if (data.icon === 'error') {
                return;
            } else {
                $('.noAttendanceAlert').addClass('d-none');
            }
              tbno=1
            data.forEach(subject => {
                $('#tables-container').append(createTable(subject, tbno));
                tbno++
            });

           $('.search-bar').on('keyup', function() {
    let tbno = $(this).data('tbno');
    let tableClass = '.table-' + tbno;
    const value = $(this).val().toLowerCase();
    let $rows = $(tableClass).find('tbody tr');
    let found = false;

    $rows.hide().filter(function() {
        return $(this).text().toLowerCase().indexOf(value) > -1;
    }).show();

    $(tableClass).find('tbody tr:visible').each(function() {
        found = true;
        return false;
    });
    let colspan = $(tableClass).find('thead th').length;
    if (!found) {
        $(tableClass).find('.no-results-message').show();
        $(tableClass).find('.no-results-message td').attr('colspan', colspan)
    } else {
        $(tableClass).find('.no-results-message').hide();
    }
});
        },
        error: function() {
            alert('Failed to fetch data.');
        }
    });

    function createTable(subject, tbno) {
        let table = `<h3 class="mb-1 ms-1 mt-2 text-center">${subject.subjectName}</h3>
                      <div class="d-flex justify-content-center">
                            <input type="text" class="form-control mb-2 ms-1 mt-1 w-50 search-bar" data-tbno="${tbno}" placeholder="Search in ${subject.subjectName}">
                            <button class="btn btn-primary btn-sm download-csv ms-1 mt-1 mb-2 d-none">Download CSV</button>
                        </div>
                      <p class="my-1 ms-1 text-center">Total periods: ${subject.totalPeriods}</p>
                      <div class="table-responsive">
                        <table class="table table-bordered table-hover tableAtt table-${tbno}">`;
        table += `<thead><tr><th>Name</th><th>Roll</th><th>Semester</th><th>Present</th><th>Percentage</th>`;

        subject.attendance[0].attendance.forEach(att => { table += `<th class="text-center" colspan="${att.periods.length}">${att.date}</th>`; });

        table += `</tr></thead><tbody>
        <tr class="no-results-message text-center text-secondary" style="display: none;"><td>No Results.</td></tr>
        `;

        subject.attendance.forEach(student => {
            let attendanceDetails = '';
            student.attendance.forEach(att => {
                att.periods.forEach(session => {
                    attendanceDetails += `<td>${session.title} (${session.timing})</td>`;
                });
            });

            table += `<tr><td>${student.studentName}</td><td>${student.roll}</td><td>${student.semester}</td><td>${student.totalPresent}</td><td>${student.percentage}%</td>${attendanceDetails}</tr>`;
        });

        table += `</tbody></table></div> <hr class="border-primary">`;
        return table;
    }
    }
});
</script>