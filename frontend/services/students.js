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
    }
};