var StudentService = {
    reload_students_datatable : function () {
        Utils.get_datatable(
            "tbl_students",
            Constants.API_BASE_URL + "students",
            [
                { data: "action"},
                { data: "FirstName"},
                { data: "LastName"},
                { data: "Email"},
                { data: "Password"}
            ]
        );
    },

    open_edit_student_modal : function (student_id) {
        RestClient.get(
            'students/' + student_id,
            function (data) {
                $('#addStudentModal').modal("toggle");
                $("#addStudentForm input[name='FirstName']").val(data.FirstName);
                $("#addStudentForm input[name='LastName']").val(data.LastName);
                $("#addStudentForm input[name='Email']").val(data.Email);
                $("#addStudentForm input[name='Password']").val(data.Password);
                $("#addStudentForm input[name='id']").val(data.id);
            }
        )
    },

    delete_student : function (student_id) {
        if (confirm("Do you want to delete student with the id " + student_id + "?") == true) {
            RestClient.delete(
                "students/delete/" + student_id,
                null,
                function(data) {
                    StudentService.reload_students_datatable();
                }
            );
        }
    },

    render_assigned_courses: function(student_id, data) {
        const assignedCourseList = $('#assignedCoursesList');
        assignedCourseList.empty();

        data.forEach(course => {
            assignedCourseList.append(`
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>${course.CourseName}</span>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-course-btn" 
                        data-course-id="${course.id}" 
                        data-student-id="${student_id}">
                        Remove
                    </button>
                </li>
            `);
        });
    },

    manage_courses: function(student_id) {
        $('#selectedStudentId').val(student_id);
    
        RestClient.get('student-courses/assigned/' + student_id, function(data) {
            StudentService.render_assigned_courses(student_id, data);
        });
    
        RestClient.get('student-courses/unassigned/' + student_id, function(data) {
            const select = $('#unassignedCoursesSelect');
            select.empty();
    
            if (data.length === 0) {
                select.append('<option disabled> No unassigned courses available!</option>');
            } else {
                data.forEach(course => {
                    select.append(`<option value="${course.id}">${course.CourseName}</option>`);
                });
            }
    
            $('#manageCoursesModal').modal("toggle");
        });
    },

    load_course_exams: function(courseId) {
        const studentId = $('#selectedStudentId').val();
        RestClient.get(`exams/?CourseId=${courseId}&studentId=${studentId}`, function(response) {
            const examSelect = $('#examSelect');
            examSelect.empty();
            
            response.data.forEach(exam => {
                examSelect.append(`<option value="${exam.ExamId}">${exam.ExamName}</option>`);
            });
        });
    },

    load_student_grades: function(studentId, courseId) {
        RestClient.get(`grades/${studentId}/${courseId}`, function(data) {
            const gradesList = $('#gradesList');
            gradesList.empty();
            
            if (data && Array.isArray(data)) {
                let totalPoints = 0;
                let totalWeight = 0;
                
                data.forEach(grade => {
                    const pointsEarned = (grade.Grade * grade.Weight) / 100;
                    totalPoints += pointsEarned;
                    totalWeight += parseFloat(grade.Weight);
                    
                    gradesList.append(`
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${grade.ExamName}</strong><br>
                                <small>Grade: ${grade.Grade}% (${pointsEarned.toFixed(1)} points out of ${grade.Weight})</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-grade-btn" 
                                data-grade-id="${grade.id}">
                                Remove
                            </button>
                        </li>
                    `);
                });

                // Calculate total grade percentage
                if (totalWeight > 0) {
                    const passStatus = totalPoints >= 55 ? 'PASSED' : 'FAILED';
                    const statusClass = totalPoints >= 55 ? 'bg-success' : 'bg-danger';
                    
                    gradesList.append(`
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Total Grade:</strong>
                            <div>
                                <span class="badge ${statusClass}">${passStatus}</span><br>
                                <small>Total Points: ${totalPoints.toFixed(1)} out of ${totalWeight}</small>
                            </div>
                        </li>
                    `);
                }
            } else {
                console.error("Data is not an array", data);
            }
        });
    },

    remove_grade: function(gradeId) {
        if (confirm("Do you want to remove this grade?")) {
            RestClient.delete(`grades/delete/${gradeId}`, null, function() {
                toastr.success("Grade removed successfully");
                // Reload grades list
                const studentId = $('#selectedStudentId').val();
                const courseId = $('#courseSelect').val();
                StudentService.load_student_grades(studentId, courseId);
            });
        }
    }
};