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

    manage_courses: function(student_id) {
        $('#selectedStudentId').val(student_id);
    
        RestClient.get('student-courses/assigned/' + student_id, function(data) {
            const assignedCourseList = $('#assignedCoursesList');
            assignedCourseList.empty();
    
            data.forEach(course => {
                assignedCourseList.append(`
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        ${course.CourseName}
                        <button class="btn btn-sm btn-outline-danger remove-course-btn" data-id="${course.id}">Remove</button>
                    </li>
                `);
            });
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
    }
};