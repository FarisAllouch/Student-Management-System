var ProfessorService = {
    reload_professors_datatable : function () {
        Utils.get_datatable(
            "tbl_professors",
            Constants.API_BASE_URL + "professors",
            [
                { data: "action"},
                { data: "FullName"},
                { data: "Email"},
                { data: "Password"}
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
    }
};