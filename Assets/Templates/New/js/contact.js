$(document).ready(function () {
	$("#edit_contact").hide();
	$("#add_contact").show();

    $("#add_contact").click(function (e) {
        e.preventDefault();
        if($("#contactmodal_form").valid())
        {
			// var base_url = Contact/create;
            var dataString = $("#contactmodal_form").serialize();
			$.ajax({
				type: "POST",
				url: 'Contact/create',
				data: dataString,
				success: function (data) {
					$("#contactmodal_form")[0].reset();
					location.reload();
				}
			});
        }
    });
});

$(document).ready(function () {
	$("#contactmodal_form").validate({
		rules:
		{
			email: "required",
			phone: "required",

			email: 
			{
				required: true,
				email: true,
			},
			phone: 
			{
				required: true,
				minlength: 10,
				maxlength: 10,
				number: true,
			},
		},
		messages:
		{
			email: {
				required: "Please enter your email",
				minlength: "Please enter a valid email address",

			},
			phone: {
				required: "Please provide a phone number",
				minlength: "Phone number must be min 10 characters long",
				maxlength: "Phone number must not be more than 10 characters long"
			},
		},
		submitHandler: function (contactmodal_form) {
			contactmodal_form.submit();
		}

	});
});

//clear field
$(document).ready(function () {
	$("#contactmodalid").click(function () {
		$("#edit_contact").hide();
		$("#add_contact").show();
		$("#contactmodal_form")[0].reset();
		$('#editid').children('input').val('')
	});
});


function populate_edit_contact(contact_id, phone, email, address)
{
	$("#edit_contact").show();
	$("#add_contact").hide();

	$("#editid").val(contact_id);
	$("#phone").val(phone);
	$("#email").val(email);
	$("#address").val(address);
}

//edit
$(document).ready(function () {
    $("#edit_contact").click(function (e) {
        e.preventDefault();
        if($("#contactmodal_form").valid())
        {
            var dataString = $("#contactmodal_form").serialize();
			$.ajax({
				type: "POST",
				url: 'Contact/edit',
				data: dataString,
				success: function (data) {
					$("#contactmodal_form")[0].reset();
					location.reload();
				}
			});
        }
    });
});

// delete
function delete_contact(contact_id) {
	if (confirm("Are you sure you want to delete this contact?")) {
		$.ajax({
			type: "post",
			url: 'Contact/delete',
			data: { 'contact_id': contact_id },
			success: function (data) {
				location.reload(true);
			}
		});
	}
}

	