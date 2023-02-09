$(document).ready(function () {
    $(document).on("ready pjax:success", function () {
        $("#add_anothe_suspect").click(function (e) {
            e.preventDefault();

            var first_name = $("#temp_first_name");
            var middle_name = $("#temp_middle_name");
            var last_name = $("#temp_last_name");
            var age = $("#temp_age");
            var sex = $('input[name="temp_sex"]:checked');
            var reported_by = $('input[name="reported_by"]');

            var phone_number = $("#temp_phone_number");
            var national_id_number = $("#temp_national_id_number");
            var occuptaion = $("#temp_occuptaion");
            var parish = $("#temp_parish");
            var village = $("#temp_village");
            var national_id_number = $("#temp_national_id_number");
            var arrest_parish = $("#temp_arrest_parish");
            var arrest_village = $("#temp_arrest_village");
            var arrest_first_police_station = $("#temp_arrest_first_police_station");
            var arrest_current_police_station = $(
                "#temp_arrest_current_police_station"
            );
            var court_file_number = $("#temp_court_file_number");
            var court_date = $("#temp_court_date");
            var court_name = $("#temp_court_name");
            var prosecutor = $("#temp_prosecutor");
            var magistrate_name = $("#temp_magistrate_name");

            if (!isRequired(first_name, "Suspect's first name")) {
                return;
            }
            if (!isRequired(last_name, "Suspect's last name")) {
                return;
            }

          
            if (!isRequired(age, "Suspect's D.O.B")) {
                return;
            }

 
  
            $.ajax({
                type: "post",
                url: "/api/temp-data",
                data: {
                    'user_id' : reported_by.val(),
                    'magistrate_name' : magistrate_name.val(),
                    'prosecutor' : prosecutor.val(),
                    'court_name' : court_name.val(),
                    'court_date' : court_date.val(),
                    'court_file_number' : court_file_number.val(),
                    'arrest_current_police_station' : arrest_current_police_station.val(),
                    'arrest_first_police_station' : arrest_first_police_station.val(),
                    'arrest_village' : arrest_village.val(),
                    'village' : village.val(),
                    'arrest_parish' : arrest_parish.val(),
                    'parish' : parish.val(),
                    'national_id_number' : national_id_number.val(),
                    'occuptaion' : occuptaion.val(), 
                    'phone_number' : phone_number.val(),
                    'sex' : sex.val(),
                    'age' : age.val(),
                    'national_id_number' : national_id_number.val(),
                    'first_name' : first_name.val(),
                    'last_name' : last_name.val(),
                    'middle_name' : middle_name.val(),
                    'type' : 'case',  
                }, 
                success: function (r) { 
                    console.log(r);
                }, 
                always: function (r) { 
                    console.log(r); 
                }
            });
            
         

            $("#suspects_list").append(
                '<div class="row  border border-primary border-3 pt-4 pb-2 mb-4" style="background-color: #f2f2f2;">\
            <div class="col-md-10">\
                <h4 class=" p-0 m-0">Name</h4>\
                <p class=" p-0 m-0">' +
                    first_name.val() +
                    " " +
                    middle_name.val() +
                    " " +
                    last_name.val() +
                    '</p>\
                <h4 class=" p-0 m-0 mt-2">Sex</h4>\
                <p class=" p-0 m-0">' +
                    sex.val() +
                    '</p>\
                <h4 class=" p-0 m-0 mt-2">Date of birth</h4>\
                <p class=" p-0 m-0">' +
                    age.val() +
                    '</p>\
                <h4 class=" p-0 m-0 mt-2">Occupation</h4>\
                <p class=" p-0 m-0">' +
                    occuptaion.val() +
                    '</p>\
            </div>\
            <div class="col-md-2 text-danger text-center h4">\
                <i class="fa fa-trash" style="font-size: 3rem;"></i>\
                <br>\
                <b>Remove this suspect</b>\
            </div>\
        </div>'
            );


            reported_by.val("");
            magistrate_name.val("");
            prosecutor.val("");
            court_name.val("");
            court_date.val("");
            court_file_number.val("");
            arrest_current_police_station.val("");
            arrest_first_police_station.val("");
            arrest_village.val("");
            village.val("");
            arrest_parish.val("");
            parish.val(""),
            national_id_number.val();
            occuptaion.val("");
            reported_by.val("");
            phone_number.val("");
            sex.val("");
            age.val("");
            first_name.val("");
            last_name.val("");
            middle_name.val("");
            national_id_number.val("");
            
            $("#temp_first_name").focus();
            
 
        });

        function isRequired(field, name) {
            if (
                typeof field === "undefined" ||
                typeof field.val() === "undefined" ||
                field.val().trim().length < 2
            ) {
                alert(name + " is required.");
                field.focus();
                return false;
            }
            return true;
        }

        //suspect_first_name
    });
});
