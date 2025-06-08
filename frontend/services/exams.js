var ExamService = {

    reload_exams_datatable : function (course_id) {
        Utils.get_datatable(
            "tbl_exams",
            Constants.get_api_base_url() + "exams?CourseId=" + course_id,
            [
                { data: "ExamName"},
                { data: "Weight"},
                { data: "action"}
            ]
        );
    },

    manage_exams : function (course_id) {
        $("#id").val(course_id);
        $("#examCourseId").val(course_id);
        $('#manageExamsModal').modal("toggle");

        ExamService.reload_exams_datatable(course_id);
    },

    delete_exam : function (exam_id) {
        if (confirm("Do you want to delete exam with the id " + exam_id + "?") == true) {
            RestClient.delete(
                "exams/delete/" + exam_id,
                null,
                function(data) {
                    const course_id = $("#id").val()
                    ExamService.reload_exams_datatable(course_id);
                }
            );
        }
    } ,

    open_edit_exam_modal : function (exam_id) {
        RestClient.get(
            'exams/' + exam_id,
            function (data) {
                $('#addExamModal').modal("toggle");
                $("#addExamForm input[name='ExamId']").val(exam_id);
                $("#addExamForm input[name='ExamName']").val(data.ExamName);
                $("#addExamForm input[name='Weight']").val(data.Weight);
            }
        )
    }
}