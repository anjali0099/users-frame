// Create
$(document).ready(function () {
	$("#edituser").hide();
	$("#adduser").show();
	
	$("#adduser").click(function (e) {
		e.preventDefault();
		if ($('#createmodal_form').valid()) {
			// alert('hi');
			var dataString = $("#createmodal_form").serialize();
			$.ajax({
				type: "POST",
				url: 'User/create',
				data: dataString,
				success: function (data) {
					// console.log(data);
					// alert(data);
					$("#createmodal_form")[0].reset();
					location.reload();
				}
			});
		}
	});
});

//clear field
$(document).ready(function () {
	$("#createmodalid").click(function () {
		$("#edituser").hide();
		$("#adduser").show();
		$("#createmodal_form")[0].reset();
		$('#editid').children('input').val('')
	});
});

// $(document).ready(function () {
// 	$("#createmodal_form").on('submit', function(e){
// 		e.preventDefault();

// 		if()
// 		add();

// 		if
// 		edit();
// 	});
// });

// $(document).ready(function () {
// 	$( "#adduser" ).click(function(e) {
// 		e.preventDefault();
// console.log($(this).attr('id'));

// 		console.log('add');
// 	});

// 	$( "#edituser" ).click(function(e) {
// 		e.preventDefault();
// 		console.log('edit');
// 	});
// });

// $(document).ready(function () {
// 	$(".user_submit_form").on('click', function (e) {
// 		e.preventDefault();

// 		var form_mode = $(this).attr('id');

// 		if (form_mode == 'adduser') {
// 			add_user();
// 		} else {
// 			edit_user();
// 		}
// 	});
// });

// function add_user() {
// 	var dataString = $("#createmodal_form").serialize();
// 	$.ajax({
// 		type: "POST",
// 		url: 'User/create',
// 		data: dataString,
// 		success: function (data) {
// 			// alert(data);
// 			$("#createmodal_form")[0].reset();
// 			// location.reload();
// 		}
// 	});
// }


// function edit_user() {
// 	var dataString = $("#createmodal_form").serialize();
// 	$.ajax({
// 		type: "POST",
// 		url: 'User/edit',
// 		data: dataString,
// 		success: function (data) {
// 			// alert(data);
// 			// alert('User Updated');
// 			$("#createmodal_form")[0].reset();
// 			location.reload(true);
// 		}
// 	});
// }

// Edit function
function populate_edit(userId, firstname, lastname, email, address, companyname, companyaddress) {

	$("#edituser").show();
	$("#adduser").hide();
	$("#hidepass").hide();

	$("#editid").val(userId);
	$("#firstname").val(firstname);
	$("#lastname").val(lastname);
	$("#email").val(email);
	$("#address").val(address);
	$("#companyname").val(companyname);
	$("#companyaddress").val(companyaddress);
}


// edit
$(document).ready(function () {
	$("#edituser").click(function (e) {
		// debugger;
		e.preventDefault();
		var dataString = $("#createmodal_form").serialize();
		$.ajax({
			type: "POST",
			url: 'User/edit',
			data: dataString,
			success: function (data) {
				// alert(data);
				// alert('User Updated');
				$("#createmodal_form")[0].reset();
				location.reload(true);
			}
		});
	});
});

// $(document).ready(function () {
// $("#edituser").on('click', function(e){
// 	e.preventDefault();
// 	alert('here');
// });
// });


// delete
function delete_user(id) {
	// debugger;
	if (confirm("Are you sure you want to delete this user?")) {
		$.ajax({
			type: "post",
			url: 'User/delete',
			data: { 'id': id },
			success: function (data) {
				// console.log(data);
				// alert(data);
				location.reload(true);
			}
		});
	}
}


//pagination
$(document).ready(function () {
	var table = '#mytable'
	$('#maxRows').on('change', function () {
		$('.pagination').html('')
		var trnum = 0
		var maxRows = parseInt($(this).val())
		var totalRows = $(table + ' tbody tr').length
		$(table + ' tr:gt(0)').each(function () {
			trnum++
			if (trnum > maxRows) {
				$(this).hide()
			}
			if (trnum <= maxRows) {
				$(this).show()
			}
		});
		if (totalRows > maxRows) {
			var pagenum = Math.ceil(totalRows / maxRows)
			for (var i = 1; i <= pagenum;) {
				$('.pagination').append('<li data-page="' + i + '">\<span>' + i++ + '<span class="sr-only">(current)</span></span>\</li>').show()
			}
		}
		$('.pagination li').addClass('active page-link')
		$('.pagination li').on('click', function () {
			var pageNum = $(this).attr('data-page')
			var trIndex = 0;
			$('.pagination li').removeClass('active')
			$(this).addClass('active')
			$(table + ' tr:gt(0)').each(function () {
				trIndex++
				if (trIndex > (maxRows * pageNum) || trIndex <= ((maxRows * pageNum) - maxRows)) {
					$(this).hide()
				} else {
					$(this).show()
				}
			});
		});
	});
	// $(function () {
	// 	$('table tr:eq(0)').prepend('<th>SNo</th>')
	// 	var SNo = 0;
	// 	$('table tr:gt(0)').each(function () {
	// 		SNo++
	// 		$(this).prepend('<td>' + SNo + '</td>')
	// 	});
	// });
});


//toast fadeout
$(document).ready(function () {
	$(".msg-alert").fadeTo(2000, 500).slideUp(500, function () {
		$("msg-alert").slideUp(500);
	});
});

// // toastr
// $(document).ready(function() {

//     // toastr.info('Page Loaded!');
//     $('#test').click(function() {
//        // show when the button is clicked
//        toastr.success('success');

//     });

//   // $("#myBtn").click(function(){
//   //   $('.toast').toast('show');
//   // });
// });


// Register
$(document).ready(function () {
	$('#register_form').on('submit', function (e) {
		e.preventDefault();
		if ($('#register_form').valid()) {
			var dataString = $("#register_form").serialize();
			$.ajax({
				type: "POST",
				url: 'Log/register',
				data: dataString,
				success: function (data) {
					// alert(data);
					$("#register_form")[0].reset();
					location.reload();
				}
			});
		}
	});
});

//delete with checkbox
$(document).ready(function () {
	$("#selectall").change(function () {
		$("input:checkbox").prop('checked', $(this).prop("checked"));
		
		if ($('.selectdel').is(':checked')) {
			$('#deleteallbtn').removeAttr('disabled');
		} else {
			
			$('#deleteallbtn').attr('disabled', 'disabled');
		}
	});
});
   
// delete btn enable/disable
$(function () {
	$('.selectdel').click(function () {
		if ($('.selectdel').is(':checked')) {
			$('#deleteallbtn').removeAttr('disabled');
		} else {
			$('#deleteallbtn').attr('disabled', 'disabled');
		}
	});
});

//change password
$(document).ready(function () {
	$("#userpass").hide();
	$("#loguserpass").show();
	$("#loguserpass").on('click', function (e) {
		e.preventDefault();
		if ($('#changepassmodal_form').valid()) {
			var dataString = $("#changepassmodal_form").serialize();
			$.ajax({
				type: "POST",
				url: 'User/change_password',
				data: dataString,
				success: function (data) {
					// alert(data);
					$("#changepassmodal_form")[0].reset();
					location.reload();
				}
			});
		}
	});
});

// change pass of individual users
function change_pass(userId) {
	$("#userpass").show();
	$("#loguserpass").hide();
	$("#userpass").on('click', function (e) {
		e.preventDefault();
		if ($('#changepassmodal_form').valid()) {
			var oldpassword = $("#oldpassword").val();
			var newpassword = $("#newpassword").val();
			var cpassword = $("#cpassword").val();
			$.ajax({
				type: "post",
				url: 'User/change_user_pass',
				data: { 'userId': userId, 'oldpassword': oldpassword, 'newpassword': newpassword, 'cpassword': cpassword },
				success: function (data) {
					// alert(data);
					location.reload(true);
				}
			});
		}
	});
}


