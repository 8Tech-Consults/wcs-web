$(document).ready(function () {
    $("#add_anothe_suspect").click(function (e) {
        e.preventDefault();
        var first_name = $("#first_name");
        var middle_name = $("#middle_name");
        var last_name = $("#last_name");
        var age = $("#age");
        var sex = $('input[name="sex"]:checked');


        var age = $("#phone_number");
        var age = $("#national_id_number");
        var occuptaion = $("#occuptaion");




        if (!isRequired(first_name, "Suspect's first name")) {
            return;
        }
        if (!isRequired(last_name, "Suspect's last name")) {
            return;
        }

        if (!isRequired(sex, "Suspect's sex")) {
            return;
        }
        if (!isRequired(age, "Suspect's D.O.B")) {
            return;
        }
         console.clear();
         $('#country').select2({
            // ...
            templateSelection: function (data, container) {
                console.log(data);
              return data.text;
            }
          });





       // alert("Good to go woth " + country);
        //$("#first_name").focus();
        //suspects_added.append("<h5>" + first_name + "</h5>");
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
