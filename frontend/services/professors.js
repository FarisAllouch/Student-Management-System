var ProfessorService = {
    reload_professors_datatable : function () {
        Utils.get_datatable(
            "tbl_professors",
            Constants.API_BASE_URL + "professors",
            [
                { data: "action"},
                { data: "FullName"},
                { data: "Email"},
            ]
        );
    },

    open_edit_professor_modal : function (professor_id) {
        RestClient.get(
            'professors/' + professor_id,
            function (data) {
                $('#addProfessorModal').modal("toggle");
                $("#addProfessorForm input[name='FullName']").val(data.FullName);
                $("#addProfessorForm input[name='Email']").val(data.Email);
                $("#addProfessorForm input[name='Password']").val(data.Password);
                $("#addProfessorForm input[name='id']").val(data.id);
            }
        )
    },

    delete_professor : function (professor_id) {
        if (confirm("Do you want to delete Professor with the id " + professor_id + "?") == true) {
            RestClient.delete(
                "professors/delete/" + professor_id,
                null,
                function(data) {
                    ProfessorService.reload_professors_datatable();
                }
            );
        }
    },

    render_assigned_courses: function(professor_id, data) {
        const assignedCourseList = $('#assignedCoursesList');
        assignedCourseList.empty();

        data.forEach(course => {
            assignedCourseList.append(`
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>${course.CourseName}</span>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-course-btn" 
                        data-course-id="${course.id}" 
                        data-professor-id="${professor_id}">
                        Remove
                    </button>
                </li>
            `);
        });
    },

    load_students_for_course: function(courseId, professorId) {
        RestClient.get(`courses/${courseId}/students`, function(data) {
            const studentSelect = $('#studentSelect');
            studentSelect.empty();
            
            data.forEach(student => {
                studentSelect.append(`<option value="${student.id}">${student.FirstName} ${student.LastName}</option>`);
            });

            $('#selectedCourseId').val(courseId);
            $('#selectedProfessorId').val(professorId);
            $('#assignGradeModal').modal('show');
        });
    },

    manage_courses: function(professor_id) {
        $('#selectedProfessorId').val(professor_id);
    
        RestClient.get('professor-courses/assigned/' + professor_id, function(data) {
            ProfessorService.render_assigned_courses(professor_id, data);
        });
    
        RestClient.get('professor-courses/unassigned/' + professor_id, function(data) {
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

    remove_course: function(courseId, professorId) {
        if (!courseId || !professorId) {
            toastr.error("Error: Missing course or professor ID");
            return;
        }
        
        if (confirm("Do you want to remove this course from the professor?")) {
            RestClient.delete(
                `professor-courses/delete/${professorId}/${courseId}`,
                null,
                function(data) {
                    toastr.success("Course removed successfully");
                    
                    // Reload the assigned courses list using the shared function
                    RestClient.get('professor-courses/assigned/' + professorId, function(data) {
                        ProfessorService.render_assigned_courses(professorId, data);
                    });
                }
            );
        }
    }
};