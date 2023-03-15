
$(document).ready(function(){

  $.ajax({
    url: "Notification/GetNotification",
    method: 'get',

    success: function(result){
      const obj = JSON.parse(result);
      //console.log(obj);
      if(obj.notifications!=''){
        $('#notification-count-header').html(obj.count);
        $('#notification-div-header').html(obj.notifications);
      }

    }
  });

});

var today=new Date();
$(document).ready(function(){
  window.setInterval(function(){
    get_new();
  }, 5000);
});

function get_new(){

  var date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
  var time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
  var dateTime = date+' '+time;
  var timezone=Intl.DateTimeFormat().resolvedOptions().timeZone;
//console.log(Intl.DateTimeFormat().resolvedOptions().timeZone)

  $.ajax({
    url: "Notification/GetNewNotification",
    method: 'post',
    data: {'Date': dateTime,'TimeZone': timezone},
    success: function(result){
      const obj = JSON.parse(result);
      //console.log(obj);
      if(obj.notifications!=''){
        $('#notification-count-header').html(obj.count);
        $('#notification-div-header').prepend(obj.notifications);

      }
      today = new Date();

    }
  });

}

//$('.timepicker').pickatime({});
