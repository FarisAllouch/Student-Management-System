var CourseService = {
    
    reload_courses_datatable : function () {
        Utils.get_datatable(
            "tbl_courses",
            Constants.API_BASE_URL + "courses",
            [
                { data: "action"},
                { data: "CourseName"},
                { data: "CourseCode"}
            ]
        );
    },

    open_edit_course_modal : function (course_id) {
        RestClient.get(
            'courses/' + course_id,
            function (data) {
                $('#addCourseModal').modal("toggle");
                $("#addCourseForm input[name='CourseCode']").val(data.CourseCode);
                $("#addCourseForm input[name='CourseName']").val(data.CourseName);
                $("#addCourseForm input[name='id']").val(data.id);
            }
        )
    },

    delete_course : function (course_id) {
        if (confirm("Do you want to delete course with the id " + course_id + "?") == true) {
            RestClient.delete(
                "courses/delete/" + course_id,
                null,
                function(data) {
                    CourseService.reload_courses_datatable();
                }
            );
        }
    },

    get_enrolledStudents : function (course_id) {
        RestClient.get(
            'courses/' + course_id + '/students',
            function(data) {
                $('#enrolled-students-list').empty();

                if (data && data.length > 0) {
                data.forEach(student => {
                    const totalPoints = parseFloat(student.TotalPoints) || 0;
                    const totalWeight = parseFloat(student.TotalWeight) || 0;
                    const passed = totalPoints >= 55;
                    const passedText = passed ?
                    `<span class="badge bg-success">Passed</span>` :
                    `<span class="badge bg-danger">Not Passed</span>`;

                    if (student.Grades) {
                    gradesHtml = student.Grades.split(', ').map(g => `<span class="badge bg-primary me-1">${g}</span>`).join('');
                    } else {
                    gradesHtml = `<span class="text-muted">No grades</span>`;
                    }

                    $('#enrolled-students-list').append(`
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">${student.FirstName} ${student.LastName} ${passedText}</h5>
                            <p class="card-text mb-1"><i class="bi bi-envelope"></i> ${student.Email}</p>
                            <div><small>Total Points: ${totalPoints.toFixed(1)} out of ${totalWeight}</small></div>
                        </div>
                        <div>
                            <!-- Mjesto za dugmad ili dodatne akcije ako treba -->
                        </div>
                        </div>
                    </div>
                    `);
                });
                } else {
                $('#enrolled-students-list').append(`
                    <p class="text-muted">No students enrolled.</p>
                `);
                }

                $('#enrolled-students-modal').modal('show');
            }
        )
    }
};