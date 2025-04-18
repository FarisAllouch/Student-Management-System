var CourseService = {
    reload_courses_datatable : function () {
        Utils.get_datatable(
            "tbl_courses",
            Constants.API_BASE_URL + "get_courses.php",
            [
                { data: "action"},
                { data: "CourseName"},
                { data: "CourseCode"}
            ]
        );
    },

    open_edit_course_modal : function (course_id) {
        RestClient.get(
            'get_course.php?id=' + course_id,
            function (data) {
                $('#addCourseModal').modal("toggle");
                $("#addCourseForm input[name='CourseCode']").val(data.CourseCode);
                $("#addCourseForm input[name='CourseName']").val(data.CourseName);
            }
        )
    },

    open_add_course_modal : function () {
        $('#addCourseModal').modal("toggle");
        
        $("#addCourseForm input[name='id']").val('');
    
        $("#addCourseForm input[name='CourseCode']").val('');
        $("#addCourseForm input[name='CourseName']").val('');
    },

    delete_course : function (course_id) {
        if (confirm("Do you want to delete course with the id " + course_id + "?") == true) {
            RestClient.delete(
                "delete_course.php?id=" + course_id,
                null,
                function(data) {
                    CourseService.reload_courses_datatable();
                }
            );
        }
    }
};